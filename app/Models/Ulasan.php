<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory, Uuid;

    protected $table = "ulasans";

    protected $fillable = [
        'pesanans_id',
        'rating',
        'komentar',
    ];
}
