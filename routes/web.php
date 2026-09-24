<?php

use App\Http\Controllers\Auth\AccountManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Facility\FacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Reservation\ReservationController;
use Illuminate\Support\Facades\Route;

// ==========================================================
// PUBLIC (Pengunjung, tanpa login)
// ==========================================================
// Landing: tamu diarahkan ke login, yang sudah login langsung ke dashboard sesuai role.
// Pengunjung tetap bisa melihat daftar fasilitas lewat /fasilitas (tanpa login).
Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return redirect()->route(match ($user->role) {
        'admin' => 'admin.dashboard',
        'petugas' => 'petugas.dashboard',
        default => 'pengguna.dashboard',
    });
})->name('home');
Route::get('/fasilitas', [FacilityController::class, 'index'])->name('facilities.index');

Route::get('/fasilitas/fakultas/{faculty}', [FacilityController::class, 'byFaculty'])->name('facilities.by-faculty');
Route::get('/fasilitas/gedung/{building}', [FacilityController::class, 'byBuilding'])->name('facilities.by-building');

Route::get('/fasilitas/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

// Data slot terpakai (Orang 3 -> dipakai kalender availability Orang 2). Read-only, tanpa data pemohon.
Route::get('/fasilitas/{facility}/slot', [ReservationController::class, 'slots'])
    ->middleware('throttle:60,1')
    ->name('facilities.slots');

// ==========================================================
// AUTH (Orang 1)
// ==========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil (Orang 1) — bisa diakses semua role yang sudah login
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

// ==========================================================
// PENGGUNA (login: mahasiswa/dosen/staf)
// ==========================================================
Route::middleware(['auth', 'role:pengguna'])->prefix('app')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pengguna'])->name('dashboard');

    // Reservasi (Orang 3) — urutan penting: /saya & /create HARUS sebelum /{reservation}
    Route::get('/reservasi', [ReservationController::class, 'hub'])->name('reservations.hub');
    Route::get('/reservasi/saya', [ReservationController::class, 'history'])->name('reservations.index');
    Route::get('/reservasi/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservasi', [ReservationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reservations.store');
    Route::get('/reservasi/{reservation}', [ReservationController::class, 'show'])
        ->whereNumber('reservation')
        ->name('reservations.show');
    Route::post('/reservasi/{reservation}/batal', [ReservationController::class, 'cancel'])
        ->whereNumber('reservation')
        ->name('reservations.cancel');

    // Laporan (Orang 4)
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/laporan/draft', [ReportController::class, 'storeDraft'])->name('reports.store-draft');
    Route::get('/laporan/draft', [ReportController::class, 'drafts'])->name('reports.drafts');
    Route::delete('/laporan/{report}/foto/{photo}',[ReportController::class, 'deletePhoto'])->name('reports.delete-photo');
    Route::get('/laporan/{report}/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/laporan/{report}/edit', [ReportController::class, 'editDraft'])->name('reports.edit');
    Route::put('/laporan/{report}/draft', [ReportController::class, 'updateDraft'])->name('reports.update-draft');
    Route::post('/laporan/{report}/submit', [ReportController::class, 'submitDraft'])->name('reports.submit');
    Route::get('/laporan/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// ==========================================================
// PETUGAS
// ==========================================================
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');

    // Reservasi (Orang 3)
    Route::post('/reservasi/{reservation}/approve', [ReservationController::class, 'approve'])->name('reservations.approve');
    Route::post('/reservasi/{reservation}/reject', [ReservationController::class, 'reject'])->name('reservations.reject');
    Route::post('/reservasi/{reservation}/batal', [ReservationController::class, 'petugasCancel'])->name('reservations.petugas-cancel');

    // Laporan (Orang 4)
    Route::patch('/laporan/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');
    Route::post('/laporan/{report}/dalam-perbaikan', [ReportController::class, 'markUnderRepair'])->name('reports.mark-under-repair');
    Route::post('/laporan/{report}/selesai-perbaikan', [ReportController::class, 'markFixed'])->name('reports.mark-fixed');
});

// ==========================================================
// ADMIN
// ==========================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Akun (Orang 1)
    Route::get('/akun', [AccountManagementController::class, 'index'])->name('accounts.index');
    Route::post('/akun', [AccountManagementController::class, 'store'])->name('accounts.store');
    Route::get('/akun/{user}', [AccountManagementController::class, 'show'])->name('accounts.show');
    Route::post('/akun/{user}/verify', [AccountManagementController::class, 'verify'])->name('accounts.verify');
    Route::post('/akun/{user}/reject', [AccountManagementController::class, 'reject'])->name('accounts.reject');
    Route::get('/akun/{user}/edit', [AccountManagementController::class, 'edit'])->name('accounts.edit');
    Route::put('/akun/{user}', [AccountManagementController::class, 'update'])->name('accounts.update');
    Route::post('/akun/{user}/toggle-active', [AccountManagementController::class, 'toggleActive'])->name('accounts.toggle-active');

    // Fasilitas (Orang 2)
    Route::post('/fasilitas', [FacilityController::class, 'store'])->name('facilities.store');
    Route::put('/fasilitas/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
});