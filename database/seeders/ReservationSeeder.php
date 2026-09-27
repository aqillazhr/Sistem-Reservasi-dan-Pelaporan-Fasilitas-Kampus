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

        // ─── Reservasi Khusus Demo (Untuk User Pertama) ──────────────────────
        // Menjamin user pertama selalu punya riwayat lengkap untuk demo/testing
        $demoUser = User::where('role', 'pengguna')->first();
        if ($demoUser) {
            $f = Facility::where('status', 'aktif')->take(5)->get();
            if ($f->isNotEmpty()) {
                $now = now();
                
                // 1. Dibatalkan (oleh pengguna sendiri -> cancellation_reason kosong,
                //    alasan tercatat di status_logs.note, sama seperti cancelByOwner()).
                $dibatalkan = Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->addDays(2)->format('Y-m-d'), 'start_time' => '10:00', 'end_time' => '12:00', 'status' => 'cancelled', 'purpose' => 'Rapat organisasi mahasiswa']);
                StatusLog::create([
                    'reservation_id' => $dibatalkan->id, 'changed_by_user_id' => $demoUser->id,
                    'old_status' => 'pending', 'new_status' => 'cancelled',
                    'note' => 'Batal karena jadwal kuliah pengganti.',
                ]);
                // 2. Ditolak (alasan WAJIB tercatat di status_logs.note, BUKAN
                //    cancellation_reason -- kolom itu khusus pembatalan, lihat
                //    ReservationService::rejectByOfficer()).
                $ditolak = Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->addDays(4)->format('Y-m-d'), 'start_time' => '08:00', 'end_time' => '10:00', 'status' => 'rejected', 'purpose' => 'Seminar proposal skripsi']);
                StatusLog::create([
                    'reservation_id' => $ditolak->id, 'changed_by_user_id' => $petugas?->id ?? $demoUser->id,
                    'old_status' => 'pending', 'new_status' => 'rejected',
                    'note' => 'Fasilitas sedang dalam perbaikan dadakan.',
                ]);
                // 3. Selesai
                Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->subDays(3)->format('Y-m-d'), 'start_time' => '13:00', 'end_time' => '15:00', 'status' => 'approved', 'purpose' => 'Latihan bulu tangkis']);
                // 4. Aktif
                Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->addDays(1)->format('Y-m-d'), 'start_time' => '15:00', 'end_time' => '17:00', 'status' => 'approved', 'purpose' => 'Lomba esai tingkat fakultas']);
                // 5. Menunggu
                Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->addDays(5)->format('Y-m-d'), 'start_time' => '09:00', 'end_time' => '11:00', 'status' => 'pending', 'purpose' => 'Pertemuan klub bahasa Inggris']);
                // 6. Kedaluwarsa
                Reservation::factory()->create(['user_id' => $demoUser->id, 'facility_id' => $f->random()->id, 'reservation_date' => $now->clone()->subDays(1)->format('Y-m-d'), 'start_time' => '08:00', 'end_time' => '09:00', 'status' => 'pending', 'purpose' => 'Diskusi kelompok']);
            }
        }

        // ─── Reservasi mendatang (Acak) ──────────────────────────────────────
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

        // Cancelled (dibatalkan petugas -> cancellation_reason terisi lewat factory,
        // status_logs.note-nya mengikuti alasan yang sama seperti cancelByOfficer()).
        Reservation::factory()->cancelled()->past()->count(5)->create()
            ->each(function (Reservation $r) use ($petugas) {
                StatusLog::create([
                    'reservation_id'     => $r->id,
                    'report_id'          => null,
                    'facility_id'        => null,
                    'changed_by_user_id' => $petugas->id,
                    'old_status'         => 'approved',
                    'new_status'         => 'cancelled',
                    'note'               => $r->cancellation_reason,
                ]);
            });

        $total = 10 + 8 + 15 + 5 + 5;
        $this->command->info("Reservation: {$total} reservasi berhasil di-seed.");
    }
}
