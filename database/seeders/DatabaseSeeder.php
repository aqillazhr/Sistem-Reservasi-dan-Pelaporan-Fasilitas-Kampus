<?php

namespace Database\Seeders;

use App\Models\FacilityType;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin default supaya tim bisa langsung login dan coba fitur
        // CRUD akun/fasilitas tanpa harus insert manual ke DB.
        User::firstOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Admin PPK',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'user_type' => null,
                'status' => 'verified',
                'account_status' => 'aktif',
            ]
        );

        // Data master jenis fasilitas sesuai deskripsi project.
        foreach (['Ruang Kelas', 'Aula', 'Laboratorium', 'Alat', 'Lapangan'] as $type) {
            FacilityType::firstOrCreate(['name' => $type]);
        }

        // Contoh lokasi tingkat universitas, supaya ada minimal 1 lokasi
        // untuk dipakai saat testing tambah fasilitas pertama.
        // Ingat: relasi locations<->facilities mandatory-mandatory, jadi
        // idealnya tiap location baru langsung dibuatkan fasilitas pertamanya
        // (lihat README-database.pdf, Asumsi Perancangan poin 3 & 11).
        Location::firstOrCreate([
            'scope_level' => 'universitas',
            'fakultas' => null,
            'gedung' => null,
            'ruangan' => null,
        ]);
    }
}
