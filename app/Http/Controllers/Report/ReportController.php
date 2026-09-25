<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Modul: Laporan, Maintenance & Rekap (Orang 4)
 * Modul database: Reports
 *
 * TODO Orang 4:
 * - store(): INSERT reports + INSERT report_photos (minimal 1 foto) WAJIB
 *   dibungkus satu DB::transaction() — rollback kalau tidak ada foto yang
 *   berhasil di-attach (relasi mandatory-mandatory, lihat README-database.pdf)
 * - index() untuk pengguna: riwayat + status laporan sendiri
 * - updateStatus(): baru -> diproses -> selesai/ditolak, isi resolution_note
 *   & resolved_at saat ditutup
 * - markUnderRepair()/markFixed(): JANGAN ubah $facility->status langsung,
 *   panggil $facility->updateFacilityStatus() dari Model Facility (milik Orang 2)
 * - rekap(): export CSV/Excel/PDF okupansi (dari Reservation milik Orang 3)
 *   + frekuensi kerusakan (dari Report sendiri)
 */

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::with([
            'facility.location',
            'facility.type',
            'photos',
        ])
        ->where('user_id', $request->user()->id)
        ->whereIn('status', [
            'baru',
            'diproses',
            'selesai',
            'ditolak',
        ])
        ->latest()
        ->get();

        return view('reports.index', compact('reports'));
    }

    public function drafts(Request $request)
    {
        $reports = Report::with([
            'facility.location',
            'photos',
        ])
        ->where('user_id', $request->user()->id)
        ->where('status', 'draft')
        ->latest()
        ->get();

        return view('reports.drafts', compact('reports'));
    }

    public function preview(Request $request, Report $report)
    {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $report->status === 'draft',
            404
        );

        $report->load([
            'facility.location',
            'photos',
        ]);

        return view('reports.preview', compact('report'));
    }

    public function editDraft(
        Request $request,
        Report $report
    ) {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $report->status === 'draft',
            404
        );

        $facilities = Facility::with([
            'location',
            'type',
        ])
        ->where('status', 'aktif')
        ->orderBy('name')
        ->get();

        $report->load([
            'facility.location',
            'facility.type',
            'photos',
        ]);

        return view(
            'reports.edit',
            compact('report','facilities')
        );
    }

    public function updateDraft(Request $request, Report $report)
    {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $report->status === 'draft',
            404
        );

        $validated = $request->validate(
        [
            'facility_id' => ['required', 'exists:facilities,id'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['nullable', 'image', 'max:4096'],
            'delete_photo_ids' => ['nullable', 'array'],
            'delete_photo_ids.*' => ['integer','exists:report_photos,id',],
        ],
        [
            'photos.max' => 'Maksimal 3 foto.',
            'photos.*.max' => 'Ukuran setiap foto maksimal 4 MB.',
            'photos.*.image' => 'File yang dipilih harus berupa gambar.',
        ]
    );

        DB::transaction(function () use (
            $validated,
            $request,
            $report
        ) {
            //Update data utama laporan.
            $report->update([
                'facility_id' => $validated['facility_id'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
            ]);

            // Hapus foto yang ditandai user
            $deletePhotoIds =
                $validated['delete_photo_ids'] ?? [];

            foreach ($deletePhotoIds as $photoId) {

                $photo = $report->photos()
                    ->where('id', $photoId)
                    ->first();

                if ($photo) {

                    Storage::disk('public')
                        ->delete($photo->file_path);

                    $photo->delete();
                }
            }

            if ($request->hasFile('photos')) {
                $currentPhotoCount = $report->photos()->count();
                $deletePhotoIds = $validated['delete_photo_ids'] ?? [];
                $currentPhotoCountAfterDelete = $currentPhotoCount - count($deletePhotoIds);
                $newPhotoCount = count($request->file('photos', []));

                if ($currentPhotoCountAfterDelete + $newPhotoCount > 3) {
                    return back()->withErrors([
                        'photos' =>
                            'Maksimal 3 foto untuk satu laporan.',
                    ]);
                }

                foreach ($request->file('photos')as $photo) {
                    $path = $photo->store(
                        'report-photos',
                        'public'
                    );

                    $report->photos()->create([
                        'file_path' => $path,
                    ]);
                }
            }
        });

        return redirect()
            ->route(
                'pengguna.reports.preview',
                $report
            )
            ->with(
                'status',
                'Draft berhasil diperbarui.'
            );
    }

    public function submitDraft(Request $request, Report $report)
    {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $report->status === 'draft',
            404
        );

        $report->load('photos');

        if ($report->photos->isEmpty()) {
            return back()->withErrors([
                'photos' =>
                    'Laporan belum dapat dikirim. Silakan tambahkan minimal satu foto kerusakan terlebih dahulu.',
            ]);
        }

        $report->update([
            'status' => 'baru',
        ]);

        return redirect()
            ->route('pengguna.reports.show', $report)
            ->with(
                'status',
                'Laporan berhasil dikirim dan menunggu diproses petugas.'
            );
    }

    public function create()
    {
        $facilities = Facility::with([
            'location',
            'type',
        ])
        ->where('status', 'aktif')
        ->orderBy('name')
        ->get();

        return view('reports.create', compact('facilities'));
    }

    //User bisa melihat detail laporan miliknya sendiri, tapi tidak bisa melihat laporan orang lain.
    public function show(Request $request, Report $report)
    {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        if ($report->status === 'draft') {
            return redirect()
                ->route('pengguna.reports.preview', $report);
        }

        $report->load([
            'facility.location',
            'photos',
        ]);

        return view('reports.show', compact('report'));
    }

    public function storeDraft(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'max:4096'],
        ],
        [
            'photos.max' =>
                'Maksimal 3 foto yang dapat diupload.',

            'photos.min' =>
                'Minimal 1 foto harus dipilih.',

            'photos.*.max' =>
                'Ukuran setiap foto maksimal 4 MB.',

            'photos.*.image' =>
                'File yang dipilih harus berupa gambar.',
        ]);

        $report = DB::transaction(function () use ($validated, $request) {
            $report = Report::create([
                'user_id' => $request->user()->id,
                'facility_id' => $validated['facility_id'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'status' => 'draft',
            ]);

            foreach ($request->file('photos', []) as $photo) {
                $path = $photo->store('report-photos', 'public');

                $report->photos()->create([
                    'file_path' => $path,
                ]);
            }

            return $report;
        });

        if ($request->input('action') === 'preview') {
            return redirect()
                ->route('pengguna.reports.preview', $report);
        }

        return redirect()
            ->route('pengguna.reports.drafts')
            ->with('status', 'Draft laporan berhasil disimpan.');
    }

    public function deletePhoto(
        Request $request,
        Report $report,
        \App\Models\ReportPhoto $photo
    ) {
        abort_unless(
            $report->user_id === $request->user()->id,
            403
        );

        abort_unless(
            $report->status === 'draft',
            404
        );

        abort_unless(
            $photo->report_id === $report->id,
            404
        );

        Storage::disk('public')->delete(
            $photo->file_path
        );

        $photo->delete();

        return back()->with(
            'status',
            'Foto berhasil dihapus.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'max:4096'],
        ]);

        $report = DB::transaction(function () use ($validated, $request) {
            $report = Report::create([
                'user_id' => $request->user()->id,
                'facility_id' => $validated['facility_id'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'status' => 'baru',
            ]);

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('report-photos', 'public');
                $report->photos()->create(['file_path' => $path]);
            }

            // Kalau sampai sini tidak ada foto yang tersimpan, transaction
            // akan rollback otomatis karena validasi 'min:1' di atas
            // menjamin minimal ada 1 file yang lolos ke sini.

            return $report;
        });

        return redirect()->route('pengguna.reports.show', $report)->with('status', 'Laporan berhasil dikirim.');
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai,ditolak'],
            'resolution_note' => ['nullable', 'required_if:status,selesai,ditolak', 'string'],
        ]);

        if ($report->status === 'baru' && $validated['status'] !== 'diproses')
        {
            return back()->withErrors([
                'status' => 'Laporan baru hanya dapat diubah menjadi diproses.',
            ]);
        }

        if ($report->status === 'diproses' && !in_array($validated['status'],['selesai', 'ditolak'],true)) 
        {
            return back()->withErrors([
                'status' => 'Laporan yang sedang diproses hanya dapat menjadi selesai atau ditolak.',
            ]);
        }

        $report->update([
            'status' => $validated['status'],
            'resolution_note' => $validated['resolution_note'] ?? $report->resolution_note,
            'resolved_at' => in_array($validated['status'], ['selesai', 'ditolak'], true) ? now() : null,
        ]);

        return back()->with('status', 'Status laporan diperbarui.');
    }

    public function markUnderRepair(Request $request, Report $report)
    {
        // Panggil method milik Model Facility (Orang 2), jangan bikin
        // logic status fasilitas sendiri di sini.
        $report->facility->updateFacilityStatus('dalam perbaikan', $request->user()->id, "Terkait laporan #{$report->id}");

        return back()->with('status', 'Fasilitas ditandai dalam perbaikan.');
    }

    public function markFixed(Request $request, Report $report)
    {
        $report->facility->updateFacilityStatus('aktif', $request->user()->id, "Perbaikan selesai, terkait laporan #{$report->id}");

        return back()->with('status', 'Fasilitas dikembalikan ke status aktif.');
    }
}
