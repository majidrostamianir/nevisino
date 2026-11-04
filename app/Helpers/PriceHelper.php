<?php

namespace App\Helpers;

class PriceHelper
{
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
     * دریافت قیمت پایه محصول (بدون تخفیف)
     *
     * @param \App\Models\Product $product
     * @return int|null
     */
    public static function getBasePrice($product)
    {
        if (Site::isGhesti()) {
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
        if (Site::isGhesti()) {
            return !is_null($product->discounted_installment_price)
                && $product->discounted_installment_price < $product->installment_price;
        }

        return !is_null($product->discounted_price)
            && $product->discounted_price < $product->price;
    }
}