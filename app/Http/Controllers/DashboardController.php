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
        $totalFacilities = Facility::count();

        $totalAccounts = User::count();

        $pendingAccounts = User::where('status', 'pending')
            ->count();

        $pendingRegistrations = User::where('status', 'pending')
            ->latest()
            ->take(3)
            ->get();

        $latestReservationDate = Reservation::orderByDesc('reservation_date')
            ->value('reservation_date');

        $latestReportDate = Report::orderByDesc('created_at')
            ->value('created_at');

        $dashboardMonth = now()->format('Y-m');

        $possibleMonths = array_filter([
            $latestReservationDate
                ? substr((string) $latestReservationDate, 0, 7)
                : null,

            $latestReportDate
                ? substr((string) $latestReportDate, 0, 7)
                : null,
        ]);

        if (! empty($possibleMonths)) {
            $dashboardMonth = max($possibleMonths);
        }

        $monthStart = \Carbon\Carbon::createFromFormat(
            'Y-m',
            $dashboardMonth
        )->startOfMonth();

        $monthEnd = $monthStart->copy()->endOfMonth();

        $facilities = Facility::with('location')
            ->orderBy('name')
            ->get();

        $approvedReservations = Reservation::where(
            'status',
            'approved'
        )
            ->whereBetween(
                'reservation_date',
                [
                    $monthStart->toDateString(),
                    $monthEnd->toDateString(),
                ]
            )
            ->get();

        $reports = Report::whereIn('status', [
            'baru',
            'diproses',
            'selesai',
            'ditolak',
        ])
            ->whereBetween(
                'created_at',
                [
                    $monthStart,
                    $monthEnd,
                ]
            )
            ->get();

        $availableMinutes = $monthStart->daysInMonth * 13 * 60;

        $occupancyRows = $facilities
            ->map(function ($facility) use (
                $approvedReservations,
                $availableMinutes
            ) {
                $usedMinutes = 0;

                foreach (
                    $approvedReservations->where(
                        'facility_id',
                        $facility->id
                    ) as $reservation
                ) {
                    $start = \Carbon\Carbon::parse(
                        $reservation->reservation_date
                    )->setTimeFromTimeString(
                        $reservation->start_time
                    );

                    $end = \Carbon\Carbon::parse(
                        $reservation->reservation_date
                    )->setTimeFromTimeString(
                        $reservation->end_time
                    );

                    $usedMinutes += $start->diffInMinutes($end);
                }

                $percentage = $availableMinutes > 0
                    ? min(
                        100,
                        round(
                            ($usedMinutes / $availableMinutes) * 100
                        )
                    )
                    : 0;

                return [
                    'name' => $facility->name,
                    'location' =>
                        $facility->location->gedung
                        ?? $facility->location->fakultas
                        ?? 'Universitas',
                    'percentage' => $percentage,
                ];
            })
            ->values();

        $damageRows = $facilities
            ->map(function ($facility) use ($reports) {
                return [
                    'name' => $facility->name,
                    'location' =>
                        $facility->location->gedung
                        ?? $facility->location->fakultas
                        ?? 'Universitas',
                    'count' => $reports->where(
                        'facility_id',
                        $facility->id
                    )->count(),
                ];
            })
            ->values();

        $highestOccupancy = $occupancyRows
            ->sortByDesc('percentage')
            ->first();

        $lowestOccupancy = $occupancyRows
            ->sortBy('percentage')
            ->first();

        $mostDamaged = $damageRows
            ->filter(function ($row) {
                return $row['count'] > 0;
            })
            ->sortByDesc('count')
            ->first();

        return view(
            'dashboard.admin',
            compact(
                'totalFacilities',
                'totalAccounts',
                'pendingAccounts',
                'pendingRegistrations',
                'dashboardMonth',
                'highestOccupancy',
                'lowestOccupancy',
                'mostDamaged'
            )
        );
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