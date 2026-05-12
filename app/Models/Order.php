<?php

namespace App\Models;

use App\Enums\TorobpayStatusEnum;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_number', 'user_id', 'status','shipping_status','tracking_code',
        'total_price', 'shipping_price', 'amount',
        'recipient_name', 'recipient_mobile', 'postal_address', 'zipcode',
        'province', 'city' , 'description', 'expires_at' ,
        'torobpay_payment_token',
        'torobpay_transaction_id',
        'torobpay_status',
        'torobpay_amount',
        'torobpay_paid_at',
        ];
    protected $casts = [
        'torobpay_paid_at' => 'datetime',
        'torobpay_status' => TorobpayStatusEnum::class,
    ];

    public function isTorobpayOrder(): bool
    {
        return !is_null($this->torobpay_payment_token);
    }

    public function canVerify(): bool
    {
        return $this->torobpay_status === TorobpayStatusEnum::W_FOR_VERIFY;
    }

    public function canSettle(): bool
    {
        return $this->torobpay_status === TorobpayStatusEnum::W_FOR_SETTLE;
    }

    public function updateTorobpayStatus(TorobpayStatusEnum $status, ?string $transactionId = null): void
    {
        $this->torobpay_status = $status;

        if ($transactionId) {
            $this->torobpay_transaction_id = $transactionId;
        }

        if ($status === TorobpayStatusEnum::PAID) {
            $this->torobpay_paid_at = now();
        }

        $this->save();
    }
    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function markAsPaid()
    {
        $this->update(['status' => 'paid']);
    }

    public function markAsCanceled()
    {
        // آزاد کردن موجودی محصولات یا واریانت‌ها
        foreach ($this->items as $item) {
            if ($item->variant_id && $item->variant) {
                $item->variant->increment('stock', $item->quantity);
            } else {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $this->update(['status' => 'canceled']);
    }


    public function isExpired()
    {
        return now()->greaterThan($this->expires_at);
    }

}
