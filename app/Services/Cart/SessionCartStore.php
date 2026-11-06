<?php

namespace App\Services\Cart;

use App\Helpers\PriceHelper;
use App\Models\Product;

class SessionCartStore implements CartStore
{
    private const SESSION_KEY = 'cart';

    public function quantities(Product $product): array
    {
        return $product->variants
            ->mapWithKeys(fn($variant) => [$variant->id => $this->quantity($product, $variant->id)])
            ->all();
    }

    public function quantity(Product $product, ?int $variantId): int
    {
        $cart = session()->get(self::SESSION_KEY, []);

        return $cart[$this->key($product, $variantId)]['quantity'] ?? 0;
    }

    public function save(Product $product, ?int $variantId, int $quantity): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $variant = $variantId ? $product->variants->firstWhere('id', $variantId) : null;

        $cart[$this->key($product, $variantId)] = [
            'id'          => $product->id,
            'title'       => $product->title,
            'price'       => PriceHelper::getProductPrice($product),
            'code'        => $product->code,
            'quantity'    => $quantity,
            'variant'     => $variantId,
            'variantName' => $variant?->name,
        ];

        session()->put(self::SESSION_KEY, $cart);
    }

    /** در سشن برای محصول بدون واریانت از 0 استفاده می‌شود تا کلید معتبر باشد */
    private function key(Product $product, ?int $variantId): string
    {
        return $product->id . '-' . ($variantId ?? 0);
    }
}