<?php

namespace App\Helpers;

use App\Models\Setting;

class PriceHelper
{
    /**
     * دریافت قیمت نهایی محصول برای نمایش در سایت
     *
     * @param \App\Models\Product $product
     * @return int|null
     */
    public static function getProductPrice($product)
    {
        // دریافت نوع قیمت از تنظیمات (پیش‌فرض: installment)
        $priceType = Setting::get('display_price_type', 'installment');

        // اگر نوع قیمت قسطی باشد
        if ($priceType === 'installment') {
            // اگر قیمت تخفیفی قسطی وجود داشت، اون رو برگردون، وگرنه قیمت اصلی قسطی
            return $product->discounted_installment_price ?? $product->installment_price;
        }

        // در غیر این صورت (نقدی)
        // اگر قیمت تخفیفی نقدی وجود داشت، اون رو برگردون، وگرنه قیمت اصلی نقدی
        return $product->discounted_price ?? $product->price;
    }

    /**
     * دریافت قیمت پایه محصول (بدون تخفیف)
     *
     * @param \App\Models\Product $product
     * @return int|null
     */
    public static function getBasePrice($product)
    {
        $priceType = Setting::get('display_price_type', 'installment');

        if ($priceType === 'installment') {
            return $product->installment_price;
        }

        return $product->price;
    }


    /**
     * بررسی آیا قیمت تخفیف خورده است یا نه
     *
     * @param \App\Models\Product $product
     * @return bool
     */
    public static function hasDiscount($product)
    {
        $priceType = Setting::get('display_price_type', 'installment');

        if ($priceType === 'installment') {
            return !is_null($product->discounted_installment_price) && $product->discounted_installment_price < $product->installment_price;
        }

        return !is_null($product->discounted_price) && $product->discounted_price < $product->price;
    }
}