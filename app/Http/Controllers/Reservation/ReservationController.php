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
        $counts = Reservation::query()
            ->where('user_id', $request->user()->id)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('reservations.hub', compact('counts'));
    }

    // "Halaman Reservasi Saya": Menunggu, Aktif, Ditolak, Dibatalkan
    public function history(Request $request)
    {
        $tabs = ['menunggu' => 'pending', 'aktif' => 'approved', 'ditolak' => 'rejected', 'dibatalkan' => 'cancelled'];
        $tab = $request->query('status');

        $reservations = $request->user()->reservations()
            ->with('facility.location')
            ->when(isset($tabs[$tab]), fn ($q) => $q->where('status', $tabs[$tab]))
            ->orderByDesc('reservation_date')
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('reservations.index', [
            'reservations' => $reservations,
            'tab' => isset($tabs[$tab]) ? $tab : null,
            'tabs' => array_keys($tabs),
        ]);
    }

    // Ajukan Reservasi: cari/filter fasilitas -> pilih tanggal -> pilih slot -> tujuan
    public function create(Request $request)
    {
        $filters = $request->validate([
            'q'           => ['nullable', 'string', 'max:100'],
            'type_id'     => ['nullable', 'integer'],
            'location_id' => ['nullable', 'integer'],
            'min_capacity' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'facility'    => ['nullable', 'integer'],
            'date'        => ['nullable', 'date_format:Y-m-d'],
        ]);

        $facilities = Facility::query()
            ->where('status', 'aktif')
            ->with(['type', 'location'])
            ->when($filters['q'] ?? null, fn ($q, $v) => $q->where('name', 'like', '%'.addcslashes($v, '%_\\').'%'))
            ->when($filters['type_id'] ?? null, fn ($q, $v) => $q->where('type_id', $v))
            ->when($filters['location_id'] ?? null, fn ($q, $v) => $q->where('location_id', $v))
            ->when($filters['min_capacity'] ?? null, fn ($q, $v) => $q->where('capacity', '>=', $v))
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        $selected = isset($filters['facility'])
            ? Facility::where('status', 'aktif')->with(['type', 'location'])->find($filters['facility'])
            : null;

        // Tanggal dijepit ke rentang yang boleh dipesan (hari ini s/d hari ini + N hari)
        $today = now()->startOfDay();
        $maxDate = $today->copy()->addDays((int) config('reservation.max_days_ahead'));
        $date = Carbon::parse($filters['date'] ?? $today->toDateString(), config('app.timezone'))->startOfDay();
        $date = $date->lt($today) ? $today : ($date->gt($maxDate) ? $maxDate : $date);

        $board = $selected ? $this->service->slotBoard($selected->id, $date->toDateString()) : [];

        return view('reservations.create', [
            'facilities'  => $facilities,
            'types'       => FacilityType::orderBy('name')->get(),
            'locations'   => \App\Models\Location::orderBy('scope_level')->orderBy('fakultas')->get(),
            'selected'    => $selected,
            'board'       => $board,
            'date'        => $date->toDateString(),
            'minDate'     => $today->toDateString(),
            'maxDate'     => $maxDate->toDateString(),
            'filters'     => $filters,
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
    // PETUGAS (belum direvisi di tahap ini — menyusul setelah sisi pengguna)
    // TODO: approve() wajib pakai $this->service->hasConflict(..., ignoreReservationId)
    //       di dalam DB::transaction + lock; reject()/petugasCancel() tulis status_logs.note
    // ------------------------------------------------------------

    public function approve(Reservation $reservation)
    {
        $reservation->update(['status' => 'approved']);

        return back()->with('status', 'Reservasi disetujui.');
    }

    public function reject(Reservation $reservation)
    {
        $reservation->update(['status' => 'rejected']);

        return back()->with('status', 'Reservasi ditolak. Slot otomatis tersedia kembali.');
    }

    public function petugasCancel(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string'],
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        return back()->with('status', 'Reservasi dibatalkan petugas.');
    }
}
