<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'destinasi_id',
        'jenis',
        'kapasitas',
        'harga',
        'tipe',
        'gambar',
        'fasilitas',
        'available_seats',
        'held_seats', // Pastikan ini ada
    ];

    protected $casts = [
        'available_seats' => 'array',
        'held_seats' => 'array', // Pastikan ini ada dan 'array'
        'harga' => 'float',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }
}
