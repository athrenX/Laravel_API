<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            // Ubah kolom held_seats agar tidak nullable dan memiliki default array kosong
            $table->json('held_seats')->default(json_encode([]))->change();
        });

        // Opsional: Update data yang sudah ada agar null menjadi array kosong
        DB::table('kendaraans')->whereNull('held_seats')->update(['held_seats' => json_encode([])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            // Kembalikan ke nullable jika diinginkan, atau sesuai dengan kebutuhan aplikasi Anda
            $table->json('held_seats')->nullable()->change();
        });
    }
};
