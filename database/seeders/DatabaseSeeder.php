<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Master data & akun wajib ada lebih dulu
            FacilityTypeSeeder::class,
            UserSeeder::class,

            // 2. Fasilitas + lokasi (FK ke facility_types)
            FacilitySeeder::class,

            // 3. Transaksi (FK ke users & facilities)
            ReservationSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
