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

    public function convertToOrder($recipient_name , $recipient_mobile , $postal_address , $zipcode ,$description = null)
    {
        return DB::transaction(function () use ($zipcode, $postal_address, $recipient_mobile, $recipient_name, $description) {

            $totalPrice = $this->items->sum(function ($item) {
                if ($item->variant_id && $item->variant) {
                    return ($item->variant->price ?? $item->product->price) * $item->quantity;
                }
                return $item->product->price * $item->quantity;
            });

            $lastOrderNumber = \App\Models\Order::max('order_number');

            $newOrderNumber = $lastOrderNumber
                ? $lastOrderNumber + rand(1, 20)
                : 2145180;

            $order = $this->user->orders()->create([
                'order_number' => $newOrderNumber,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'shipping_price' => config('shop.shipping'),
                'amount' => $totalPrice + config('shop.shipping'),
                'recipient_name' => $recipient_name,
                'recipient_mobile' => $recipient_mobile,
                'postal_address' => $postal_address,
                'zipcode' => $zipcode,
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
