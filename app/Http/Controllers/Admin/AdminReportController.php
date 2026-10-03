<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Report;
use App\Models\Reservation;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query(
            'month',
            now()->format('Y-m')
        );

        $locationId = $request->query('location_id');

        $data = $this->getRecapData(
            $month,
            $locationId
        );

        $locations = Location::orderBy('gedung')
            ->orderBy('fakultas')
            ->get();

        return view(
            'admin.reports.index',
            compact(
                'data',
                'locations',
                'month',
                'locationId'
            )
        );
    }

    private function getRecapData(
        string $month,
        ?int $locationId = null
    ): array {
        $start = Carbon::createFromFormat(
            'Y-m',
            $month
        )->startOfMonth();

        $end = $start->copy()->endOfMonth();

        $facilities = Facility::with([
            'type',
            'location',
        ])
            ->when($locationId, function ($query) use ($locationId) {
                $query->where(
                    'location_id',
                    $locationId
                );
            })
            ->orderBy('name')
            ->get();

        $reservations = Reservation::where(
            'status',
            'approved'
        )
            ->whereBetween(
                'reservation_date',
                [
                    $start->toDateString(),
                    $end->toDateString(),
                ]
            )
            ->get()
            ->groupBy('facility_id');

        $reports = Report::where(
            'status',
            '!=',
            'draft'
        )
            ->whereBetween(
                'created_at',
                [
                    $start->copy()->startOfDay(),
                    $end->copy()->endOfDay(),
                ]
            )
            ->get()
            ->groupBy('facility_id');

        /*
         * Jam operasional:
         * 07.00 - 20.00
         */
        $operatingMinutesPerDay = 13 * 60;

        $daysInMonth = $start->daysInMonth;

        $totalAvailableMinutes =
            $daysInMonth *
            $operatingMinutesPerDay;

        $rows = $facilities->map(function ($facility) use (
            $reservations,
            $reports,
            $totalAvailableMinutes
        ) {
            $facilityReservations =
                $reservations->get(
                    $facility->id,
                    collect()
                );

            $reservedMinutes = 0;

            foreach ($facilityReservations as $reservation) {

                $startTime = Carbon::parse(
                    $reservation->start_time
                );

                $endTime = Carbon::parse(
                    $reservation->end_time
                );

                $reservedMinutes +=
                    $startTime->diffInMinutes(
                        $endTime
                    );
            }

            $occupancy =
                $totalAvailableMinutes > 0
                    ? round(
                        (
                            $reservedMinutes /
                            $totalAvailableMinutes
                        ) * 100,
                        1
                    )
                    : 0;

            $damageCount =
                $reports->get(
                    $facility->id,
                    collect()
                )->count();

            return [
                'facility' =>
                    $facility->name,

                'type' =>
                    $facility->type->name ?? '-',

                'location' =>
                    $this->formatLocation(
                        $facility
                    ),

                'occupancy' =>
                    $occupancy,

                'damage_count' =>
                    $damageCount,

                'damage_percentage' =>
                    0,
            ];
        })->values();

        $highestDamage =
            $rows->max('damage_count') ?? 0;

        $rows = $rows->map(function ($row) use (
            $highestDamage
        ) {
            $row['damage_percentage'] =
                $highestDamage > 0
                    ? round(
                        (
                            $row['damage_count'] /
                            $highestDamage
                        ) * 100,
                        1
                    )
                    : 0;

            return $row;
        });

        return [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
        ];
    }

    private function formatLocation($facility): string
    {
        $location = $facility->location;

        if (!$location) {
            return '-';
        }

        $parts = [];

        if ($location->ruangan) {
            $parts[] = $location->ruangan;
        }

        if ($location->gedung) {
            $parts[] = $location->gedung;
        }

        if ($location->fakultas) {
            $parts[] = $location->fakultas;
        }

        return $parts
            ? implode(', ', $parts)
            : '-';
    }

    public function exportCsv(Request $request)
    {
        $month = $request->query(
            'month',
            now()->format('Y-m')
        );

        $locationId =
            $request->query('location_id');

        $data = $this->getRecapData(
            $month,
            $locationId
        );

        $filename =
            'rekap-fasilitas-' .
            $month .
            '.csv';

        return response()->streamDownload(
            function () use ($data) {

                $handle =
                    fopen(
                        'php://output',
                        'w'
                    );

                fputcsv($handle, [
                    'Fasilitas',
                    'Tipe',
                    'Lokasi',
                    'Okupansi (%)',
                    'Jumlah Kerusakan',
                    'Frekuensi Kerusakan (%)',
                ]);

                foreach ($data['rows'] as $row) {

                    fputcsv($handle, [
                        $row['facility'],
                        $row['type'],
                        $row['location'],
                        $row['occupancy'],
                        $row['damage_count'],
                        $row['damage_percentage'],
                    ]);
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' =>
                    'text/csv; charset=UTF-8',
            ]
        );
    }

    public function exportExcel(Request $request)
    {
        $month = $request->query(
            'month',
            now()->format('Y-m')
        );

        $locationId =
            $request->query('location_id');

        $data = $this->getRecapData(
            $month,
            $locationId
        );

        $filename =
            'rekap-fasilitas-' .
            $month .
            '.xls';

        $html = '
            <html>
            <head>
                <meta charset="UTF-8">
            </head>

            <body>

                <table border="1">

                    <thead>
                        <tr>
                            <th>Fasilitas</th>
                            <th>Tipe</th>
                            <th>Lokasi</th>
                            <th>Okupansi (%)</th>
                            <th>Jumlah Kerusakan</th>
                            <th>Frekuensi Kerusakan (%)</th>
                        </tr>
                    </thead>

                    <tbody>
        ';

        foreach ($data['rows'] as $row) {

            $html .= '
                <tr>

                    <td>' .
                        e($row['facility']) .
                    '</td>

                    <td>' .
                        e($row['type']) .
                    '</td>

                    <td>' .
                        e($row['location']) .
                    '</td>

                    <td>' .
                        e($row['occupancy']) .
                    '</td>

                    <td>' .
                        e($row['damage_count']) .
                    '</td>

                    <td>' .
                        e($row['damage_percentage']) .
                    '</td>

                </tr>
            ';
        }

        $html .= '
                    </tbody>

                </table>

            </body>
            </html>
        ';

        return response($html)
            ->header(
                'Content-Type',
                'application/vnd.ms-excel'
            )
            ->header(
                'Content-Disposition',
                'attachment; filename="' .
                $filename .
                '"'
            );
    }

    public function exportPdf(Request $request)
    {
        $month = $request->query(
            'month',
            now()->format('Y-m')
        );

        $locationId =
            $request->query('location_id');

        $data = $this->getRecapData(
            $month,
            $locationId
        );

        $html = view(
            'admin.reports.pdf',
            compact(
                'data',
                'month'
            )
        )->render();

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html);

        $dompdf->setPaper(
            'A4',
            'landscape'
        );

        $dompdf->render();

        return response()->streamDownload(
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            'rekap-fasilitas-' .
            $month .
            '.pdf'
        );
    }
}