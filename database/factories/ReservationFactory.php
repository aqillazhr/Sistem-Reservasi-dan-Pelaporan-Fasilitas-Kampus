<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /** Slot waktu valid: 07:00-20:00, kelipatan 30 menit. */
    private static array $slots = [
        '07:00', '07:30', '08:00', '08:30', '09:00', '09:30',
        '10:00', '10:30', '11:00', '11:30', '12:00', '12:30',
        '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
        '16:00', '16:30', '17:00', '17:30', '18:00', '18:30',
        '19:00', '19:30', '20:00',
    ];

    private static array $purposes = [
        'Rapat koordinasi program kerja himpunan mahasiswa.',
        'Seminar kewirausahaan untuk mahasiswa semester akhir.',
        'Praktikum mata kuliah Basis Data.',
        'Workshop desain UI/UX tingkat universitas.',
        'Kegiatan orientasi mahasiswa baru.',
        'Tryout CPNS bagi mahasiswa tingkat akhir.',
        'Pertemuan dosen pembimbing dengan mahasiswa bimbingan.',
        'Latihan rutin tim basket universitas.',
        'Pembuatan film pendek untuk tugas akhir.',
        'Sosialisasi program beasiswa dari Kementerian.',
    ];

    public function definition(): array
    {
        // Ambil dua slot acak kemudian pastikan start < end
        $slotKeys   = array_keys(self::$slots);
        $startIdx   = fake()->numberBetween(0, count(self::$slots) - 4);
        $endIdx     = fake()->numberBetween($startIdx + 1, min($startIdx + 6, count(self::$slots) - 1));
        $startTime  = self::$slots[$startIdx];
        $endTime    = self::$slots[$endIdx];

        return [
            'user_id'             => User::where('role', 'pengguna')
                ->where('status', 'verified')
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->pengguna()->create()->id,
            'facility_id'         => Facility::where('status', 'aktif')
                ->whereNull('deleted_at')
                ->inRandomOrder()
                ->first()?->id
                ?? Facility::factory()->create()->id,
            'reservation_date'    => fake()->dateTimeBetween('now', '+60 days')->format('Y-m-d'),
            'start_time'          => $startTime,
            'end_time'            => $endTime,
            'purpose'             => fake()->randomElement(self::$purposes),
            'status'              => 'pending',
            'cancellation_reason' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'              => 'pending',
            'cancellation_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'              => 'approved',
            'cancellation_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'              => 'rejected',
            'cancellation_reason' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status'              => 'cancelled',
            'cancellation_reason' => fake()->randomElement([
                'Kegiatan ditunda oleh panitia.',
                'Peserta tidak memenuhi kuorum.',
                'Terdapat perubahan jadwal mendadak.',
            ]),
        ]);
    }

    /** Reservasi yang sudah lampau (untuk riwayat). */
    public function past(): static
    {
        return $this->state(fn () => [
            'reservation_date' => fake()->dateTimeBetween('-90 days', '-1 day')->format('Y-m-d'),
        ]);
    }
}
