<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = 'wishlists';

    protected $fillable = [
        'users_id',     // pastikan kolom ini memang 'users_id' di DB
        'destinasis_id', // koreksi typo dari 'destinass_id' jadi 'destinasis_id'
    ];

    public $timestamps = true;

    // Relasi ke User dengan foreign key 'users_id'
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // Relasi ke Destinasi dengan foreign key 'destinasis_id'
    public function destinasi()
    {
        return $this->belongsTo(Destinasi::class, 'destinasis_id');
    }
}
