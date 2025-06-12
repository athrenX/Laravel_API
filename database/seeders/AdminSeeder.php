<?php
// database/seeders/AdminSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Hapus admin yang sudah ada (opsional)
        User::where('email', 'admin@gmail.com')->delete();
        
        // Buat admin baru
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123', // Akan otomatis di-hash oleh mutator
            'role' => 'admin',
            'foto_profil' => null,
            'payment_method' => null,
        ]);
        
        echo "Admin berhasil dibuat dengan email: admin@gmail.com dan password: password123\n";
    }
}