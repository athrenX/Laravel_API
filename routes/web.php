<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityController; // Pastikan ini sudah ada

// Route login admin
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Group route untuk admin yang sudah login dan role admin
Route::middleware(['auth', 'is_admin'])->prefix('admin')->as('admin.')->group(function () {

    // Halaman dashboard admin
    Route::get('/home', function () {
        return view('admin.home');
    })->name('home');

    // Resource routes untuk aktivitas (CRUD lengkap)
    Route::resource('activities', ActivityController::class);

    // Routes Kendaraan
    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::get('/kendaraan/create', [KendaraanController::class, 'create'])->name('kendaraan.create');
    Route::post('/kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store');

    // Routes Lokasi
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::get('/lokasi/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

    // Routes Destinasi
    Route::get('/destinasi', [DestinasiController::class, 'adminIndex'])->name('destinasi.index');
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('destinasi.create');
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('destinasi.store');
    Route::get('/destinasi/{id}/edit', [DestinasiController::class, 'edit'])->name('destinasi.edit');
    Route::put('/destinasi/{id}', [DestinasiController::class, 'update'])->name('destinasi.update');
    Route::delete('/destinasi/{id}', [DestinasiController::class, 'destroy'])->name('destinasi.destroy');
});
