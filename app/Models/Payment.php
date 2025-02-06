<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, Uuid;

    public $fillable = [
        'order_id',
        'snap_token',
        'status',
        'expired_at',
        'paid_at',
    ];
}
