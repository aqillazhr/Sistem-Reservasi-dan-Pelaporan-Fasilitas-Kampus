<?php

namespace App\Http\Controllers\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Reservation;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Modul: Reservasi (Orang 3)
 * Modul database: Reservations
 *
 * Bagian PENGGUNA: hub, create, store, history, show, cancel, slots.
 * Bagian PETUGAS (approve/reject/petugasCancel): belum dikerjakan di tahap ini.
 * Logika transaction + cek bentrok ada di App\Services\ReservationService.
 */
class ReservationController extends Controller
{
    public function __construct(private ReservationService $service) {}

    // ------------------------------------------------------------
    // PENGGUNA
    // ------------------------------------------------------------

    // "Halaman Reservasi" (Pilih Aktivitas): Ajukan Reservasi / Reservasi Saya
    public function hub(Request $request)
    {
        $userId = $request->user()->id;

        $base = Reservation::where('user_id', $userId);

        // Hitung per status DB (pending, rejected, cancelled)
        $byStatus = (clone $base)->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'pending'    => $byStatus['pending']   ?? 0,
            'aktif'      => (clone $base)->approvedActive()->count(),
            'selesai'    => (clone $base)->approvedFinished()->count(),
            'rejected'   => $byStatus['rejected']  ?? 0,
            'cancelled'  => $byStatus['cancelled'] ?? 0,
        ];

