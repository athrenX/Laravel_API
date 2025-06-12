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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // Nama lokasi, misal: Java Wonderland
            $table->string('alamat');     // Alamat lokasi
            $table->decimal('latitude', 10, 7);  // Latitude, presisi 7 desimal
            $table->decimal('longitude', 10, 7); // Longitude, presisi 7 desimal
            $table->timestamps();         // created_at dan updated_at
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
