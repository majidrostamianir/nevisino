<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'amount',
        'payment_gateway',
        'authority',
        'payment_token',
        'torobpay_transaction_id',
        'torobpay_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
