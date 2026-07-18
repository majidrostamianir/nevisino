<?php

namespace App\Models;

use App\Helpers\PriceHelper; // <-- اضافه کن
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

    public function convertToOrder(array $orderData)
    {
        return DB::transaction(function () use ($orderData) {

            $province_id = $orderData['province_id'];
            $city_id = $orderData['city_id'];
            $recipient_name = $orderData['recipient_name'];
            $recipient_mobile = $orderData['recipient_mobile'];
            $postal_address = $orderData['postal_address'];
            $zipcode = $orderData['zipcode'];
            $description = $orderData['description'] ?? null;
            $shipping_method = $orderData['shipping_method'] ?? 'post_cod';
            $shipping_price = $orderData['shipping_price'] ?? 0;
            $packaging_price = $orderData['packaging_price'] ?? 0;

            // اصلاح شده: استفاده از PriceHelper
            $totalPrice = $this->items->sum(function ($item) {
                return PriceHelper::getProductPrice($item->product) * $item->quantity;
            });

            $lastOrderNumber = \App\Models\Order::max('order_number');

            $newOrderNumber = $lastOrderNumber
                ? $lastOrderNumber + rand(1, 20)
                : 2145180;

            $order = $this->user->orders()->create([
                'order_number' => $newOrderNumber,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'shipping_method' => $shipping_method,
                'shipping_price' => $shipping_price,
                'packaging_price' => $packaging_price,
                'amount' => $totalPrice + $shipping_price + $packaging_price,
                'recipient_name' => $recipient_name,
                'recipient_mobile' => $recipient_mobile,
                'postal_address' => $postal_address,
                'zipcode' => $zipcode,
                'province' => Province::find($province_id)->name,
                'city' => City::find($city_id)->name,
                'description' => $description,
                'expires_at' => now()->addMinutes(config('shop.expire_order_time_minutes')),
            ]);

            foreach ($this->items as $item) {
                // اصلاح شده: استفاده از PriceHelper
                $priceSnapshot = PriceHelper::getProductPrice($item->product);

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price_snapshot' => $priceSnapshot,
                ]);
            }

            $this->items()->delete();
            $this->delete();

            return $order;
        });
    }
}