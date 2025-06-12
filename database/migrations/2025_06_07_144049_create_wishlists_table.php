<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishlistsTable extends Migration
{
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();

            // Foreign key ke users
            $table->unsignedBigInteger('users_id');

            // Foreign key ke destinasis
            $table->unsignedBigInteger('destinasis_id');

            $table->timestamps();

            // Definisikan foreign key constraints
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('destinasis_id')->references('id')->on('destinasis')->onDelete('cascade');

            // Optional: agar kombinasi users_id dan destinasis_id unik (tidak boleh dobel)
            $table->unique(['users_id', 'destinasis_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wishlists');
    }
}
