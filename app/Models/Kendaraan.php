<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'destinasi_id',
        'jenis',
        'kapasitas',
        // 'harga', // HAPUS BARIS INI
        'tipe',
        'gambar',
        'fasilitas',
        'available_seats',
        'held_seats',
    ];

    protected $casts = [
        'available_seats' => 'array',
        'held_seats' => 'array',
        // 'harga' => 'float', // HAPUS BARIS INI
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }
}
