<?php

namespace App\View\Components\Dashboard;

use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Daftar "Antrian Reservasi" (3 item terlama, dengan aksi Setuju/Tolak
 * langsung) di dashboard petugas (Desktop 5). Lihat juga
 * PetugasReservationQueueCard untuk kartu ringkasan angkanya (komponen
 * terpisah karena beda baris tata letak).
 */
class PetugasReservationQueueList extends Component
{
    public $queue;

    public function __construct()
    {
        // Idempoten & murah kalau dipanggil lagi (component kartu di
        // sebelahnya juga memanggil ini) — sengaja tidak mengandalkan urutan
        // render antar component supaya keduanya tetap benar berdiri sendiri.
        app(\App\Services\ReservationService::class)->autoCancelExpired();

        $this->queue = Reservation::with(['user', 'facility.location'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('components.dashboard.petugas-reservation-queue-list');
    }
}
