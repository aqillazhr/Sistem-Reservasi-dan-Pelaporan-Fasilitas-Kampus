<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityPhoto;
use Illuminate\Database\Seeder;

class FacilityPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $photos = [
            'Ruang Kelas A101' => 'facilities/ruang-kelas-a101.jpg',
            'Ruang Kelas A102' => 'facilities/ruang-kelas-a102.jpg',
            'Ruang Kelas B201' => 'facilities/ruang-kelas-b201.jpeg',
            'Lab Komputer 1' => 'facilities/lab-komputer-fix1.jpg',
            'Lab Komputer 2' => 'facilities/lab-komputer2.jpg',
            'Lab Jaringan' => 'facilities/lab-jaringan.jpeg',
            'Lab Fisika Dasar' => 'facilities/lab-fisdas.jpg',
            'Aula Utama Rektorat' => 'facilities/aula-utama-rektorat.jpg',
            'Aula Fakultas Teknik' => 'facilities/aula-ft.png',
            'Aula Serbaguna FEB' => 'facilities/aula-serbaguna-feb.jpg',
            'Lapangan Basket Universitas' => 'facilities/Lapangan-Basket.jpg',
            'Lapangan Futsal' => 'facilities/lapangan-futsal.jpg',
            'Lapangan Voli' => 'facilities/lapangan-voli.png',
            'Proyektor Portabel A' => 'facilities/proyektor-portable-aula.jpg',
            'Sound System Aula' => 'facilities/sound-system-aula.png',
        ];

        foreach ($photos as $facilityName => $filePath) {
            $facility = Facility::where('name', $facilityName)->first();

            if ($facility) {
                FacilityPhoto::updateOrCreate(
                    [
                        'facility_id' => $facility->id,
                        'file_path' => $filePath,
                    ],
                    [
                        'is_primary' => true,
                    ]
                );
            }
        }
    }
}