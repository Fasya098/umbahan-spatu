<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory, Uuid;

    protected $table = "pesanans";

    protected $fillable = [
        'user_id',
        'toko_id',
        'layanan_id',
        'foto_sepatu',
        'brand_sepatu',
        'warna_sepatu',
        'tanggal_pesanan',
        'status',
        'total_harga',
        'kode_pesanan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function referensiLayanan()
    {
        return $this->belongsTo(ReferensiLayanan::class);
    }
}
