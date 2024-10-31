<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferensiLayanan extends Model
{
    use HasFactory;

    protected $table = "referensi_layanans";

    protected $fillable = [
        'nama_layanan',
    ];

    public function Layanan ()
    {
        return $this->hasMany(Layanan::class);
    }
}
