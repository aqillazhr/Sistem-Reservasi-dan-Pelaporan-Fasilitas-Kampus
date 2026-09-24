<?php

namespace Database\Seeders;

use App\Models\FacilityType;
use Illuminate\Database\Seeder;

class FacilityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Ruang Kelas',
            'Aula',
            'Laboratorium',
            'Alat',
            'Lapangan',
        ];

        foreach ($types as $type) {
            FacilityType::firstOrCreate(['name' => $type]);
        }

        $this->command->info('FacilityType: '.count($types).' jenis fasilitas berhasil di-seed.');
    }
}
