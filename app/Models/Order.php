<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'amount','description', 'expires_at'];

    protected $dates = ['expires_at'];

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
