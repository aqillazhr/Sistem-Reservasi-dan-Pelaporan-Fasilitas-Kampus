<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Facility;
use App\Models\Reservation;
use Carbon\Carbon;

// Cari user pengguna
$user = User::where('role', 'pengguna')->first();
if (!$user) {
    echo "Tidak ada user pengguna.\n";
    exit;
}

echo "Generating reservations for user: {$user->name}...\n";

// Fasilitas aktif
$facilities = Facility::where('status', 'aktif')->take(5)->get();
if ($facilities->isEmpty()) {
    echo "Tidak ada fasilitas aktif.\n";
    exit;
}

$now = now();

// 1. Dibatalkan (masa lalu atau masa depan)
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->addDays(2)->format('Y-m-d'),
    'start_time' => '10:00',
    'end_time' => '12:00',
    'status' => 'cancelled',
    'cancellation_reason' => 'Batal karena jadwal kuliah pengganti.',
    'purpose' => 'Rapat organisasi mahasiswa',
]);

// 2. Ditolak
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->addDays(4)->format('Y-m-d'),
    'start_time' => '08:00',
    'end_time' => '10:00',
    'status' => 'rejected',
    'cancellation_reason' => 'Fasilitas sedang dalam perbaikan dadakan.',
    'purpose' => 'Seminar proposal skripsi',
]);

// 3. Selesai (approved & waktu sudah lewat)
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->subDays(3)->format('Y-m-d'),
    'start_time' => '13:00',
    'end_time' => '15:00',
    'status' => 'approved',
    'purpose' => 'Latihan bulu tangkis',
]);

// 4. Aktif (approved & waktu belum lewat)
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->addDays(1)->format('Y-m-d'),
    'start_time' => '15:00',
    'end_time' => '17:00',
    'status' => 'approved',
    'purpose' => 'Lomba esai tingkat fakultas',
]);

// 5. Menunggu (pending)
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->addDays(5)->format('Y-m-d'),
    'start_time' => '09:00',
    'end_time' => '11:00',
    'status' => 'pending',
    'purpose' => 'Pertemuan klub bahasa Inggris',
]);

// 6. Kedaluwarsa (pending tapi waktu sudah lewat)
Reservation::factory()->create([
    'user_id' => $user->id,
    'facility_id' => $facilities->random()->id,
    'reservation_date' => $now->clone()->subDays(1)->format('Y-m-d'),
    'start_time' => '08:00',
    'end_time' => '09:00',
    'status' => 'pending',
    'purpose' => 'Diskusi kelompok',
]);

echo "Done! 6 realistic reservations created.\n";
