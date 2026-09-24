<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /** Gedung fiktif di kampus. */
    private static array $gedung = [
        'Gedung A', 'Gedung B', 'Gedung C', 'Gedung D',
        'Gedung Rektorat', 'Gedung Perpustakaan', 'Gedung Olahraga',
    ];

    /** Daftar fakultas fiktif. */
    private static array $fakultas = [
        'Fakultas Teknik',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Ilmu Sosial dan Ilmu Politik',
        'Fakultas Hukum',
        'Fakultas Kedokteran',
        'Fakultas Keguruan dan Ilmu Pendidikan',
        'Fakultas MIPA',
    ];

    public function definition(): array
    {
        // Default: lokasi tingkat fakultas
        $fakultas = fake()->randomElement(self::$fakultas);

        return [
            'scope_level' => 'fakultas',
            'fakultas'    => $fakultas,
            'prodi'       => fake()->optional(0.6)->randomElement([
                'Teknik Informatika', 'Sistem Informasi', 'Manajemen',
                'Akuntansi', 'Hukum', 'Pendidikan Matematika',
            ]),
            'gedung'      => fake()->optional(0.8)->randomElement(self::$gedung),
            'ruangan'     => fake()->optional(0.7)->numerify('Ruang ###'),
        ];
    }

    /** Lokasi tingkat universitas (fasilitas umum kampus). */
    public function universitas(): static
    {
        return $this->state(fn () => [
            'scope_level' => 'universitas',
            'fakultas'    => null,
            'prodi'       => null,
            'gedung'      => fake()->optional(0.8)->randomElement(self::$gedung),
            'ruangan'     => fake()->optional(0.5)->numerify('Ruang ###'),
        ]);
    }

    /** Lokasi outdoor (tanpa gedung/ruangan). */
    public function outdoor(): static
    {
        return $this->state(fn () => [
            'gedung'  => null,
            'ruangan' => null,
        ]);
    }
}
