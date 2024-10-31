<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = "layanans";

    protected $fillable = [
        'user_id',
        'referensi_layanan_id',
        'harga',
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function ReferensiLayanan ()
    {
        return $this->belongsTo(ReferensiLayanan::class);
    }     
}
