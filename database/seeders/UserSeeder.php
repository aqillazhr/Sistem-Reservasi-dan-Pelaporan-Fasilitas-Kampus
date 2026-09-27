<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Akun tetap (login credentials yang bisa dibagikan ke tim) ─────────
        $fixed = [
            [
                // Akun "Sistem": DIPAKAI SEBAGAI PELAKU DI status_logs untuk
                // perubahan status otomatis (misal reservasi pending yang
                // lewat waktu booking-nya otomatis dibatalkan sebelum sempat
                // diproses petugas — lihat ReservationService::autoCancelExpired()).
                // JANGAN dibagikan sebagai kredensial login: password acak
                // dan tidak pernah dicatat di mana pun, akun ini murni jadi
                // "pelaku" pencatatan, bukan untuk dipakai manusia login.
                'name'           => 'Sistem',
                'email'          => config('reservation.system_account_email'),
                'password'       => Hash::make(Str::random(40)),
                'role'           => 'admin',
                'user_type'      => null,
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Admin PPK',
                'email'          => 'admin@kampus.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'admin',
                'user_type'      => null,
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Petugas Satu',
                'email'          => 'petugas1@kampus.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'petugas',
                'user_type'      => null,
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Petugas Dua',
                'email'          => 'petugas2@kampus.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'petugas',
                'user_type'      => null,
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Budi Mahasiswa',
                'email'          => 'budi@mahasiswa.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'pengguna',
                'user_type'      => 'mahasiswa',
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Dewi Dosen',
                'email'          => 'dewi@dosen.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'pengguna',
                'user_type'      => 'dosen',
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
            [
                'name'           => 'Andi Staf',
                'email'          => 'andi@staf.ac.id',
                'password'       => Hash::make('password'),
                'role'           => 'pengguna',
                'user_type'      => 'staf',
                'status'         => 'verified',
                'account_status' => 'aktif',
            ],
        ];

        foreach ($fixed as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }

        // ─── Pengguna acak ─────────────────────────────────────────────────────
        // Pengguna terverifikasi (mahasiswa/dosen/staf)
        User::factory()->pengguna()->count(20)->create();

        // Pengguna yang pending verifikasi
        User::factory()->pending()->count(5)->create();

        // Pengguna yang ditolak
        User::factory()->rejected()->count(3)->create();

        // Pengguna yang dinonaktifkan
        User::factory()->nonaktif()->count(2)->create();

        $this->command->info('User: Selesai seed akun tetap + 30 akun acak.');
    }
}
