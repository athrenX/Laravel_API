<?php

use Illuminate\Support\Facades\Route; // PASTIKAN BARIS INI ADA
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DestinasiController; // For admin destinasi web routes (adminIndex, etc.)
use App\Http\Controllers\LokasiController; // For admin lokasi web routes
use App\Http\Controllers\ActivityController; // Tambahkan/pastikan baris import ini ada dan benar
use App\Http\Controllers\Api\KendaraanController; // For admin kendaraan web resource routes

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

Route::get('/', function () { // Perbaikan: Route::get
    return view('welcome');
});

// Route login admin
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Perbaikan: Route::get
Route::post('/login', [LoginController::class, 'login']); // Perbaikan: Route::post
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // Perbaikan: Route::post

// Group route for authenticated admin users with 'is_admin' middleware
Route::middleware(['auth', 'is_admin'])->prefix('admin')->as('admin.')->group(function () {

    // Admin dashboard page
    Route::get('/home', function () { // Perbaikan: Route::get
        return view('admin.home');
    })->name('home');

    // Resource routes for activities (complete CRUD)
    // Asumsi ActivityController ada di App\Http\Controllers\ActivityController dan menggunakan method default (index, create, store, etc.)
    Route::resource('activities', ActivityController::class);

    // NON-RESOURCE ROUTES for Kendaraan (using App\Http\Controllers\Api\KendaraanController)
    // Metode-metode ini secara eksplisit memanggil fungsi dengan akhiran 'Admin'
    Route::get('/kendaraan', [KendaraanController::class, 'indexAdmin'])->name('kendaraan.index'); // Perbaikan: Route::get
    Route::get('/kendaraan/create', [KendaraanController::class, 'createAdmin'])->name('kendaraan.create'); // Perbaikan: Route::get
    Route::post('/kendaraan', [KendaraanController::class, 'storeAdmin'])->name('kendaraan.store'); // Perbaikan: Route::post
    Route::get('/kendaraan/{kendaraan}/edit', [KendaraanController::class, 'editAdmin'])->name('kendaraan.edit'); // Perbaikan: Route::get
    Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'updateAdmin'])->name('kendaraan.update'); // Perbaikan: Route::put
    Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroyAdmin'])->name('kendaraan.destroy'); // Perbaikan: Route::delete
    // Catatan: Method 'show' dihilangkan karena Anda tidak menginginkannya untuk admin web,
    // dan sudah ditangani oleh API jika dibutuhkan.

    // Routes for Lokasi
    // Asumsi LokasiController ada di App\Http\Controllers\LokasiController dan menggunakan method default (index, create, store, etc.)
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index'); // Perbaikan: Route::get
    Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create'); // Perbaikan: Route::get
    Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store'); // Perbaikan: Route::post
    Route::get('/lokasi/{id}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit'); // Perbaikan: Route::get
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update'); // Perbaikan: Route::put
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy'); // Perbaikan: Route::delete

    // Routes for Destinasi (using App\Http\Controllers\DestinasiController for admin panel)
    // Asumsi DestinasiController ada di App\Http\Controllers\DestinasiController dan menggunakan method adminIndex, create, store, etc.
    Route::get('/destinasi', [DestinasiController::class, 'adminIndex'])->name('destinasi.index'); // Perbaikan: Route::get
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('destinasi.create'); // Perbaikan: Route::get
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('destinasi.store'); // Perbaikan: Route::post
    Route::get('/destinasi/{id}/edit', [DestinasiController::class, 'edit'])->name('destinasi.edit'); // Perbaikan: Route::get
    Route::put('/destinasi/{id}', [DestinasiController::class, 'update'])->name('destinasi.update'); // Perbaikan: Route::put
    Route::delete('/destinasi/{id}', [DestinasiController::class, 'destroy'])->name('destinasi.destroy'); // Perbaikan: Route::delete
});
