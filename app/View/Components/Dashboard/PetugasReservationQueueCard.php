<?php

namespace App\View\Components\Dashboard;

use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;


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
