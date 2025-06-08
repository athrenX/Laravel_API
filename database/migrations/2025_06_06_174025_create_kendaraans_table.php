<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKendaraansTable extends Migration
{
   
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
                $table->string('gambar');
                $table->text('fasilitas')->nullable();
                $table->json('available_seats')->nullable();
                $table->timestamps();
            });
        }
    
     
    

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
}
