<?php

use App\Http\Controllers\Auth\AccountManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Facility\FacilityController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Reservation\ReservationController;
use Illuminate\Support\Facades\Route;

// ==========================================================
// PUBLIC (Pengunjung, tanpa login)
// ==========================================================
Route::get('/', [FacilityController::class, 'index'])->name('home');
Route::get('/fasilitas', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/fasilitas/fakultas/{faculty}', [FacilityController::class, 'byFaculty'])->name('facilities.by-faculty');
Route::get('/fasilitas/{facility}', [FacilityController::class, 'show'])->name('facilities.show');

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
});

// ==========================================================
// PENGGUNA (login: mahasiswa/dosen/staf)
// ==========================================================
Route::middleware(['auth', 'role:pengguna'])->prefix('app')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pengguna'])->name('dashboard');

    // Reservasi (Orang 3)
    Route::post('/reservasi', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservasi/{reservation}/batal', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Laporan (Orang 4)
    Route::post('/laporan', [ReportController::class, 'store'])->name('reports.store');
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
    Route::post('/akun/{user}/verify', [AccountManagementController::class, 'verify'])->name('accounts.verify');
    Route::post('/akun/{user}/reject', [AccountManagementController::class, 'reject'])->name('accounts.reject');
    Route::post('/akun/{user}/toggle-active', [AccountManagementController::class, 'toggleActive'])->name('accounts.toggle-active');

    // Fasilitas (Orang 2)
    Route::get('/fasilitas/create', [FacilityController::class, 'create'])
        ->name('facilities.create');
    Route::post('/fasilitas', [FacilityController::class, 'store'])->name('facilities.store');
    Route::put('/fasilitas/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
});
