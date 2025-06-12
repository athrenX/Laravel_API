<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'destinasi_id', // Pastikan ini ada
        'jenis',
        'kapasitas',
        'harga',
        'tipe',
        'gambar',
        'fasilitas',
        'available_seats',
    ];

    protected $casts = [
        'available_seats' => 'array', // Pastikan ini ada
        'harga' => 'float',
    ];

    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }
}
