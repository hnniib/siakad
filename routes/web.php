<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', function () {
        return 'REGISTER ROUTE AKTIF';
    })->name('register');

    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::get('/dashboard', function () {
    return 'Dashboard sementara berhasil';
})->name('dashboard');