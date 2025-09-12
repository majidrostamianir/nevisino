<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function convertToOrder($description = null)
    {
        return DB::transaction(function () use ($description) {

            $totalPrice = $this->items->sum(function ($item) {
                if ($item->variant_id && $item->variant) {
                    return ($item->variant->price ?? $item->product->price) * $item->quantity;
                }
                return $item->product->price * $item->quantity;
            });

            $order = $this->user->orders()->create([
                'status' => 'pending',
                'amount' => $totalPrice + config('shop.shipping'),
                'description' => $description,
                'expires_at' => now()->addMinutes( config('shop.expire_order_time_minutes') ),
            ]);

            foreach ($this->items as $item) {
                $priceSnapshot = $item->variant_id
                    ? ($item->variant->price ?? $item->product->price)
                    : $item->product->price;

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price_snapshot' => $priceSnapshot,
                ]);

                if ($item->variant_id && $item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            $this->items()->delete();
            $this->delete();

            return $order;
        });
    }
}
