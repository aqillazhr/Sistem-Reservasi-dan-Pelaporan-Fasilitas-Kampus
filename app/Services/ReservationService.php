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
    private ?int $systemUserIdCache = null;

    public function create(User $user, array $data): Reservation
    {
        // Beresin dulu pending milik user ini yang sudah lewat waktu bookingnya
        // (belum sempat diproses petugas) supaya tidak ikut mengunci kuota
        // max_pending_per_user selamanya. Transaction terpisah dari transaction
        // pengajuan baru di bawah, supaya tetap konsisten walau create() gagal.
        $this->autoCancelExpired($user->id);

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
        $tooSoonBefore = now()->addHours((int) config('reservation.min_advance_hours'));
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

                // Belum lewat & belum terisi, tapi kurang dari batas H-1 (min_advance_hours)
                // dari sekarang -> tetap tidak boleh diajukan (lihat StoreReservationRequest).
                if ($state === 'free' && $cursor->lt($tooSoonBefore)) {
                    $state = 'toosoon';
                }
            }

            $board[] = ['start' => $start, 'end' => $end, 'state' => $state];
            $cursor = $next;
        }

        return $board;
    }

    // ------------------------------------------------------------
    // Auto-cancel reservasi pending yang sudah lewat WAKTU BOOKING-nya
    // (bukan waktu pengajuannya) tapi belum sempat diproses petugas.
    // Contoh: booking 12.00-12.30 tanggal 26, sekarang sudah jam 13.00
    // tanggal 26 dan status masih 'pending' -> otomatis jadi 'cancelled',
    // dengan catatan jelas dan pelaku "Sistem" (bukan log anonim).
    //
    // TIDAK pakai cron/scheduler (riskan kalau lupa dijalankan pas demo) —
    // dipanggil "on the fly" di titik-titik yang relevan: sebelum hitung
    // kuota pending (di atas), sebelum tampilkan riwayat/detail reservasi
    // pengguna, dan sebelum tampilkan antrian petugas.
    // ------------------------------------------------------------
    public function autoCancelExpired(?int $userId = null): int
    {
        return DB::transaction(function () use ($userId) {
            $now = now();

            $query = Reservation::where('status', 'pending')
                ->where(fn ($q) => $q
                    ->whereDate('reservation_date', '<', $now->toDateString())
                    ->orWhere(fn ($q2) => $q2
                        ->whereDate('reservation_date', $now->toDateString())
                        ->where('end_time', '<=', $now->format('H:i:s'))
                    )
                );

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $ids = $query->lockForUpdate()->pluck('id');

            if ($ids->isEmpty()) {
                return 0;
            }

            $reason = 'Dibatalkan otomatis oleh sistem karena sudah lewat waktu booking yang diminta.';
            $systemUserId = $this->systemUserId();

            Reservation::whereIn('id', $ids)->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            StatusLog::insert($ids->map(fn ($id) => [
                'reservation_id' => $id,
                'changed_by_user_id' => $systemUserId,
                'old_status' => 'pending',
                'new_status' => 'cancelled',
                'note' => $reason,
                'created_at' => $now,
            ])->all());

            return $ids->count();
        }, 3);
    }

    private function systemUserId(): int
    {
        return $this->systemUserIdCache ??= User::where('email', config('reservation.system_account_email'))
            ->value('id');
    }

    // ------------------------------------------------------------
    // PETUGAS — approve/reject/batal darurat. Sama-sama transaction + lock,
    // supaya tidak ada state reservasi yang berubah tanpa audit log, dan
    // tidak ada perubahan "nyangkut" separuh kalau salah satu langkah gagal.
    // ------------------------------------------------------------

    public function approveByOfficer(User $officer, int $reservationId): Reservation
    {
        return DB::transaction(function () use ($officer, $reservationId) {
            $reservation = Reservation::whereKey($reservationId)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== 'pending') {
                throw ValidationException::withMessages([
                    'reservation' => 'Reservasi ini sudah diproses sebelumnya.',
                ]);
            }

            if (now()->gt($reservation->endsAt())) {
                throw ValidationException::withMessages([
                    'reservation' => 'Reservasi ini sudah lewat waktu booking-nya dan otomatis dibatalkan sistem, tidak bisa disetujui lagi.',
                ]);
            }

            // Jaga-jaga: di jalur normal (semua reservasi lewat store(), yang
            // sudah lock fasilitas + hasConflict() sebelum insert), dua
            // reservasi pending/approved yang bentrok TIDAK BISA terjadi.
            // Cek ini murah dan cuma jaring pengaman untuk data yang masuk
            // di luar jalur normal (mis. lewat Tinker/seeder demo).
            $conflict = $this->hasConflict(
                $reservation->facility_id,
                $reservation->reservation_date->toDateString(),
                substr($reservation->start_time, 0, 5),
                substr($reservation->end_time, 0, 5),
                $reservation->id
            );

            if ($conflict) {
                throw ValidationException::withMessages([
                    'reservation' => 'Ada reservasi lain yang sudah disetujui pada slot yang sama. Tolak salah satunya dulu.',
                ]);
            }

            $reservation->update(['status' => 'approved']);

            StatusLog::create([
                'reservation_id' => $reservation->id,
                'changed_by_user_id' => $officer->id,
                'old_status' => 'pending',
                'new_status' => 'approved',
                'note' => 'Disetujui petugas',
            ]);

            return $reservation;
        }, 3);
    }

    public function rejectByOfficer(User $officer, int $reservationId, string $note): Reservation
    {
        return DB::transaction(function () use ($officer, $reservationId, $note) {
            $reservation = Reservation::whereKey($reservationId)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== 'pending') {
                throw ValidationException::withMessages([
                    'reservation' => 'Reservasi ini sudah diproses sebelumnya.',
                ]);
            }

            // Simetris dengan approveByOfficer(): reservasi yang waktu booking-nya
            // sudah lewat biar konsisten cuma bisa "diproses" lewat autoCancelExpired()
            // (jadi cancelled), bukan ditolak manual lagi.
            if (now()->gt($reservation->endsAt())) {
                throw ValidationException::withMessages([
                    'reservation' => 'Reservasi ini sudah lewat waktu booking-nya dan otomatis dibatalkan sistem, tidak bisa ditolak lagi.',
                ]);
            }

            $reservation->update(['status' => 'rejected']);

            // Alasan penolakan disimpan di status_logs.note (BUKAN cancellation_reason,
            // kolom itu khusus pembatalan — lihat README-database.pdf).
            StatusLog::create([
                'reservation_id' => $reservation->id,
                'changed_by_user_id' => $officer->id,
                'old_status' => 'pending',
                'new_status' => 'rejected',
                'note' => $note,
            ]);

            return $reservation; // slot otomatis kembali free: occupyingSlot() hanya pending+approved
        }, 3);
    }

    public function cancelByOfficer(User $officer, int $reservationId, string $reason): Reservation
    {
        return DB::transaction(function () use ($officer, $reservationId, $reason) {
            $reservation = Reservation::whereKey($reservationId)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== 'approved') {
                throw ValidationException::withMessages([
                    'reservation' => 'Hanya reservasi berstatus aktif yang bisa dibatalkan petugas.',
                ]);
            }

            $reservation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ]);

            StatusLog::create([
                'reservation_id' => $reservation->id,
                'changed_by_user_id' => $officer->id,
                'old_status' => 'approved',
                'new_status' => 'cancelled',
                'note' => $reason,
            ]);

            return $reservation;
        }, 3);
    }
}
