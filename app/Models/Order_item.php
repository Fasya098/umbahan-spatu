<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_item extends Model
{
    use HasFactory, Uuid;

    protected $fillable = [
        'order_id',
        'product_name',
        'price',
        'quantity',
    ];
}
