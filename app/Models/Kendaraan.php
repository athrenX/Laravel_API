<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'jenis',
        'kapasitas',
        'harga',
        'tipe',
        'gambar',
        'fasilitas',
        'available_seats',
    ];

    protected $casts = [
        'available_seats' => 'array',
        'harga' => 'float',
    ];
}
