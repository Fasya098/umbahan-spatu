<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferensiLayanan extends Model
{
    use HasFactory, Uuid;

    protected $table = "referensi_layanans";

    protected $fillable = [
        'nama_layanan',
    ];

    public function Layanan ()
    {
        return $this->hasMany(Layanan::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
