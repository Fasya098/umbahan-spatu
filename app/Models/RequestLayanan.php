<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLayanan extends Model
{
    use HasFactory, Uuid;s

    protected $table="request_layanans";

    protected $fillable=[
        'request_nama_layanan',
    ];
}
