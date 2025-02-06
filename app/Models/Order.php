<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, Uuid;

    protected $guarded = [];

    protected $fillable = [
        'order_id',
        'total_price',
        'status',
        'payment_status',
    ];

    public function items()
    {
        return $this->hasMany(Order_item::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
