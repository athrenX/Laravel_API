<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destinasi_id',
        'kendaraan_id',
        'selected_seats',
        'jumlah_peserta',
        'tanggal_pemesanan',
        'total_harga',
        'status',
        'expired_at', // Tambahkan ini
    ];

    protected $casts = [
        'selected_seats' => 'array', // Otomatis mengonversi ke/dari JSON
        'total_harga' => 'double',
        'tanggal_pemesanan' => 'datetime', // Cast ke objek DateTime
        'expired_at' => 'datetime', // Tambahkan ini
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Destinasi
    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class);
    }

    // Relasi ke Kendaraan
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
