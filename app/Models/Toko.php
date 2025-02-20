<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory, Uuid;

    protected $table = "tokos";

    protected $fillable = [
        'user_id',
        'nama_toko',
        'foto_toko',
        'deskripsi',
        'alamat',
        'nomor_telepon',
        'ongkir',
        'latitude',
        'longitude',
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function Promo () {
        return $this->hasMany(Promo::class);
    }

    public function pesanan ()
    {
        return $this->hasMany(Pesanan::class);
    }
}
