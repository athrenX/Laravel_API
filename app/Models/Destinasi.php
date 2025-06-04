<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;


class Destinasi extends Model
{
    use HasFactory;

    protected $table = 'destinasis';

    protected $fillable = [
        'nama',
        'kategori',
        'deskripsi',
        'harga',
        'gambar',
        'rating',
        'lat',
        'lng',
        'lokasi',
        'galeri',
    ];

    protected $casts = [
        'galeri' => 'array', // Laravel akan otomatis decode dari JSON ke array
        'harga' => 'double',
        'rating' => 'double',
        'lat' => 'double',
        'lng' => 'double',
    ];

    public function getGambarAttribute($value)
    {
        // Jika sudah berupa URL lengkap, langsung return
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        // Jika bukan URL lengkap, prepend dengan asset()
        return asset('storage/' . $value);
    }


}
