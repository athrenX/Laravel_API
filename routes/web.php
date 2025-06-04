<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\DestinasiController;

// ✅ ROUTE: Halaman login untuk admin
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ✅ ROUTE: Proteksi untuk admin saja
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/destinasi', [DestinasiController::class, 'adminIndex'])->name('admin.destinasi.index');
    Route::get('/destinasi/create', [DestinasiController::class, 'create'])->name('admin.destinasi.create');
    Route::post('/destinasi', [DestinasiController::class, 'store'])->name('admin.destinasi.store');
    Route::get('/destinasi/{id}/edit', [DestinasiController::class, 'edit'])->name('admin.destinasi.edit');
    Route::put('/destinasi/{id}', [DestinasiController::class, 'update'])->name('admin.destinasi.update');
    Route::delete('/destinasi/{id}', [DestinasiController::class, 'destroy'])->name('admin.destinasi.destroy');
});
