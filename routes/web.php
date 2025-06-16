<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\ReviewController; // <--- BARIS INI DITAMBAHKAN!

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Web Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Admin Login & Logout Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Group for Authenticated Admin Users
Route::middleware(['auth', 'is_admin'])->prefix('admin')->as('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/home', function () {
        return view('admin.home');
    })->name('home');

    // Activities Management (Web CRUD)
    Route::resource('activities', ActivityController::class);

    // Reviews Management (Web CRUD) <--- BLOK INI DITAMBAHKAN!
    Route::resource('reviews', ReviewController::class);

    // Kendaraan (Vehicle) Management (Web CRUD)
    Route::get('/kendaraan', [KendaraanController::class, 'indexAdmin'])->name('kendaraan.index');
    Route::get('/kendaraan/create', [KendaraanController::class, 'createAdmin'])->name('kendaraan.create');
    Route::post('/kendaraan', [KendaraanController::class, 'storeAdmin'])->name('kendaraan.store');
    Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'editAdmin'])->name('kendaraan.edit');
    Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'updateAdmin'])->name('kendaraan.update');
    Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroyAdmin'])->name('kendaraan.destroy');

    // Lokasi (Location) Management (Web CRUD)
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::get('/lokasi/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

    // Destinasi (Destination) Management (Web CRUD)
    Route::get('/destinasi', [DestinasiController::class, 'adminIndex'])->name('destinasi.index');
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('destinasi.create');
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('destinasi.store');
    Route::get('/destinasi/{id}/edit', [DestinasiController::class, 'edit'])->name('destinasi.edit');
    Route::put('/destinasi/{id}', [DestinasiController::class, 'update'])->name('destinasi.update');
    Route::delete('/destinasi/{id}', [DestinasiController::class, 'destroy'])->name('destinasi.destroy');

    // Pemesanan (Booking/Order) Management for Admin Web Panel
    Route::get('/pemesanan', [PemesananController::class, 'indexAdmin'])->name('pemesanan.index');
    // RUTE BARU UNTUK MENAMPILKAN DETAIL PEMESANAN DI ADMIN PANEL
    Route::get('/pemesanan/{pemesanan}', [PemesananController::class, 'showAdmin'])->name('pemesanan.show');
    Route::get('/pemesanan/{pemesanan}/edit', [PemesananController::class, 'editAdmin'])->name('pemesanan.edit');
    Route::put('/pemesanan/{pemesanan}', [PemesananController::class, 'update'])->name('pemesanan.update');
    Route::delete('/pemesanan/{pemesanan}', [PemesananController::class, 'destroy'])->name('pemesanan.destroy');
    Route::put('/pemesanan/{pemesanan}/cancel', [PemesananController::class, 'cancelPemesanan'])->name('pemesanan.cancel');
});

// API Routes (tidak perlu diubah)
