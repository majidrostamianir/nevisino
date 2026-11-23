<?php

namespace App\Services\Tapin;

use App\Exceptions\TapinException;
use App\Models\City;
use App\Models\Order;
use App\Models\Province;

class OrderToTapinMapper
{
    public function build(Order $order): array
    {
        $defaults   = (array) config('tapin.defaults');
        $multiplier = (int) config('tapin.currency_multiplier', 10);

        // ۱) pay_type
        $methodValue = $order->shipping_method?->value;
        $payType     = $methodValue ? config('tapin.pay_type_map.' . $methodValue) : null;

        if ($payType === null) {
            throw new TapinException('روش ارسال «' . ($methodValue ?? 'نامشخص') . '» به تاپین متصل نیست.');
        }

        // ۲) استان
        $provinceName = trim((string) $order->province);
        $province     = Province::where('name', $provinceName)->first();

        if (!$province) {
            throw new TapinException('استان «' . $provinceName . '» در دیتابیس یافت نشد.');
        }
        if (blank($province->tapin_code)) {
            throw new TapinException('کد تاپین استان «' . $province->name . '» تنظیم نشده است.');
        }

        // ۳) شهر
        $cityName = trim((string) $order->city);
        $city     = City::where('province_id', $province->id)
            ->get()
            ->first(fn ($c) => trim($c->name) === $cityName);

        if (!$city) {
            throw new TapinException('شهر «' . $cityName . '» در استان «' . $province->name . '» یافت نشد.');
        }
        if (blank($city->tapin_code)) {
            throw new TapinException('کد تاپین شهر «' . trim($city->name) . '» تنظیم نشده است.');
        }

        // ۴) نام و نام خانوادگی
        $fullName  = trim((string) preg_replace('/\s+/u', ' ', (string) $order->recipient_name));
        $parts     = $fullName === '' ? ['نامشخص'] : explode(' ', $fullName, 2);
        $firstName = $parts[0];
        $lastName  = $parts[1] ?? $parts[0];

        // ۵) محصولات — ادغام واریانت‌های یک محصول (قیمت و وزن یکسان)
        $grouped = [];

        foreach ($order->items()->with(['product', 'variant.product'])->get() as $item) {
            $product = $item->product ?? $item->variant?->product;

            if (!$product) {
                continue;
            }

            $pid = $product->id;

            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'count' => 0,
                    'price' => (int) $item->price_snapshot,
                    'title' => trim((string) $product->title) ?: 'محصول',
                    'weight' => max(1, (int) $product->weight),
                ];
            }

            $grouped[$pid]['count'] += (int) $item->quantity;
        }

        $products = [];
        foreach ($grouped as $p) {
            $products[] = [
                'count'      => $p['count'],
                'discount'   => 0,
                'price'      => $p['price'] * $multiplier,
                'title'      => mb_substr($p['title'], 0, 100),
                'weight'     => $p['weight'],
                'product_id' => null,
            ];
        }

        if (empty($products)) {
            throw new TapinException('سفارش هیچ محصول معتبری ندارد.');
        }

        // ۶) payload
        return [
            'register_type'        => (int) $defaults['register_type'],
            'shop_id'              => (string) config('tapin.shop_id'),
            'address'              => mb_substr((string) $order->postal_address, 0, 300),
            'city_code'            => (int) $city->tapin_code,
            'province_code'        => (int) $province->tapin_code,
            'description'          => $order->description ? mb_substr((string) $order->description, 0, 250) : null,
            'email'                => null,
            'employee_code'        => (int) $defaults['employee_code'],
            'first_name'           => mb_substr($firstName, 0, 30),
            'last_name'            => mb_substr($lastName, 0, 40),
            'mobile'               => (string) $order->recipient_mobile,
            'phone'                => null,
            'postal_code'          => (string) $order->zipcode,
            'pay_type'             => (int) $payType,
            'order_type'           => (int) $defaults['order_type'],
            'box_id'               => (int) $defaults['box_id'],
            'packet_type'          => (int) $defaults['packet_type'],
            'non_standard_package' => false,
            'pre_paid_price'       => null,
            'packaging_price'      => (int) ($order->packaging_price ?? 0) * $multiplier,
            'package_weight'       => 100,
            'manual_id'            => (string) $order->id,
            'has_insurance'        => ((int) $payType === 1) && (bool) $defaults['has_insurance'],
            'content_type'         => (int) $defaults['content_type'],
            'products'             => $products,
        ];
    }
}