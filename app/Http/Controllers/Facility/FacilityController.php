<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Location;
use Illuminate\Http\Request;

/**
 * Modul: Fasilitas (Orang 2)
 * Modul database: Facilities
 *
 * TODO Orang 2:
 * - index(): search & filter (tipe/lokasi/kapasitas) untuk pengunjung & pengguna,
 *   fasilitas nonaktif tidak boleh muncul
 * - show(): halaman detail fasilitas + availability slot (data mentah dari
 *   Reservation milik Orang 3, sepakati formatnya, mis. {facility_id, date, start_time, end_time})
 * - store()/update(): CRUD fasilitas oleh admin, validasi kapasitas > 0 dsb (server & client)
 * - updateStatus(): dipanggil sendiri di sini ATAU dipanggil dari modul Report (Orang 4)
 *   lewat $facility->updateFacilityStatus() di Model Facility — jangan bikin logic
 *   status terpisah di controller Report.
 */
class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        // =========================
        // FILTER TIPE
        // =========================
        $typeId = $request->query('type_id');

        // =========================
        // FILTER STATUS
        // =========================
        $status = $request->query('status');

        // =========================
        // FILTER KAPASITAS
        // =========================
        $capacityMin = filter_var(
            $request->query('capacity_min'),
            FILTER_VALIDATE_INT
        );

        $capacityMax = filter_var(
            $request->query('capacity_max'),
            FILTER_VALIDATE_INT
        );

        $capacityMin = $capacityMin === false ? null : $capacityMin;
        $capacityMax = $capacityMax === false ? null : $capacityMax;

        // =========================
        // BACKWARD COMPATIBILITY
        // ?kapasitas=100
        // =========================
        $capacity = filter_var(
            $request->query('kapasitas'),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                    'max_range' => 100000,
                ],
            ]
        );

        $capacity = $capacity === false ? null : $capacity;

        // Kalau masih memakai ?kapasitas=100,
        // dianggap sebagai kapasitas minimum.
        if ($capacity !== null && $capacityMin === null) {
            $capacityMin = $capacity;
        }

        // =========================
        // SEARCH "kapasitas 100"
        // =========================
        if (preg_match('/kapasitas\s+(\d+)/i', $search, $matches)) {

            $capacityFromSearch = (int) $matches[1];

            if ($capacityMin === null) {
                $capacityMin = $capacityFromSearch;
            }

            $search = trim(
                preg_replace('/kapasitas\s+\d+/i', '', $search)
            );
        }

        // =========================
        // QUERY
        // =========================
        $facilities = Facility::query()

            // SEARCH
            ->when($search !== '', function ($q) use ($search) {

                $q->where(function ($query) use ($search) {

                    $query->where(
                        'name',
                        'like',
                        '%'.$search.'%'
                    )
                        ->orWhereHas('type', function ($typeQuery) use ($search) {
                            $typeQuery->where(
                                'name',
                                'like',
                                '%'.$search.'%'
                            );
                        })
                        ->orWhereHas('location', function ($locationQuery) use ($search) {

                            $locationQuery
                                ->where('fakultas', 'like', '%'.$search.'%')
                                ->orWhere('prodi', 'like', '%'.$search.'%')
                                ->orWhere('gedung', 'like', '%'.$search.'%')
                                ->orWhere('ruangan', 'like', '%'.$search.'%');
                        });
                });
            })

            // FILTER TIPE
            ->when($typeId, function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            })

            // FILTER STATUS
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })

            // KAPASITAS MINIMUM
            ->when($capacityMin !== null, function ($q) use ($capacityMin) {
                $q->where('capacity', '>=', $capacityMin);
            })

            // KAPASITAS MAKSIMUM
            ->when($capacityMax !== null, function ($q) use ($capacityMax) {
                $q->where('capacity', '<=', $capacityMax);
            })

            ->with([
                'type',
                'location',
                'photos',
            ])

            ->paginate(12)

            ->withQueryString();

        // Data dropdown tipe
        $types = FacilityType::orderBy('name')->get();

        return view(
            'facilities.index',
            compact(
                'facilities',
                'search',
                'typeId',
                'status',
                'capacity',
                'capacityMin',
                'capacityMax',
                'types'
            )
        );
    }

    public function show(Facility $facility)
    {
        $facility->load(['type', 'location', 'photos']);

        // TODO: ambil slot terpakai dari Reservation (Orang 3) untuk
        // ditampilkan di kalender availability.

        return view('facilities.show', compact('facility'));
    }

    public function create()
    {
        $types = FacilityType::all();

        $locations = Location::query()
            ->whereNotNull('fakultas')
            ->get();

        $faculties = $locations
            ->pluck('fakultas')
            ->unique()
            ->sort()
            ->values();

        $buildings = $locations
            ->whereNotNull('gedung')
            ->pluck('gedung')
            ->unique()
            ->sort()
            ->values();

        $programsByFaculty = $locations
            ->whereNotNull('prodi')
            ->groupBy('fakultas')
            ->map(function ($items) {
                return $items
                    ->pluck('prodi')
                    ->unique()
                    ->sort()
                    ->values();
            });

        return view('admin.facilities.create', compact(
            'types',
            'faculties',
            'buildings',
            'programsByFaculty'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'exists:facility_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $facility = Facility::create($validated + ['status' => 'aktif']);

        return redirect()->route('facilities.show', $facility)->with('status', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'exists:facility_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $facility->update($validated);

        return redirect()->route('facilities.show', $facility)->with('status', 'Fasilitas berhasil diperbarui.');
    }

    public function byFaculty(Request $request, string $faculty)
    {
        $typeId = $request->query('type_id');

        $status = $request->query('status');

        $capacityMin = filter_var(
            $request->query('capacity_min'),
            FILTER_VALIDATE_INT
        );

        $capacityMax = filter_var(
            $request->query('capacity_max'),
            FILTER_VALIDATE_INT
        );

        $capacityMin = $capacityMin === false ? null : $capacityMin;
        $capacityMax = $capacityMax === false ? null : $capacityMax;

        $facilities = Facility::query()

            // Tetap hanya fasilitas dari fakultas yang sedang dibuka
            ->whereHas('location', function ($q) use ($faculty) {
                $q->where('fakultas', $faculty);
            })

            // Filter tipe
            ->when($typeId, function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            })

            // Filter status
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })

            // Kapasitas minimum
            ->when($capacityMin !== null, function ($q) use ($capacityMin) {
                $q->where('capacity', '>=', $capacityMin);
            })

            // Kapasitas maksimum
            ->when($capacityMax !== null, function ($q) use ($capacityMax) {
                $q->where('capacity', '<=', $capacityMax);
            })

            ->with(['type', 'location', 'photos'])

            ->paginate(12)

            ->withQueryString();

        $types = FacilityType::orderBy('name')->get();

        return view('facilities.by-faculty', compact(
            'facilities',
            'faculty',
            'types',
            'typeId',
            'status',
            'capacityMin',
            'capacityMax'
        ));
    }

    public function byBuilding(string $building)
    {
        $facilities = Facility::query()
            ->where('status', '!=', 'nonaktif')
            ->whereHas('location', function ($q) use ($building) {
                $q->where('scope_level', 'universitas')
                    ->where(function ($q2) use ($building) {
                        $q2->where('gedung', $building)
                            ->orWhere('ruangan', $building);
                    });
            })
            ->with(['type', 'location', 'photos'])
            ->paginate(12);

        return view('facilities.by-group', [
            'facilities' => $facilities,
            'groupName' => $building,
        ]);
    }
}
