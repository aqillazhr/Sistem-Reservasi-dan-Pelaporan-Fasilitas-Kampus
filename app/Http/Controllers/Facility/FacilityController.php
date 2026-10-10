<?php

namespace App\Http\Controllers\Facility;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        // FILTER KETERSEDIAAN
        // =========================
        $availability = $request->query('availability');

        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

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

            // FILTER KETERSEDIAAN
            ->when($availability === 'available', function ($q) use ($today, $currentTime) {
                $q->where('status', 'aktif')
                    ->whereDoesntHave('reservations', function ($reservationQuery) use ($today, $currentTime) {
                        $reservationQuery
                            ->whereDate('reservation_date', $today)
                            ->whereIn('status', ['pending', 'approved'])
                            ->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>', $currentTime);
                    });
            })
            ->when($availability === 'unavailable', function ($q) use ($today, $currentTime) {
                $q->where(function ($query) use ($today, $currentTime) {
                    $query
                        ->where('status', '!=', 'aktif')
                        ->orWhereHas('reservations', function ($reservationQuery) use ($today, $currentTime) {
                            $reservationQuery
                                ->whereDate('reservation_date', $today)
                                ->whereIn('status', ['pending', 'approved'])
                                ->where('start_time', '<=', $currentTime)
                                ->where('end_time', '>', $currentTime);
                        });
                });
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
                'reservations' => function ($reservationQuery) use ($today, $currentTime) {
                    $reservationQuery
                        ->whereDate('reservation_date', $today)
                        ->whereIn('status', ['pending', 'approved'])
                        ->where('start_time', '<=', $currentTime)
                        ->where('end_time', '>', $currentTime);
                },
            ])

            ->paginate(5)

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
                'availability',
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
        $types = FacilityType::orderBy('name')->get();

        // Ambil seluruh data lokasi dari database.
        $locations = Location::query()->get();

        // Pisahkan lokasi berdasarkan lingkupnya.
        $facultyLocations = $locations
            ->where('scope_level', 'fakultas')
            ->whereNotNull('fakultas');

        $universityLocations = $locations
            ->where('scope_level', 'universitas');

        // Daftar fakultas.
        $faculties = $facultyLocations
            ->pluck('fakultas')
            ->unique()
            ->sort()
            ->values();

        // Gedung dikelompokkan berdasarkan fakultas.
        $buildingsByFaculty = $facultyLocations
            ->whereNotNull('gedung')
            ->groupBy('fakultas')
            ->map(function ($items) {
                return $items
                    ->pluck('gedung')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
            });

        // Gedung yang berada pada lingkup universitas.
        $universityBuildings = $universityLocations
            ->pluck('gedung')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        // Program studi dikelompokkan berdasarkan fakultas.
        $programsByFaculty = $facultyLocations
            ->whereNotNull('prodi')
            ->groupBy('fakultas')
            ->map(function ($items) {
                return $items
                    ->pluck('prodi')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values();
            });

        return view('admin.facilities.create', compact(
            'types',
            'faculties',
            'buildingsByFaculty',
            'universityBuildings',
            'programsByFaculty'
        ));
    }

    public function adminIndex(Request $request)
    {
        $search = trim($request->query('search', ''));

        $facilities = Facility::query()
            ->with(['type', 'location'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
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
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.facilities.index', compact(
            'facilities',
            'search'
        ));
    }

    public function store(Request $request)
    {
        // Tentukan apakah fasilitas berada di tingkat universitas.
        $fakultasInput = $request->input('fakultas');
        $isUniversity = $fakultasInput === '__UNIVERSITAS__';

        // Ambil fakultas yang benar-benar tersedia di database.
        $availableFaculties = Location::query()
            ->where('scope_level', 'fakultas')
            ->whereNotNull('fakultas')
            ->distinct()
            ->pluck('fakultas')
            ->all();

        // Cari pilihan lokasi yang sesuai dengan lingkup yang dipilih.
        if ($isUniversity) {
            $matchingLocations = Location::query()
                ->where('scope_level', 'universitas')
                ->get();
        } elseif (is_string($fakultasInput) && $fakultasInput !== '') {
            $matchingLocations = Location::query()
                ->where('scope_level', 'fakultas')
                ->where('fakultas', $fakultasInput)
                ->get();
        } else {
            $matchingLocations = collect();
        }

        $availablePrograms = $matchingLocations
            ->pluck('prodi')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $availableBuildings = $matchingLocations
            ->pluck('gedung')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $type = FacilityType::find($request->input('type_id'));

        $capacityRequired = $type && in_array(
            mb_strtolower(trim($type->name)),
            ['ruang kelas', 'aula', 'laboratorium', 'lapangan'],
            true
        );

        // Validasi input form.
        $validated = $request->validate([
            'fakultas' => [
                'required',
                'string',
                Rule::in(array_merge(
                    $availableFaculties,
                    ['__UNIVERSITAS__']
                )),
            ],
            'prodi' => [
                'nullable',
                'string',
                Rule::in($availablePrograms),
            ],
            'gedung' => [
                'nullable',
                'string',
                'max:255',
                Rule::in($availableBuildings),
            ],
            'ruangan' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'exists:facility_types,id'],
            'capacity' => $capacityRequired
                ? ['required', 'integer', 'min:1']
                : ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        // Susun data lokasi sesuai lingkup fasilitas.
        $locationData = [
            'scope_level' => $isUniversity ? 'universitas' : 'fakultas',
            'fakultas' => $isUniversity ? null : $validated['fakultas'],
            'prodi' => $isUniversity
                ? null
                : ($validated['prodi'] ?? null),
            'gedung' => $validated['gedung'] ?? null,
            'ruangan' => $validated['ruangan'] ?? null,
        ];

        // Unggah foto jika admin memilih file.
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')
                ->store('facilities', 'public');

            if (! $photoPath) {
                return back()
                    ->withErrors(['photo' => 'Foto gagal diunggah. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        try {
            // Simpan lokasi, fasilitas, dan catatan foto
            // dalam satu transaksi database.
            $facility = DB::transaction(function () use (
                $validated,
                $locationData,
                $photoPath
            ) {
                // Gunakan lokasi yang sudah ada jika datanya cocok.
                // Jika belum ada, buat lokasi baru.
                $location = Location::firstOrCreate($locationData);

                // Simpan data fasilitas.
                $facility = Facility::create([
                    'name' => $validated['name'],
                    'type_id' => $validated['type_id'],
                    'location_id' => $location->id,
                    'capacity' => $validated['capacity'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'status' => 'aktif',
                ]);

                // Catat foto jika ada.
                if ($photoPath !== null) {
                    $facility->photos()->create([
                        'file_path' => $photoPath,
                        'is_primary' => true,
                        'uploaded_at' => now(),
                    ]);
                }

                return $facility;
            });
        } catch (\Throwable $exception) {
            // Jika transaksi gagal, bersihkan file yang telanjur diunggah.
            if ($photoPath !== null) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.facilities.index')
            ->with('status', 'Fasilitas berhasil ditambahkan.');
    }

    public function toggleActive(Request $request, Facility $facility)
    {
        // Fasilitas dalam perbaikan tidak diubah melalui tombol ini.
        if (! in_array($facility->status, ['aktif', 'nonaktif'], true)) {
            return redirect()
                ->route('admin.facilities.index', $request->only(['search', 'page']))
                ->with('error', 'Fasilitas sedang dalam perbaikan. Status tidak diubah.');
        }

        // Ubah status aktif menjadi nonaktif, atau sebaliknya.
        $newStatus = $facility->status === 'aktif'
            ? 'nonaktif'
            : 'aktif';

        // Simpan perubahan sekaligus mencatat riwayat status.
        $facility->updateFacilityStatus(
            $newStatus,
            $request->user()->id,
            $newStatus === 'nonaktif'
                ? 'Dinonaktifkan oleh admin.'
                : 'Diaktifkan kembali oleh admin.'
        );

        return redirect()
            ->route('admin.facilities.index', $request->only(['search', 'page']))
            ->with(
                'status',
                'Status fasilitas "'.$facility->name.
                '" berhasil diubah menjadi '.$newStatus.'.'
            );
    }

    public function edit(Facility $facility)
    {
        // Ambil data fasilitas beserta lokasi dan tipenya.
        $facility->load(['type', 'location']);

        // Data untuk dropdown form.
        $types = FacilityType::orderBy('name')->get();

        // Ambil semua lokasi, termasuk lokasi tingkat universitas.
        $locations = Location::query()->get();

        // Ambil lokasi yang memiliki data fakultas saja.
        $facultyLocations = $locations->whereNotNull('fakultas');

        $faculties = $facultyLocations
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

        $programsByFaculty = $facultyLocations
            ->whereNotNull('prodi')
            ->groupBy('fakultas')
            ->map(function ($items) {
                return $items
                    ->pluck('prodi')
                    ->unique()
                    ->sort()
                    ->values();
            });

        return view('admin.facilities.edit', compact(
            'facility',
            'types',
            'locations',
            'faculties',
            'buildings',
            'programsByFaculty'
        ));
    }

    public function update(Request $request, Facility $facility)
    {
        $type = FacilityType::find($request->input('type_id'));

        $capacityRequired = $type && in_array(
            mb_strtolower(trim($type->name)),
            ['ruang kelas', 'aula', 'laboratorium', 'lapangan'],
            true
        );
        // Validasi data fasilitas dan foto baru jika diunggah.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['required', 'exists:facility_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'capacity' => $capacityRequired
                ? ['required', 'integer', 'min:1']
                : ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $photo = $validated['photo'] ?? null;
        unset($validated['photo']);

        $validated['capacity'] = $validated['capacity'] ?? null;

        // Perbarui informasi utama fasilitas.
        $facility->update($validated);

        // Jika admin memilih foto baru, proses unggahannya.
        if ($photo) {
            // Simpan foto baru di storage/app/public/facilities.
            $newPath = $photo->store('facilities', 'public');

            // Ambil foto pertama yang sudah tersimpan, jika ada.
            $currentPhoto = $facility->photos()
                ->orderBy('id')
                ->first();

            if ($currentPhoto) {
                // Catat lokasi file lama agar dapat dihapus setelah
                // database berhasil diperbarui.
                $oldPath = $currentPhoto->file_path;

                // Jadikan foto lama yang pertama sebagai foto utama
                // yang diperbarui dengan file baru.
                $facility->photos()->update([
                    'is_primary' => false,
                ]);

                $currentPhoto->update([
                    'file_path' => $newPath,
                    'is_primary' => true,
                    'uploaded_at' => now(),
                ]);

                // Hapus file lama setelah perubahan database berhasil.
                Storage::disk('public')->delete($oldPath);
            } else {
                // Jika belum ada foto, tambahkan foto pertama.
                $facility->photos()->create([
                    'file_path' => $newPath,
                    'is_primary' => true,
                    'uploaded_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('admin.facilities.index')
            ->with('status', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Request $request, Facility $facility)
    {
        // Periksa aturan minimum fasilitas untuk fakultas dan prodi.
        $location = $facility->location;

        if ($location && $location->scope_level === 'fakultas') {

            // Pastikan prodi masih memiliki fasilitas lain.
            if ($location->prodi !== null) {
                $hasOtherFacilitiesInProgram = Facility::query()
                    ->where('id', '!=', $facility->id)
                    ->whereHas('location', function ($query) use ($location) {
                        $query->where('scope_level', 'fakultas')
                            ->where('fakultas', $location->fakultas)
                            ->where('prodi', $location->prodi);
                    })
                    ->exists();

                if (! $hasOtherFacilitiesInProgram) {
                    return redirect()
                        ->route('admin.facilities.index', $request->only(['search', 'page']))
                        ->with(
                            'status',
                            'Fasilitas tidak dapat dihapus karena merupakan fasilitas terakhir di prodi '.
                            $location->prodi.'. Setiap prodi wajib memiliki minimal satu fasilitas.'
                        );
                }
            }

            // Pastikan fakultas masih memiliki fasilitas lain.
            $hasOtherFacilitiesInFaculty = Facility::query()
                ->where('id', '!=', $facility->id)
                ->whereHas('location', function ($query) use ($location) {
                    $query->where('scope_level', 'fakultas')
                        ->where('fakultas', $location->fakultas);
                })
                ->exists();

            if (! $hasOtherFacilitiesInFaculty) {
                return redirect()
                    ->route('admin.facilities.index', $request->only(['search', 'page']))
                    ->with(
                        'status',
                        'Fasilitas tidak dapat dihapus karena merupakan fasilitas terakhir di fakultas '.
                        $location->fakultas.'. Setiap fakultas wajib memiliki minimal satu fasilitas.'
                    );
            }
        }

        // Cegah penghapusan jika masih ada reservasi
        // berstatus menunggu atau disetujui pada hari ini/mendatang.
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        $hasUpcomingReservations = $facility->reservations()
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($today, $currentTime) {
                $query->whereDate('reservation_date', '>', $today)
                    ->orWhere(function ($todayQuery) use ($today, $currentTime) {
                        $todayQuery
                            ->whereDate('reservation_date', $today)
                            ->where('end_time', '>', $currentTime);
                    });
            })
            ->exists();

        if ($hasUpcomingReservations) {
            return redirect()
                ->route('admin.facilities.index', $request->only(['search', 'page']))
                ->with(
                    'status',
                    'Fasilitas tidak dapat dihapus karena masih memiliki reservasi yang menunggu atau disetujui. Selesaikan atau batalkan reservasi terlebih dahulu.'
                );
        }

        // Simpan nama sebelum melakukan soft delete.
        $facilityName = $facility->name;

        // Karena model Facility menggunakan SoftDeletes,
        // data tidak dihapus permanen dari database.
        $facility->delete();

        return redirect()
            ->route('admin.facilities.index', $request->only(['search', 'page']))
            ->with(
                'status',
                'Fasilitas "'.$facilityName.'" berhasil dihapus. Riwayat reservasi dan laporan tetap disimpan.'
            );
    }

    public function byFaculty(Request $request, string $faculty)
    {
        // FILTER
        $typeId = $request->query('type_id');
        $status = $request->query('status');
        $availability = $request->query('availability');

        // WAKTU SEKARANG
        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // KAPASITAS
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

            // HANYA FASILITAS DARI FAKULTAS INI
            ->whereHas('location', function ($q) use ($faculty) {
                $q->where('fakultas', $faculty);
            })

            // FILTER TIPE
            ->when($typeId, function ($q) use ($typeId) {
                $q->where('type_id', $typeId);
            })

            // FILTER STATUS
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })

            // FILTER KAPASITAS MINIMUM
            ->when($capacityMin !== null, function ($q) use ($capacityMin) {
                $q->where('capacity', '>=', $capacityMin);
            })

            // FILTER KAPASITAS MAKSIMUM
            ->when($capacityMax !== null, function ($q) use ($capacityMax) {
                $q->where('capacity', '<=', $capacityMax);
            })

            // FILTER KETERSEDIAAN
            ->when($availability === 'available', function ($q) use ($today, $currentTime) {
                $q->where('status', 'aktif')
                    ->whereDoesntHave('reservations', function ($reservationQuery) use ($today, $currentTime) {
                        $reservationQuery
                            ->whereDate('reservation_date', $today)
                            ->whereIn('status', ['pending', 'approved'])
                            ->where('start_time', '<=', $currentTime)
                            ->where('end_time', '>', $currentTime);
                    });
            })

            ->when($availability === 'unavailable', function ($q) use ($today, $currentTime) {
                $q->where(function ($query) use ($today, $currentTime) {
                    $query
                        ->where('status', 'dalam perbaikan')
                        ->orWhereHas('reservations', function ($reservationQuery) use ($today, $currentTime) {
                            $reservationQuery
                                ->whereDate('reservation_date', $today)
                                ->whereIn('status', ['pending', 'approved'])
                                ->where('start_time', '<=', $currentTime)
                                ->where('end_time', '>', $currentTime);
                        });
                });
            })

            // RESERVATION YANG SEDANG BERLANGSUNG
            ->with([
                'type',
                'location',
                'photos',
                'reservations' => function ($reservationQuery) use ($today, $currentTime) {
                    $reservationQuery
                        ->whereDate('reservation_date', $today)
                        ->whereIn('status', ['pending', 'approved'])
                        ->where('start_time', '<=', $currentTime)
                        ->where('end_time', '>', $currentTime);
                },
            ])

            ->paginate(5)
            ->withQueryString();

        $types = FacilityType::orderBy('name')->get();

        return view('facilities.by-faculty', compact(
            'facilities',
            'faculty',
            'types',
            'typeId',
            'status',
            'capacityMin',
            'capacityMax',
            'availability'
        ));
    }

    public function byBuilding(string $building)
    {
        $facilities = Facility::query()
            ->whereHas('location', function ($q) use ($building) {
                $q->where('scope_level', 'universitas')
                    ->where(function ($q2) use ($building) {
                        $q2->where('gedung', $building)
                            ->orWhere('ruangan', $building);
                    });
            })
            ->with(['type', 'location', 'photos'])
            ->paginate(5);

        return view('facilities.by-group', [
            'facilities' => $facilities,
            'groupName' => $building,
        ]);
    }
}
