<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function petugas()
    {
        // Jumlah reservasi yang masih menunggu persetujuan petugas
        $pendingReservations = Reservation::where('status', 'pending')->count();

        // Antrian reservasi terbaru
        $latestReservations = Reservation::with([
            'user',
            'facility',
        ])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        // Jumlah laporan baru
        $newReports = Report::where('status', 'baru')
            ->count();

        // Jumlah laporan sedang diproses
        $processingReports = Report::where('status', 'diproses')
            ->count();

        // Total laporan yang sudah dikirim
        $totalReports = Report::whereIn('status', [
            'baru',
            'diproses',
            'selesai',
            'ditolak',
        ])->count();

        // Jumlah fasilitas yang sedang dalam perbaikan
        $facilitiesUnderRepair = Facility::where(
            'status',
            'dalam perbaikan'
        )->count();

        // Antrian laporan terbaru
        $latestReports = Report::with([
            'user',
            'facility',
        ])
            ->whereIn('status', [
                'baru',
                'diproses',
            ])
            ->latest()
            ->take(5)
            ->get();


        return view(
            'dashboard.petugas',
            compact(
                // Reservasi
                'pendingReservations',
                'latestReservations',

                // Laporan
                'newReports',
                'processingReports',
                'totalReports',
                'facilitiesUnderRepair',
                'latestReports'
            )
        );
    }

    public function pengguna()
    {
        // Ringkasan per fakultas
        $facultyGroups = Facility::query()
            ->where('status', '!=', 'nonaktif')
            ->whereHas('location', fn ($q) => $q->where('scope_level', 'fakultas'))
            ->with(['location', 'photos'])
            ->get()
            ->groupBy(fn ($facility) => $facility->location->fakultas)
            ->sortKeys()
            ->map(fn ($facilities, $fakultas) => $this->summarizeGroup(
                $facilities,
                $fakultas,
                'facilities.by-faculty'
            ))
            ->values();

        // Ringkasan per gedung (tingkat universitas)
        $buildingGroups = Facility::query()
            ->where('status', '!=', 'nonaktif')
            ->whereHas('location', fn ($q) => $q->where('scope_level', 'universitas'))
            ->with(['location', 'photos'])
            ->get()
            ->groupBy(fn ($facility) =>
                $facility->location->gedung
                ?? $facility->location->ruangan
                ?? 'Fasilitas Lainnya'
            )
            ->sortKeys()
            ->map(fn ($facilities, $building) => $this->summarizeGroup(
                $facilities,
                $building,
                'facilities.by-building'
            ))
            ->values();

        $facilityGroups = $facultyGroups->concat($buildingGroups);

        // Riwayat laporan milik user login
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
                'facilityGroups',
                'reports',
                'newReports',
                'processingReports',
                'totalReports'
            )
        );
    }

    private function summarizeGroup($facilities, string $groupName, string $routeName): object
    {
        $withPhoto = $facilities->first(fn ($f) => $f->photos->isNotEmpty());

        return (object) [
            'name' => $groupName,
            'thumbnail' => $withPhoto ? $withPhoto->photos->first()->file_path : null,
            'total' => $facilities->count(),
            'available' => $facilities->where('status', 'aktif')->count(),
            'url' => route($routeName, $groupName),
        ];
    }
}