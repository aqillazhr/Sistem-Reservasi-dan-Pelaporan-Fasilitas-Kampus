<?php

namespace App\View\Components\Dashboard;

use App\Models\Reservation;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Ringkasan reservasi di dashboard pengguna (Orang 3).
 * Dibuat sebagai component supaya tidak menyentuh DashboardController
 * (menghindari conflict dengan Orang 1 & Orang 4).
 */
class ReservationSummary extends Component
{
    public $upcoming;
    public $pending;

    public function __construct()
    {
        $userId = auth()->id();

        $this->pending = Reservation::where('user_id', $userId)->where('status', 'pending')->count();
        $this->upcoming = Reservation::with('facility')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->where('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('components.dashboard.reservation-summary');
    }
}
