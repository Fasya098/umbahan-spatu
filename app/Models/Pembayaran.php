<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory, Uuid;
    
    protected $table = "pembayarans";

    protected $fillable = [
        'pesanans_id',
        'metode_pembayaran',
        'status',
    ];
}
