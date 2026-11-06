<?php

namespace App\Services\Cart;

use App\Models\Product;

interface CartStore
{
    /**
     * تعداد هر واریانت این محصول در سبد
     *
     * @return array<int,int> variant_id => تعداد
     */
    public function quantities(Product $product): array;

    /** تعداد یک آیتم در سبد؛ $variantId = null یعنی محصول بدون واریانت */
    public function quantity(Product $product, ?int $variantId): int;

    public function save(Product $product, ?int $variantId, int $quantity): void;
}