<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KhsController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\MataKuliahController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // route yang dipakai dashboard mahasiswa / dosen
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

    Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
    Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
    Route::delete('/krs/{krs}', [KrsController::class, 'destroy'])->name('krs.destroy');

    Route::get('/khs', [KhsController::class, 'index'])->name('khs.index');

    Route::get('/krs-masuk', [KrsController::class, 'indexForDosen'])->name('krs.indexForDosen');

    Route::get('/perkuliahan', [MataKuliahController::class, 'index'])->name('perkuliahan.index');
    Route::post('/perkuliahan', [MataKuliahController::class, 'store'])->name('perkuliahan.store');
    Route::put('/perkuliahan/{mataKuliah}', [MataKuliahController::class, 'update'])->name('perkuliahan.update');
    Route::delete('/perkuliahan/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('perkuliahan.destroy');

    Route::get('/perkuliahan/{mataKuliah}/nilai', [KhsController::class, 'inputForm'])->name('khs.inputForm');
    Route::post('/perkuliahan/nilai/{krs}', [KhsController::class, 'store'])->name('khs.store');
});