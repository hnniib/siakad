<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MataKuliahController;
use Illuminate\Support\Facades\Route;

// ---------- Guest ----------
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// ---------- Authenticated ----------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Jadwal (semua role)
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

    // ---------- Mahasiswa ----------
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
        Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
        Route::delete('/krs/{krs}', [KrsController::class, 'destroy'])->name('krs.destroy');

        Route::get('/khs', [KhsController::class, 'index'])->name('khs.index');
    });

    // ---------- Dosen ----------
    Route::middleware('role:dosen')->group(function () {
        Route::get('/krs-masuk', [KrsController::class, 'indexForDosen'])->name('krs.indexForDosen');
        Route::patch('/krs/{krs}/status', [KrsController::class, 'updateStatus'])->name('krs.updateStatus');

        Route::get('/perkuliahan', [MataKuliahController::class, 'index'])->name('perkuliahan.index');
        Route::post('/perkuliahan', [MataKuliahController::class, 'store'])->name('perkuliahan.store');
        Route::put('/perkuliahan/{mataKuliah}', [MataKuliahController::class, 'update'])->name('perkuliahan.update');
        Route::delete('/perkuliahan/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('perkuliahan.destroy');

        Route::get('/perkuliahan/{mataKuliah}/nilai', [KhsController::class, 'inputForm'])->name('khs.inputForm');
        Route::post('/perkuliahan/nilai/{krs}', [KhsController::class, 'store'])->name('khs.store');
    });

    // ---------- Dosen & Admin ----------
    Route::middleware('role:dosen,admin')->group(function () {
        Route::post('/jadwal/generate', [JadwalController::class, 'generate'])->name('jadwal.generate');
        Route::delete('/jadwal/hapus', [JadwalController::class, 'hapus'])->name('jadwal.hapus');
    });
});