        return view('reservations.hub', compact('counts'));
    }

    // "Halaman Reservasi Saya" — semua dimuat sekaligus, tab filter di client-side
    public function history(Request $request)
    {
        $this->service->autoCancelExpired($request->user()->id);

        $reservations = $request->user()->reservations()
            ->with(['facility.location', 'statusLogs'])
            ->orderByDesc('updated_at')
            ->paginate(50)
            ->withQueryString(); //kalau user berpindah ke halaman 2 atau 3, 
                                 //parameter filter di URL-nya tidak hilang

        return view('reservations.index', [
            'reservations' => $reservations,
        ]);
    }

    // JSON endpoint: daftar fasilitas untuk filter client-side
    public function facilitiesJson(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q'            => ['nullable', 'string', 'max:100'],
            'type_id'      => ['nullable', 'integer'],
            'location_id'  => ['nullable', 'integer'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $facilities = Facility::query()
            ->where('status', 'aktif')
            ->with(['type', 'location'])
            ->when($filters['q'] ?? null,            fn ($q, $v) => $q->where('name', 'like', '%'.addcslashes($v, '%_\\').'%'))
            ->when($filters['type_id'] ?? null,      fn ($q, $v) => $q->where('type_id', $v))
            ->when($filters['location_id'] ?? null,  fn ($q, $v) => $q->where('location_id', $v))
            ->when($filters['min_capacity'] ?? null, fn ($q, $v) => $q->where('capacity', '>=', $v))
            ->orderBy('name')
            ->paginate(8);

        // Dirender di server (Blade auto-escape) lalu disuntikkan sebagai HTML
        // jadi oleh JS — bukan lagi field mentah yang digabung ke innerHTML
        // pakai template literal di client (itu tidak di-escape sama sekali).
        return response()->json([
            'html' => view('reservations.partials.facility-list', ['facilities' => $facilities])->render(),
            'has_prev' => $facilities->currentPage() > 1,
            'has_next' => $facilities->hasMorePages(),
            'prev_page' => $facilities->currentPage() - 1,
            'next_page' => $facilities->currentPage() + 1,
        ]);
    }

    // JSON endpoint: slot board untuk fasilitas+tanggal tertentu
    public function slotBoardJson(Request $request): JsonResponse
    {
        $data = $request->validate([
            'facility' => ['required', 'integer'],
            'date'     => ['nullable', 'date_format:Y-m-d'],
        ]);

        $facility = Facility::where('status', 'aktif')->with(['type', 'location'])->findOrFail($data['facility']);

        $today   = now()->startOfDay();
        $maxDate = $today->copy()->addDays((int) config('reservation.max_days_ahead'));
        $date    = Carbon::parse($data['date'] ?? $today->toDateString(), config('app.timezone'))->startOfDay();
        $date    = $date->lt($today) ? $today : ($date->gt($maxDate) ? $maxDate : $date);

        $board = $this->service->slotBoard($facility->id, $date->toDateString());

        // Detail fasilitas, papan slot, dan opsi jam dropdown SEMUA dirender
        // Blade di server. Client tidak lagi menerima state mentah (free/
        // pending/approved/past/toosoon) untuk dihitung ulang sendiri jadi
        // tombol & opsi — dia cuma menyuntikkan HTML jadi ke DOM. Ini juga
        // yang mencegah dropdown jam mulai & jam selesai bisa berbeda aturan
        // seperti yang pernah terjadi waktu logikanya masih ditulis dobel di JS.
        return response()->json([
            'facility_name' => $facility->name, // teks polos, dipakai lewat textContent
            'detail_html' => view('reservations.partials.facility-detail', ['facility' => $facility])->render(),
            'board_html' => view('reservations.partials.board', ['board' => $board])->render(),
            'start_options_html' => view('reservations.partials.time-options', ['board' => $board, 'field' => 'start'])->render(),
            'end_options_html' => view('reservations.partials.time-options', ['board' => $board, 'field' => 'end'])->render(),
            'facility_id' => $facility->id,
            'date'     => $date->toDateString(),
            'minDate'  => $today->toDateString(),
            'maxDate'  => $maxDate->toDateString(),
            'closeTime' => config('reservation.close_time'),
        ]);
    }


    public function create(Request $request)
    {
        return view('reservations.create', [
            'types'       => FacilityType::orderBy('name')->get(),
            'locations'   => \App\Models\Location::orderBy('scope_level')->orderBy('fakultas')->get(),
            // Tanggal min/max untuk validasi JS
            'minDate'     => now()->startOfDay()->toDateString(),
            'maxDate'     => now()->startOfDay()->addDays((int) config('reservation.max_days_ahead'))->toDateString(),
        ]);
    }

    public function store(StoreReservationRequest $request)
    {
        $reservation = $this->service->create($request->user(), $request->validated());

        return redirect()
            ->route('pengguna.reservations.show', $reservation)
            ->with('status', 'Reservasi diajukan, menunggu persetujuan petugas.');
    }

    // Detail & Status Reservasi (hanya milik sendiri)
    public function show(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $this->service->autoCancelExpired($request->user()->id);
        $reservation->refresh(); // status di atas bisa saja baru saja berubah jadi cancelled

        $reservation->load(['facility.location', 'statusLogs.changedBy']);

        // Alasan penolakan disimpan petugas di status_logs.note (new_status = rejected)
        $rejectNote = $reservation->status === 'rejected'
            ? optional($reservation->statusLogs->where('new_status', 'rejected')->last())->note
            : null;

        return view('reservations.show', compact('reservation', 'rejectNote'));
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        $this->service->cancelByOwner($request->user(), $reservation->id);

        return redirect()
            ->route('pengguna.reservations.show', $reservation)
            ->with('status', 'Reservasi dibatalkan.');
    }

    // ------------------------------------------------------------
    // PUBLIK: data slot terpakai (kontrak dengan Orang 2 untuk kalender availability)
    // GET /fasilitas/{facility}/slot?from=YYYY-MM-DD&days=1..7
    // Tanpa data pemohon/tujuan. state: pending (menunggu konfirmasi) | approved (terisi)
    // ------------------------------------------------------------
    public function slots(Request $request, Facility $facility): JsonResponse
    {
        abort_if($facility->status === 'nonaktif', 404);

        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'days' => ['nullable', 'integer', 'min:1', 'max:7'],
        ]);

        $from = $data['from'] ?? now()->toDateString();
        $days = (int) ($data['days'] ?? 7);

        $slots = $this->service->occupiedSlots($facility->id, $from, $days)->map(fn ($r) => [
            'facility_id' => $facility->id,
            'date' => $r->reservation_date->toDateString(),
            'start_time' => substr($r->start_time, 0, 5),
            'end_time' => substr($r->end_time, 0, 5),
            'state' => $r->status,
        ]);

        return response()->json(['facility_id' => $facility->id, 'from' => $from, 'days' => $days, 'slots' => $slots]);
    }

    // ------------------------------------------------------------
    // PETUGAS
    // ------------------------------------------------------------

    // "Kelola Reservasi": daftar semua reservasi + tab status, aksi
    // Setuju/Tolak (menunggu) atau Batalkan (disetujui) di tiap baris.
    public function petugasIndex(Request $request)
    {
        // Beresin dulu semua pending yang sudah lewat waktu bookingnya
        // (lintas pengguna) sebelum antrian ini ditampilkan ke petugas.
        $this->service->autoCancelExpired();

        $validTabs = ['semua', 'menunggu', 'aktif', 'selesai', 'ditolak', 'dibatalkan'];
        $tab = $request->query('status', 'menunggu');
        $tab = in_array($tab, $validTabs, true) ? $tab : 'menunggu';
        $q = trim((string) $request->query('q', ''));

        $reservations = Reservation::query()
            ->with(['user', 'facility.location'])
            ->when($tab === 'menunggu', fn ($query) => $query->where('status', 'pending'))
            ->when($tab === 'aktif', fn ($query) => $query->approvedActive())
            ->when($tab === 'selesai', fn ($query) => $query->approvedFinished())
            ->when($tab === 'ditolak', fn ($query) => $query->where('status', 'rejected'))
            ->when($tab === 'dibatalkan', fn ($query) => $query->where('status', 'cancelled'))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.addcslashes($q, '%_\\').'%';
                $query->where(function ($sub) use ($like) {
                    $sub->whereHas('user', fn ($u) => $u->where('name', 'like', $like))
                        ->orWhereHas('facility', fn ($f) => $f->where('name', 'like', $like));
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('reservations.partials.petugas-table', [
                'reservations' => $reservations,
                'tab' => $tab,
            ]);
        }

        return view('reservations.petugas-index', [
            'reservations' => $reservations,
            'tab' => $tab,
            'q' => $q,
        ]);
    }

    // Dashboard petugas: dipanggil dari DashboardController::petugas() lewat
    // component <x-dashboard.petugas-reservation-queue />, bukan lewat route
    // terpisah — lihat app/View/Components/Dashboard/PetugasReservationQueue.php

    // Detail reservasi sisi petugas (baris tabel Kelola Reservasi bisa diklik
    // supaya tujuan/keterangan yang panjang tidak perlu dipotong di tabel).
    public function petugasShow(Request $request, Reservation $reservation)
    {
        $reservation->load(['user', 'facility.location', 'statusLogs.changedBy']);

        return view('reservations.petugas-show', compact('reservation'));
    }

    public function approve(Request $request, Reservation $reservation)
    {
        $this->service->approveByOfficer($request->user(), $reservation->id);

        return back()->with('status', 'Reservasi disetujui.');
    }

    public function reject(Request $request, Reservation $reservation)
    {
        // Alasan wajib diisi (lihat flowchart: "Isi Alasan" sebelum status -> Ditolak).
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:500'],
        ]);

        $this->service->rejectByOfficer($request->user(), $reservation->id, $validated['note']);

        return back()->with('status', 'Reservasi ditolak. Slot otomatis tersedia kembali.');
    }

    public function petugasCancel(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $this->service->cancelByOfficer($request->user(), $reservation->id, $validated['cancellation_reason']);

        return back()->with('status', 'Reservasi dibatalkan petugas.');
    }
}
