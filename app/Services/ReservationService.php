<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\StatusLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Logika proses reservasi (Orang 3). Semua operasi TULIS ke DB dibungkus
 * DB::transaction; semua query lewat Eloquent/Query Builder (parameter
 * binding PDO = prepared statement, tidak ada string SQL yang digabung).
 *
 * Urutan lock (selalu sama supaya tidak deadlock): users -> facilities -> reservations.
 */
class ReservationService
{
    public function create(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            // 1) Serialkan pengajuan milik user yang sama (cek batas pending aman dari race).
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();

            // 2) Serialkan pengajuan ke fasilitas yang sama (cek bentrok aman dari race).
            //    Global scope SoftDeletes otomatis mengecualikan fasilitas terhapus.
            $facility = Facility::whereKey($data['facility_id'])->lockForUpdate()->first();

            if (! $facility || $facility->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'facility_id' => 'Fasilitas sedang tidak dapat direservasi.',
                ]);
            }

            $pending = Reservation::where('user_id', $user->id)->where('status', 'pending')->count();
            $maxPending = (int) config('reservation.max_pending_per_user');

            if ($pending >= $maxPending) {
                throw ValidationException::withMessages([
                    'facility_id' => "Kamu sudah punya {$maxPending} reservasi yang menunggu persetujuan. Tunggu diproses atau batalkan salah satunya.",
                ]);
            }

            // 3) Re-check availability TEPAT sebelum INSERT (di dalam transaction + lock).
            if ($this->hasConflict($facility->id, $data['reservation_date'], $data['start_time'], $data['end_time'])) {
                throw ValidationException::withMessages([
                    'start_time' => 'Slot itu sudah terisi atau sedang menunggu konfirmasi petugas. Pilih waktu lain.',
                ]);
            }

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'facility_id' => $facility->id,
                'reservation_date' => $data['reservation_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'purpose' => $data['purpose'],
                'status' => 'pending',
            ]);

            StatusLog::create([
                'reservation_id' => $reservation->id,
                'changed_by_user_id' => $user->id,
                'old_status' => '-',
                'new_status' => 'pending',
                'note' => 'Reservasi diajukan',
            ]);

            return $reservation;
        }, 3); // ulangi otomatis (maks 3x) kalau kena deadlock
    }

    public function cancelByOwner(User $user, int $reservationId): Reservation
    {
        return DB::transaction(function () use ($user, $reservationId) {
            // Lock baris reservasi: mencegah cancel pengguna & approve/reject petugas saling menimpa.
            $reservation = Reservation::whereKey($reservationId)->lockForUpdate()->firstOrFail();

            abort_unless($reservation->user_id === $user->id, 403);

            if (! in_array($reservation->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages([
                    'reservation' => 'Reservasi ini sudah tidak bisa dibatalkan.',
                ]);
            }

            if (now()->greaterThan($reservation->cancelDeadline())) {
                $hours = (int) config('reservation.cancel_min_hours');

                throw ValidationException::withMessages([
                    'reservation' => "Pembatalan hanya bisa sampai {$hours} jam sebelum jam mulai.",
                ]);
            }

            $old = $reservation->status;
            $reservation->update(['status' => 'cancelled']);

            StatusLog::create([
                'reservation_id' => $reservation->id,
                'changed_by_user_id' => $user->id,
                'old_status' => $old,
                'new_status' => 'cancelled',
                'note' => 'Dibatalkan oleh pengguna',
            ]);

            return $reservation;
        }, 3);
    }

    /**
     * Dipakai store() DAN approve() petugas (satu sumber kebenaran untuk aturan bentrok).
     */
    public function hasConflict(int $facilityId, string $date, string $start, string $end, ?int $ignoreReservationId = null): bool
    {
        return Reservation::query()
            ->where('facility_id', $facilityId)
            ->where('reservation_date', $date)
            ->occupyingSlot()
            ->overlapping($start, $end)
            ->when($ignoreReservationId, fn ($q) => $q->where('id', '!=', $ignoreReservationId))
            ->exists();
    }

    /**
     * Kontrak data mentah slot terpakai untuk Orang 2 (kalender availability) & Orang 4 (okupansi).
     * SENGAJA tanpa nama pemohon / tujuan penggunaan (user story 1).
     *
     * @return Collection<int, Reservation> berisi: reservation_date, start_time, end_time, status
     */
    public function occupiedSlots(int $facilityId, string $from, int $days): Collection
    {
        $days = max(1, min($days, 7));
        $to = Carbon::parse($from)->addDays($days - 1)->toDateString();

        return Reservation::query()
            ->select(['reservation_date', 'start_time', 'end_time', 'status'])
            ->where('facility_id', $facilityId)
            ->whereBetween('reservation_date', [$from, $to])
            ->occupyingSlot()
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get();
    }
    /**
     * Papan slot 30 menit untuk satu fasilitas & tanggal (dipakai form ajukan reservasi).
     * state: free | pending (menunggu konfirmasi) | approved (terisi) | past (sudah lewat)
     *
     * @return array<int, array{start:string,end:string,state:string}>
     */
    public function slotBoard(int $facilityId, string $date): array
    {
        $tz = config('app.timezone');
        $step = (int) config('reservation.slot_minutes');
        $cursor = Carbon::parse($date.' '.config('reservation.open_time'), $tz);
        $close = Carbon::parse($date.' '.config('reservation.close_time'), $tz);
        $occupied = $this->occupiedSlots($facilityId, $date, 1);
        $board = [];

        while ($cursor->lt($close)) {
            $next = $cursor->copy()->addMinutes($step);
            $start = $cursor->format('H:i');
            $end = $next->format('H:i');
            $state = 'free';

            if ($cursor->lessThanOrEqualTo(now())) {
                $state = 'past';
            } else {
                foreach ($occupied as $o) {
                    if (substr($o->start_time, 0, 5) < $end && substr($o->end_time, 0, 5) > $start) {
                        $state = $o->status === 'approved' ? 'approved' : 'pending';
                        break;
                    }
                }
            }

            $board[] = ['start' => $start, 'end' => $end, 'state' => $state];
            $cursor = $next;
        }

        return $board;
    }
}
