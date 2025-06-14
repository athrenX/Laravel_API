<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key ke tabel users
            $table->foreignId('destinasi_id')->constrained('destinasis')->onDelete('cascade'); // Foreign key ke tabel destinasis
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('restrict'); // Restrict: kendaraan tidak bisa dihapus jika ada pemesanan
            $table->json('selected_seats'); // Array kursi yang dipilih [1, 5, 8]
            $table->integer('jumlah_peserta'); // Jumlah peserta (harus sama dengan selected_seats.length)
            $table->timestamp('tanggal_pemesanan')->useCurrent(); // Tanggal pemesanan dibuat
            $table->double('total_harga', 10, 2); // Total harga akhir
            $table->string('status')->default('pending'); // Status pemesanan
            $table->timestamp('expired_at')->nullable(); // Waktu kadaluarsa pemesanan (untuk holding seats)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
