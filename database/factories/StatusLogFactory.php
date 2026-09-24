<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StatusLog>
 */
class StatusLogFactory extends Factory
{
    public function definition(): array
    {
        // Default: log untuk reservasi
        $reservation = Reservation::inRandomOrder()->first();

        return [
            'reservation_id'    => $reservation?->id,
            'report_id'         => null,
            'facility_id'       => null,
            'changed_by_user_id' => User::whereIn('role', ['petugas', 'admin'])
                ->where('status', 'verified')
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->petugas()->create()->id,
            'old_status'        => 'pending',
            'new_status'        => fake()->randomElement(['approved', 'rejected']),
            'note'              => fake()->optional(0.5)->sentence(),
        ];
    }

    /** Log untuk perubahan status reservasi. */
    public function forReservation(Reservation $reservation, string $old, string $new): static
    {
        return $this->state(fn () => [
            'reservation_id' => $reservation->id,
            'report_id'      => null,
            'facility_id'    => null,
            'old_status'     => $old,
            'new_status'     => $new,
        ]);
    }

    /** Log untuk perubahan status laporan. */
    public function forReport(Report $report, string $old, string $new): static
    {
        return $this->state(fn () => [
            'reservation_id' => null,
            'report_id'      => $report->id,
            'facility_id'    => null,
            'old_status'     => $old,
            'new_status'     => $new,
        ]);
    }

    /** Log untuk perubahan status fasilitas. */
    public function forFacility(Facility $facility, string $old, string $new): static
    {
        return $this->state(fn () => [
            'reservation_id' => null,
            'report_id'      => null,
            'facility_id'    => $facility->id,
            'old_status'     => $old,
            'new_status'     => $new,
        ]);
    }
}
