<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
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

        return redirect()->route('reports.show', $report)->with('status', 'Laporan berhasil dikirim.');
    }

    public function updateStatus(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:diproses,selesai,ditolak'],
            'resolution_note' => ['nullable', 'required_if:status,selesai,ditolak', 'string'],
        ]);

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
