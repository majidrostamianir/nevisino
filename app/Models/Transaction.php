<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['order_id', 'status', 'amount', 'payment_gateway', 'authority'];
    protected $casts = [
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
