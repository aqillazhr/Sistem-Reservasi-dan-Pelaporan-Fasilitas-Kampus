<?php

namespace App\View\Components\Dashboard;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Kartu kecil "Reservasi Menunggu: N" di baris kartu ringkasan dashboard
 * petugas (Desktop 5). Lihat juga PetugasReservationQueueList untuk daftar
 * antriannya (komponen terpisah karena beda baris tata letak).
 */
class PetugasReservationQueueCard extends Component
{
    public $pendingCount;

    public function __construct()
    {
        app(ReservationService::class)->autoCancelExpired();

        $this->pendingCount = Reservation::where('status', 'pending')->count();
    }

    public function render(): View
    {
        return view('components.dashboard.petugas-reservation-queue-card');
    }
}
