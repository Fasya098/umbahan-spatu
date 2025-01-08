<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory, Uuid;

    protected $table = "promos";
    
    protected $fillable = [
        'toko_id',
        'nama_promo',
        'harga',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];

    public function Toko () 
    {
        return $this->belongsTo(Toko::class);
    }
}
