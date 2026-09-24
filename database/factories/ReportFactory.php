<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    private static array $categories = [
        'Kerusakan AC',
        'Kerusakan Proyektor',
        'Kerusakan Kursi/Meja',
        'Kebersihan Ruangan',
        'Kerusakan Pintu/Jendela',
        'Gangguan Listrik',
        'Kerusakan Papan Tulis',
        'Kerusakan Perangkat Komputer',
        'Kerusakan Toilet',
        'Lainnya',
    ];

    private static array $descriptions = [
        'AC di ruangan ini tidak dingin, kemungkinan freon habis atau kompresor rusak.',
        'Proyektor tidak dapat menyala saat tombol power ditekan.',
        'Beberapa kursi kaki belakangnya patah dan berbahaya bagi pengguna.',
        'Ruangan kotor dan tidak ada fasilitas tempat sampah yang memadai.',
        'Engsel pintu rusak sehingga pintu tidak dapat ditutup dengan benar.',
        'Saklar lampu di sudut ruangan tidak berfungsi.',
        'Papan tulis bermasalah, tulisan tidak dapat terhapus dengan bersih.',
        'Komputer nomor 5 tidak bisa booting, layar hitam terus.',
        'Toilet dalam kondisi tersumbat dan berbau tidak sedap.',
        'Terdapat kerusakan yang perlu segera ditangani.',
    ];

    private static array $resolutionNotes = [
        'Teknisi sudah melakukan pengecekan dan penggantian komponen.',
        'Laporan telah diverifikasi dan tim pemeliharaan segera ditugaskan.',
        'Masalah telah diselesaikan. Fasilitas kembali beroperasi normal.',
        'Laporan ditolak karena kondisi fasilitas sudah memadai saat dicek.',
    ];

    public function definition(): array
    {
        return [
            'user_id'         => User::where('role', 'pengguna')
                ->where('status', 'verified')
                ->inRandomOrder()
                ->first()?->id
                ?? User::factory()->pengguna()->create()->id,
            'facility_id'     => Facility::whereNull('deleted_at')
                ->inRandomOrder()
                ->first()?->id
                ?? Facility::factory()->create()->id,
            'category'        => fake()->randomElement(self::$categories),
            'description'     => fake()->randomElement(self::$descriptions),
            'status'          => 'baru',
            'resolution_note' => null,
            'resolved_at'     => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status'          => 'draft',
            'description'     => null,
            'resolution_note' => null,
            'resolved_at'     => null,
        ]);
    }

    public function baru(): static
    {
        return $this->state(fn () => [
            'status'          => 'baru',
            'resolution_note' => null,
            'resolved_at'     => null,
        ]);
    }

    public function diproses(): static
    {
        return $this->state(fn () => [
            'status'          => 'diproses',
            'resolution_note' => null,
            'resolved_at'     => null,
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn () => [
            'status'          => 'selesai',
            'resolution_note' => fake()->randomElement(self::$resolutionNotes),
            'resolved_at'     => now()->subHours(fake()->numberBetween(1, 72)),
        ]);
    }

    public function ditolak(): static
    {
        return $this->state(fn () => [
            'status'          => 'ditolak',
            'resolution_note' => 'Laporan ditolak karena kondisi fasilitas sudah memadai saat dicek.',
            'resolved_at'     => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
