<?php

namespace Database\Seeders;

// database/seeders/AdminSeeder.php
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    public function run() {
        User::create([
            'nama' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'foto_profil' => null,
            'payment_method' => null,
        ]);
    }
}
