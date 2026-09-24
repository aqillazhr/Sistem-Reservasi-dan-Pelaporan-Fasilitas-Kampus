<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::whereIn('role', ['petugas', 'admin'])
            ->where('status', 'verified')
            ->first();

        // ─── Reservasi mendatang ───────────────────────────────────────────────
        // Pending (menunggu persetujuan)
        Reservation::factory()->pending()->count(10)->create();

        // Approved (sudah disetujui, akan datang)
        Reservation::factory()->approved()->count(8)->create()
            ->each(function (Reservation $r) use ($petugas) {
                StatusLog::create([
                    'reservation_id'     => $r->id,
                    'report_id'          => null,
                    'facility_id'        => null,
                    'changed_by_user_id' => $petugas->id,
                    'old_status'         => 'pending',
                    'new_status'         => 'approved',
                    'note'               => 'Reservasi disetujui.',
                ]);
            });

        // ─── Reservasi lampau ─────────────────────────────────────────────────
        // Approved + sudah lewat (riwayat sukses)
        Reservation::factory()->approved()->past()->count(15)->create()
            ->each(function (Reservation $r) use ($petugas) {
                StatusLog::create([
                    'reservation_id'     => $r->id,
                    'report_id'          => null,
                    'facility_id'        => null,
                    'changed_by_user_id' => $petugas->id,
                    'old_status'         => 'pending',
                    'new_status'         => 'approved',
                    'note'               => null,
                ]);
            });

        // Rejected
        Reservation::factory()->rejected()->past()->count(5)->create()
            ->each(function (Reservation $r) use ($petugas) {
                StatusLog::create([
                    'reservation_id'     => $r->id,
                    'report_id'          => null,
                    'facility_id'        => null,
                    'changed_by_user_id' => $petugas->id,
                    'old_status'         => 'pending',
                    'new_status'         => 'rejected',
                    'note'               => 'Fasilitas tidak tersedia pada waktu yang diminta.',
                ]);
            });

        // Cancelled (dibatalkan oleh pemesan)
        Reservation::factory()->cancelled()->past()->count(5)->create();

        $total = 10 + 8 + 15 + 5 + 5;
        $this->command->info("Reservation: {$total} reservasi berhasil di-seed.");
    }
}
