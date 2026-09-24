<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityPhoto;
use App\Models\FacilityType;
use App\Models\Location;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        // ─── Helper: firstOrCreate lokasi ──────────────────────────────────────
        $loc = fn (array $data) => Location::firstOrCreate($data)->id;

        // ─── Tipe fasilitas ────────────────────────────────────────────────────
        $types = FacilityType::pluck('id', 'name');

        // ─── Fasilitas tetap (name unik sebagai key) ───────────────────────────
        $fixedFacilities = [

            // ── Ruang Kelas ────────────────────────────────────────────────────
            [
                'name'        => 'Ruang Kelas A101',
                'type_id'     => $types['Ruang Kelas'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => 'Teknik Informatika', 'gedung' => 'Gedung A', 'ruangan' => 'Ruang 101']),
                'capacity'    => 40,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Ruang Kelas A102',
                'type_id'     => $types['Ruang Kelas'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => 'Teknik Informatika', 'gedung' => 'Gedung A', 'ruangan' => 'Ruang 102']),
                'capacity'    => 40,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Ruang Kelas B201',
                'type_id'     => $types['Ruang Kelas'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => null, 'gedung' => 'Gedung B', 'ruangan' => 'Ruang 201']),
                'capacity'    => 35,
                'status'      => 'dalam perbaikan',
            ],

            // ── Laboratorium ───────────────────────────────────────────────────
            [
                'name'        => 'Lab Komputer 1',
                'type_id'     => $types['Laboratorium'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => 'Teknik Informatika', 'gedung' => 'Gedung A', 'ruangan' => 'Lab Komputer 1']),
                'capacity'    => 30,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Lab Komputer 2',
                'type_id'     => $types['Laboratorium'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => 'Teknik Informatika', 'gedung' => 'Gedung A', 'ruangan' => 'Lab Komputer 2']),
                'capacity'    => 30,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Lab Jaringan',
                'type_id'     => $types['Laboratorium'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => null, 'gedung' => 'Gedung B', 'ruangan' => 'Lab Jaringan']),
                'capacity'    => 20,
                'status'      => 'nonaktif',
            ],
            [
                'name'        => 'Lab Fisika Dasar',
                'type_id'     => $types['Laboratorium'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas MIPA', 'prodi' => null, 'gedung' => 'Gedung D', 'ruangan' => 'Lab Fisika']),
                'capacity'    => 25,
                'status'      => 'aktif',
            ],

            // ── Aula ───────────────────────────────────────────────────────────
            [
                'name'        => 'Aula Utama Rektorat',
                'type_id'     => $types['Aula'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => 'Gedung Rektorat', 'ruangan' => 'Aula Utama']),
                'capacity'    => 500,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Aula Fakultas Teknik',
                'type_id'     => $types['Aula'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Teknik', 'prodi' => null, 'gedung' => 'Gedung B', 'ruangan' => 'Aula Lt. 1']),
                'capacity'    => 150,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Aula Serbaguna FEB',
                'type_id'     => $types['Aula'],
                'location_id' => $loc(['scope_level' => 'fakultas', 'fakultas' => 'Fakultas Ekonomi dan Bisnis', 'prodi' => 'Manajemen', 'gedung' => 'Gedung C', 'ruangan' => 'Ruang Seminar C301']),
                'capacity'    => 120,
                'status'      => 'aktif',
            ],

            // ── Lapangan ───────────────────────────────────────────────────────
            [
                'name'        => 'Lapangan Basket Universitas',
                'type_id'     => $types['Lapangan'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => null, 'ruangan' => 'Lapangan Basket']),
                'capacity'    => 50,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Lapangan Futsal',
                'type_id'     => $types['Lapangan'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => null, 'ruangan' => 'Lapangan Futsal']),
                'capacity'    => 30,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Lapangan Voli',
                'type_id'     => $types['Lapangan'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => null, 'ruangan' => 'Lapangan Voli']),
                'capacity'    => 24,
                'status'      => 'aktif',
            ],

            // ── Alat ───────────────────────────────────────────────────────────
            [
                'name'        => 'Proyektor Portabel A',
                'type_id'     => $types['Alat'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => 'Gedung Rektorat', 'ruangan' => 'Ruang Penyimpanan Alat']),
                'capacity'    => 1,
                'status'      => 'aktif',
            ],
            [
                'name'        => 'Sound System Aula',
                'type_id'     => $types['Alat'],
                'location_id' => $loc(['scope_level' => 'universitas', 'fakultas' => null, 'gedung' => 'Gedung Rektorat', 'ruangan' => 'Ruang Penyimpanan Alat']),
                'capacity'    => 1,
                'status'      => 'aktif',
            ],
        ];

        foreach ($fixedFacilities as $data) {
            $data['description'] ??= null;
            Facility::firstOrCreate(['name' => $data['name']], $data);
        }

        // ─── Foto fasilitas (dummy path) ───────────────────────────────────────
        Facility::whereNull('deleted_at')->get()->each(function (Facility $facility) {
            if ($facility->photos()->count() === 0) {
                FacilityPhoto::create([
                    'facility_id' => $facility->id,
                    'file_path'   => 'facility-photos/dummy-'.$facility->id.'.jpg',
                ]);
            }
        });

        $this->command->info('Facility: '.count($fixedFacilities).' fasilitas tetap + foto dummy berhasil di-seed.');
    }
}
