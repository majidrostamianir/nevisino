<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DatabaseCartStore implements CartStore
{
    private Model $cart;

    public function __construct(User $user)
    {
        $this->cart = $user->cart()->firstOrCreate();
    }

    public function quantities(Product $product): array
    {
        return $this->cart->items()
            ->where('product_id', $product->id)
            ->pluck('quantity', 'variant_id')
            ->all();
    }

    public function quantity(Product $product, ?int $variantId): int
    {
        return (int) $this->cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variantId)
            ->value('quantity');
    }

    public function save(Product $product, ?int $variantId, int $quantity): void
    {
        $this->cart->items()->updateOrCreate(
            ['product_id' => $product->id, 'variant_id' => $variantId],
            ['quantity' => $quantity],
        );
    }
}