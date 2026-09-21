<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use Illuminate\Http\Request;

/**
 * Modul: Reservasi (Orang 3)
 * Modul database: Reservations
 *
 * TODO Orang 3:
 * - store(): validasi SERVER slot 30 menit + jam operasional 07.00-20.00,
 *   cek konflik (slot occupied jika ada reservasi pending/approved yang overlap),
 *   bungkus dalam DB transaction + re-check availability sebelum INSERT
 *   (lihat README-database.pdf, Catatan Implementasi poin 3)
 * - index() untuk pengguna: riwayat + status reservasi sendiri
 * - cancel(): pengguna batalkan reservasi sendiri sebelum batas waktu tertentu
 * - approve()/reject() untuk petugas, cegah approve yang bentrok jadwal
 * - petugasCancel(): petugas batalkan reservasi approved dalam kondisi mendesak,
 *   wajib isi cancellation_reason
 */
class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string'],
        ]);

        $this->assertWithinOperationalHours($validated['start_time'], $validated['end_time']);
        $this->assertHalfHourSlot($validated['start_time'], $validated['end_time']);

        // TODO: bungkus di DB::transaction(), re-check overlap pakai
        // idx_reservations_conflict_check (facility_id, reservation_date, status)
        // sebelum insert, supaya aman dari race condition dua submit bersamaan.

        $reservation = Reservation::create($validated + [
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        return redirect()->route('reservations.show', $reservation)->with('status', 'Reservasi diajukan, menunggu persetujuan petugas.');
    }

    public function cancel(Reservation $reservation, Request $request)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        // TODO: cek batas waktu pembatalan sebelum diizinkan.

        $reservation->update(['status' => 'cancelled']);

        return back()->with('status', 'Reservasi dibatalkan.');
    }

    public function approve(Reservation $reservation)
    {
        // TODO: cek ulang tidak ada reservasi approved lain yang overlap
        // pada facility_id + reservation_date + rentang waktu yang sama.

        $reservation->update(['status' => 'approved']);

        return back()->with('status', 'Reservasi disetujui.');
    }

    public function reject(Reservation $reservation)
    {
        $reservation->update(['status' => 'rejected']);

        return back()->with('status', 'Reservasi ditolak. Slot otomatis tersedia kembali.');
    }

    public function petugasCancel(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string'],
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return back()->with('status', 'Reservasi dibatalkan petugas.');
    }

    private function assertWithinOperationalHours(string $start, string $end): void
    {
        abort_if($start < '07:00' || $end > '20:00', 422, 'Reservasi harus dalam jam operasional 07.00-20.00.');
    }

    private function assertHalfHourSlot(string $start, string $end): void
    {
        foreach ([$start, $end] as $time) {
            $minute = (int) substr($time, 3, 2);
            abort_if(! in_array($minute, [0, 30], true), 422, 'Slot waktu harus kelipatan 30 menit.');
        }
    }
}
