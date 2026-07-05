<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\KrsController;
use Illuminate\Support\Facades\Route;

// ---------- Guest (belum login) ----------
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ---------- Authenticated (semua role) ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard tunggal, tapi tampilan berbeda otomatis sesuai role (lihat DashboardController)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Jadwal kuliah/ujian - bisa diakses semua role, isi berbeda per role
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

    // ---------- Khusus Mahasiswa ----------
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
        Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
        Route::delete('/krs/{krs}', [KrsController::class, 'destroy'])->name('krs.destroy');

        Route::get('/khs', [KhsController::class, 'index'])->name('khs.index');
    });

    // ---------- Khusus Dosen ----------
    Route::middleware('role:dosen')->group(function () {
        Route::get('/krs-masuk', [KrsController::class, 'indexForDosen'])->name('krs.indexForDosen');
        Route::patch('/krs/{krs}/status', [KrsController::class, 'updateStatus'])->name('krs.updateStatus');

        Route::get('/perkuliahan', [\App\Http\Controllers\MataKuliahController::class, 'index'])->name('perkuliahan.index');
        Route::post('/perkuliahan', [\App\Http\Controllers\MataKuliahController::class, 'store'])->name('perkuliahan.store');
        Route::put('/perkuliahan/{mataKuliah}', [\App\Http\Controllers\MataKuliahController::class, 'update'])->name('perkuliahan.update');
        Route::delete('/perkuliahan/{mataKuliah}', [\App\Http\Controllers\MataKuliahController::class, 'destroy'])->name('perkuliahan.destroy');

        Route::get('/perkuliahan/{mataKuliah}/nilai', [KhsController::class, 'inputForm'])->name('khs.inputForm');
        Route::post('/perkuliahan/nilai/{krs}', [KhsController::class, 'store'])->name('khs.store');
    });

    // ---------- Dosen & Admin: jalankan Sistem Cerdas Penjadwalan Otomatis ----------
    Route::middleware('role:dosen,admin')->group(function () {
        Route::post('/jadwal/generate', [JadwalController::class, 'generate'])->name('jadwal.generate');
        Route::delete('/jadwal/hapus', [JadwalController::class, 'hapus'])->name('jadwal.hapus');
    });
});