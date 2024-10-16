<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = "pesanans";

    protected $fillable = [
        'tokos_id',
        'layanans_id',
        'promos_id',
        'tanggal_pesanan',
        'status',
        'total_harga',
    ];
}
