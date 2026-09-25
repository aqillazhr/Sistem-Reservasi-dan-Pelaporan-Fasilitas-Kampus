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
        $now    = now();

        $base = Reservation::where('user_id', $userId);

        // Hitung per status DB (pending, rejected, cancelled)
        $byStatus = (clone $base)->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Aktif = approved yang belum selesai
        $aktif = (clone $base)->where('status', 'approved')
            ->where(fn ($q) => $q
                ->whereDate('reservation_date', '>', $now->toDateString())
                ->orWhere(fn ($q2) => $q2
                    ->whereDate('reservation_date', $now->toDateString())
                    ->where('end_time', '>', $now->format('H:i:s'))
                )
            )->count();

        // Selesai = approved yang sudah lewat
        $selesai = (clone $base)->where('status', 'approved')
            ->where(fn ($q) => $q
                ->whereDate('reservation_date', '<', $now->toDateString())
                ->orWhere(fn ($q2) => $q2
                    ->whereDate('reservation_date', $now->toDateString())
                    ->where('end_time', '<=', $now->format('H:i:s'))
                )
            )->count();

        $counts = [
            'pending'    => $byStatus['pending']   ?? 0,
            'aktif'      => $aktif,
            'selesai'    => $selesai,
            'rejected'   => $byStatus['rejected']  ?? 0,
            'cancelled'  => $byStatus['cancelled'] ?? 0,
        ];

        return view('reservations.hub', compact('counts'));
    }

    // "Halaman Reservasi Saya" — semua dimuat sekaligus, tab filter di client-side
    public function history(Request $request)
    {
        $reservations = $request->user()->reservations()
            ->with('facility.location')
            ->orderByDesc('reservation_date')
            ->orderByDesc('start_time')
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

        return response()->json([
            'data' => $facilities->map(fn ($f) => [
                'id'       => $f->id,
                'name'     => $f->name,
                'type'     => $f->type->name ?? '',
                'capacity' => $f->capacity,
                'location' => collect([$f->location->ruangan, $f->location->gedung, $f->location->fakultas])
                                ->filter()->implode(', ') ?: 'Universitas',
            ]),
            'next_page_url'     => $facilities->nextPageUrl(),
            'prev_page_url'     => $facilities->previousPageUrl(),
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

        return response()->json([
            'facility' => [
                'id'       => $facility->id,
                'name'     => $facility->name,
                'type'     => $facility->type->name ?? '',
                'capacity' => $facility->capacity,
                'location' => collect([$facility->location->ruangan, $facility->location->gedung, $facility->location->fakultas])
                                ->filter()->implode(', ') ?: 'Universitas',
                'description' => $facility->description,
            ],
            'date'     => $date->toDateString(),
            'minDate'  => $today->toDateString(),
            'maxDate'  => $maxDate->toDateString(),
            'board'    => $board,
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
