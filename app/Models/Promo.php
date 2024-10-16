<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = "promos";
    
    protected $fillable = [
        'tokos_id',
        'nama_promo',
        'diskon',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];
}
