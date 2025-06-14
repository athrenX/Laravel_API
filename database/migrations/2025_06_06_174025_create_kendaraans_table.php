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
            $table->foreignId('destinasi_id')
                    ->constrained('destinasis')
                    ->onDelete('cascade');

            $table->string('jenis');
            $table->integer('kapasitas');
            $table->decimal('harga', 10, 2);
            $table->string('tipe');
            $table->string('gambar')->nullable();
            $table->text('fasilitas')->nullable();
            $table->json('available_seats'); // Kolom ini akan menyimpan array kursi yang tersedia
            $table->json('held_seats')->default(json_encode([])); // *** PENTING: Set default ke array kosong [] ***
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
