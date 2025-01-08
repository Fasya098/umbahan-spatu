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
        'toko_id',
        'layanan_id',
        'promo_id',
        'foto_sepatu',
        'brand_sepatu',
        'warna_sepatu',
        'tanggal_pesanan',
        'status',
        'total_harga',
    ];
    
}
