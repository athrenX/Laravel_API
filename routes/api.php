<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinasiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\ReviewController; // Pastikan ini sudah ada

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/activities', [ActivityController::class, 'indexApi']);
Route::get('/location/{id}', [LokasiController::class, 'show']);
Route::get('/location/name/{name}', [LokasiController::class, 'showByName']);

// Protected Routes (auth:sanctum)
Route::middleware(['auth:sanctum'])->group(function () {

    // Wishlist management
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{destinasiId}', [WishlistController::class, 'destroy']);

    // User profile management
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // REST API Resource for Destinations
    Route::apiResource('destinasis', DestinasiController::class);

    // Tambahkan route ini untuk mendapatkan review berdasarkan destinasi_id
    Route::get('/destinasis/{destinasiId}/reviews', [ReviewController::class, 'getReviewsByDestinasi']);


    // Kendaraan (Vehicle) Routes
    Route::prefix('kendaraan')->group(function () {
        Route::get('/by-destinasi/{destinasiId}', [KendaraanController::class, 'indexByDestinasi']);
        Route::post('/{kendaraanId}/hold-seats', [KendaraanController::class, 'holdSeats']);
        Route::post('/{kendaraanId}/release-held-seats', [KendaraanController::class, 'releaseHeldSeats']);
    });

    // Pemesanan (Booking/Order) Routes
    Route::apiResource('pemesanans', PemesananController::class)->except(['update']);
    Route::post('/pemesanans/{pemesanan}/confirm-payment', [PemesananController::class, 'confirmPayment']);
    Route::post('/pemesanans/{pemesanan}/cancel', [PemesananController::class, 'cancelPemesanan']);

    // Admin-specific Pemesanan routes (jika dibutuhkan terpisah dari API Resource umum)
    Route::put('/pemesanans/{pemesanan}', [PemesananController::class, 'update']);

    // Custom route for users to view their own bookings.
    Route::get('/my-pemesanans', [PemesananController::class, 'index']);

    // Review management
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::get('/reviews/order/{order_id}', [ReviewController::class, 'showByOrder']);
});