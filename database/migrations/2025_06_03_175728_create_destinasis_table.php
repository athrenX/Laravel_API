<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('destinasis', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('kategori'); // Gunung / Pantai
        $table->text('deskripsi');
        $table->double('harga', 10, 2);
        $table->string('gambar');
        $table->double('rating', 3, 2)->nullable();
        $table->double('lat', 10, 6);
        $table->double('lng', 10, 6);
        $table->string('lokasi');
        $table->json('galeri'); // disimpan sebagai array JSON
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinasis');
    }
};
