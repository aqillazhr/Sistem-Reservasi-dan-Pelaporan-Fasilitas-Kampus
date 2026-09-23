<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use App\Models\Facility;
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
        $search = trim($request->search ?? '');

        $facilities = collect();

        if ($search !== '') {
            $facilities = Facility::query()
                ->where('status', '!=', 'nonaktif')
                ->when($search !== '', function ($q) use ($search) {
                    if (preg_match('/kapasitas\s*(\d+)/i', $search, $matches)) {
                        $capacity = (int) $matches[1];

                        $q->where('capacity', '>=', $capacity);
                    } else {
                        $q->where('name', 'like', '%'.$search.'%');
                    }
                })
                ->with(['type', 'location', 'photos'])
                ->paginate(12)
                ->withQueryString();
        }

        return view('facilities.index', compact('facilities', 'search'));
    }

    public function show(Facility $facility)
    {
        $facility->load(['type', 'location', 'photos']);

        // TODO: ambil slot terpakai dari Reservation (Orang 3) untuk
        // ditampilkan di kalender availability.

        return view('facilities.show', compact('facility'));
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

    public function byFaculty(string $faculty)
    {
        $facilities = Facility::query()
            ->where('status', '!=', 'nonaktif')
            ->whereHas('location', function ($q) use ($faculty) {
                $q->where('fakultas', $faculty);
            })
            ->with(['type', 'location', 'photos'])
            ->paginate(12);

        return view('facilities.by-faculty', compact('facilities', 'faculty'));
    }
}
