<?php

namespace App\Helpers;

class PriceHelper
{
    /**
     * قیمت نهاییِ نمایشی محصول بر اساس نوع سایت.
     * قسطی → discounted_installment_price ?? installment_price
     * نقدی → discounted_price ?? price
     */
    public static function getProductPrice($product)
    {
        // سایت قسطی: قیمت قسطی (با تخفیف اگر بود)
        if (Site::isGhesti()) {
            return $product->discounted_installment_price ?? $product->installment_price;
        }

        // سایت اصلی: قیمت نقدی (با تخفیف اگر بود)
        return $product->discounted_price ?? $product->price;
    }

    /**
     * قیمت پایه محصول (بدون تخفیف) بر اساس نوع سایت.
     * قسطی → installment_price | نقدی → price
     */
    public static function getBasePrice($product)
    {
        if (Site::isGhesti()) {
            return $product->installment_price;
        }

        return $product->price;
    }

    /**
     * آیا محصول تخفیف دارد؟ (قیمت تخفیف‌خورده وجود دارد و کمتر از قیمت پایه است)
     */
    public static function hasDiscount($product)
    {
        if (Site::isGhesti()) {
            return !is_null($product->discounted_installment_price)
                && $product->discounted_installment_price < $product->installment_price;
        }

        return !is_null($product->discounted_price)
            && $product->discounted_price < $product->price;
    }

    /**
     * قیمت قسطی محصول (با تخفیف در صورت وجود)
     */
    public static function getGhestiPrice($product)
    {
        return $product->discounted_installment_price ?? $product->installment_price;
    }

    /**
     * قیمت نقدی محصول (با تخفیف در صورت وجود)
     */
    public static function getCachPrice($product)
    {
        return $product->discounted_price ?? $product->price;
    }
}