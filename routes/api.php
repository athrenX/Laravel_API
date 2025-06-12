<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Api\KendaraanController; // Pastikan ini diimpor

// ✅ Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/activities', [ActivityController::class, 'indexApi']);
Route::get('/location/{id}', [LokasiController::class, 'show']);
Route::get('/location/name/{name}', [LokasiController::class, 'showByName']);

// ✅ Protected Routes (auth:sanctum)
Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{destinasiId}', [WishlistController::class, 'destroy']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ✅ REST API Resource for Destinasi
    Route::apiResource('destinasis', DestinasiController::class);

    // ✅ Kendaraan Routes (Protected)
    Route::prefix('kendaraan')->group(function () {
        // Mengambil kendaraan berdasarkan destinasi_id
        Route::get('/by-destinasi/{destinasiId}', [KendaraanController::class, 'indexByDestinasi']);
        // Memperbarui kursi yang tersedia (untuk pemesanan)
        Route::post('/{kendaraanId}/update-seats', [KendaraanController::class, 'updateSeats']);
    });
});
