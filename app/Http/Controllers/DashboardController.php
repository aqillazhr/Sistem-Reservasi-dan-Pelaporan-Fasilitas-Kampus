<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;

/**
 * Shell dashboard per role. Isi statistik/list ada di masing-masing modul
 * (Orang 1 cuma bikin shell-nya + redirect awal sesuai role).
 * Dashboard admin bagian statistik lintas modul dikerjakan Orang 4 paling
 * akhir, setelah 3 modul lain jadi.
 */
class DashboardController extends Controller
{
    public function admin()
    {
        // TODO Orang 4 (dikerjakan terakhir): isi statistik lintas modul
        // (total akun dari Orang 1, total fasilitas dari Orang 2, rekap dari sini).
        return view('dashboard.admin');
    }

    public function petugas()
    {
        // TODO Orang 3 & Orang 4: antrian reservasi & laporan yang menunggu diproses.
        return view('dashboard.petugas');
    }

    public function pengguna()
    {
        // TODO Orang 3 & Orang 4: riwayat reservasi & laporan milik user login.
        $userId = auth()->id();

        $reports = Report::where('user_id', $userId)
            ->whereIn('status', [
                'baru',
                'diproses',
                'selesai',
                'ditolak',
            ])
            ->latest()
            ->get();

        $newReports = $reports
            ->where('status', 'baru')
            ->count();

        $processingReports = $reports
            ->where('status', 'diproses')
            ->count();

        $totalReports = $reports->count();

        return view(
            'dashboard.pengguna',
            compact(
                'reports',
                'newReports',
                'processingReports',
                'totalReports'
            )
        );
    }
}
