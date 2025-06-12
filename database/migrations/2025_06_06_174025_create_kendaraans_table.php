<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKendaraansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destinasi_id') // foreign key
                    ->constrained('destinasis') // refer ke tabel destinasis
                    ->onDelete('cascade');      // jika destinasi dihapus, kendaraan ikut dihapus

            $table->string('jenis');
            $table->integer('kapasitas');
            $table->decimal('harga', 10, 2);
            $table->string('tipe');
            $table->string('gambar')->nullable(); // Ditambahkan nullable() untuk fleksibilitas
            $table->text('fasilitas')->nullable();
            $table->json('available_seats')->nullable(); // Kolom ini akan menyimpan array kursi yang tersedia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
}
