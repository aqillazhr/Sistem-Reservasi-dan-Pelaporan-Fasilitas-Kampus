<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityType;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    private static array $namaFasilitas = [
        'Ruang Kelas'   => ['Ruang A101', 'Ruang B201', 'Ruang C301', 'Ruang Kuliah Besar', 'Ruang Tutorial'],
        'Aula'          => ['Aula Utama', 'Aula Serbaguna', 'Aula Fakultas', 'Aula Rektorat'],
        'Laboratorium'  => ['Lab Komputer 1', 'Lab Komputer 2', 'Lab Jaringan', 'Lab Fisika', 'Lab Kimia', 'Lab Biologi'],
        'Alat'          => ['Projektor Portabel', 'Sound System', 'Kamera DSLR', 'Drone', 'LCD Display'],
        'Lapangan'      => ['Lapangan Basket', 'Lapangan Voli', 'Lapangan Futsal', 'Lapangan Badminton', 'Lapangan Upacara'],
    ];

    public function definition(): array
    {
        $type = FacilityType::inRandomOrder()->first()
            ?? FacilityType::factory()->create();

        $namaPool = self::$namaFasilitas[$type->name] ?? ['Fasilitas Umum'];

        return [
            'name'        => fake()->unique()->randomElement($namaPool).' '.fake()->numerify('#'),
            'type_id'     => $type->id,
            'location_id' => Location::inRandomOrder()->first()?->id
                ?? Location::factory()->create()->id,
            'capacity'    => fake()->numberBetween(10, 200),
            'description' => fake()->optional(0.7)->paragraph(),
            'status'      => 'aktif',
        ];
    }

    /** Fasilitas aktif dan bisa dipesan. */
    public function aktif(): static
    {
        return $this->state(fn () => ['status' => 'aktif']);
    }

    /** Fasilitas sedang dalam perbaikan. */
    public function dalamPerbaikan(): static
    {
        return $this->state(fn () => ['status' => 'dalam perbaikan']);
    }

    /** Fasilitas nonaktif/diarsipkan. */
    public function nonaktif(): static
    {
        return $this->state(fn () => ['status' => 'nonaktif']);
    }

    /** Soft-deleted facility. */
    public function deleted(): static
    {
        return $this->state(fn () => ['deleted_at' => now()]);
    }
}
