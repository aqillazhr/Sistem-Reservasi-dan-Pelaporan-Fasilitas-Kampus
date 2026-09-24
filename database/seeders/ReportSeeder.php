<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportPhoto;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::whereIn('role', ['petugas', 'admin'])
            ->where('status', 'verified')
            ->first();

        // ─── Laporan per status ────────────────────────────────────────────────

        // Draft (belum disubmit pengguna)
        Report::factory()->draft()->count(3)->create()->each(function (Report $r) {
            // Draft belum wajib punya foto (belum submit)
        });

        // Baru (sudah disubmit, belum ditangani petugas)
        Report::factory()->baru()->count(8)->create()->each(function (Report $r) {
            ReportPhoto::create([
                'report_id' => $r->id,
                'file_path' => 'report-photos/dummy-'.$r->id.'-1.jpg',
            ]);
        });

        // Diproses
        Report::factory()->diproses()->count(5)->create()->each(function (Report $r) use ($petugas) {
            ReportPhoto::create([
                'report_id' => $r->id,
                'file_path' => 'report-photos/dummy-'.$r->id.'-1.jpg',
            ]);
            StatusLog::create([
                'reservation_id'     => null,
                'report_id'          => $r->id,
                'facility_id'        => null,
                'changed_by_user_id' => $petugas->id,
                'old_status'         => 'baru',
                'new_status'         => 'diproses',
                'note'               => 'Laporan sedang dalam penanganan teknisi.',
            ]);
        });

        // Selesai
        Report::factory()->selesai()->count(8)->create()->each(function (Report $r) use ($petugas) {
            ReportPhoto::create([
                'report_id' => $r->id,
                'file_path' => 'report-photos/dummy-'.$r->id.'-1.jpg',
            ]);
            StatusLog::create([
                'reservation_id'     => null,
                'report_id'          => $r->id,
                'facility_id'        => null,
                'changed_by_user_id' => $petugas->id,
                'old_status'         => 'baru',
                'new_status'         => 'diproses',
                'note'               => null,
            ]);
            StatusLog::create([
                'reservation_id'     => null,
                'report_id'          => $r->id,
                'facility_id'        => null,
                'changed_by_user_id' => $petugas->id,
                'old_status'         => 'diproses',
                'new_status'         => 'selesai',
                'note'               => $r->resolution_note,
            ]);
        });

        // Ditolak
        Report::factory()->ditolak()->count(4)->create()->each(function (Report $r) use ($petugas) {
            ReportPhoto::create([
                'report_id' => $r->id,
                'file_path' => 'report-photos/dummy-'.$r->id.'-1.jpg',
            ]);
            StatusLog::create([
                'reservation_id'     => null,
                'report_id'          => $r->id,
                'facility_id'        => null,
                'changed_by_user_id' => $petugas->id,
                'old_status'         => 'baru',
                'new_status'         => 'ditolak',
                'note'               => $r->resolution_note,
            ]);
        });

        $total = 3 + 8 + 5 + 8 + 4;
        $this->command->info("Report: {$total} laporan berhasil di-seed (dengan foto & status log).");
    }
}
