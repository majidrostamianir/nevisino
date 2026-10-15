<?php

namespace App\Livewire\Admin\User;

use App\Helpers\PriceHelper;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Cart extends Component
{
    public array|null $cart = [];
    public int $sum = 0;

    public User $user;
    public Collection $orders;

    public function mount()
    {
        $this->orders = collect();

        $cartItems = \App\Models\CartItem::with('product', 'variant')
            ->whereHas('cart', fn ($q) => $q->where('user_id', $this->user->id))
            ->get();

        $this->cart = [];

        foreach ($cartItems as $item) {
            $key = $item->product_id . '-' . ($item->variant_id ?? 'default');

            $this->cart[$key] = [
                'id'            => $item->product_id,
                'title'         => $item->product->title,
                'price'         => PriceHelper::getProductPrice($item->product),  // ← این
                'code'          => $item->product->code,
                'quantity'      => $item->quantity,
                'variant'       => $item->variant_id,
                'variantName'   => $item->variant?->name,
                'variantField'  => $item->product->variant,       // ← این (برای N+1)
                'dashed_url'    => $item->product->dashed_url,    // ← این (برای N+1)
            ];
        }

        if ($this->cart) {
            $this->sumPriceProducts();
        }
    }

    public function sumPriceProducts(): void
    {
        $this->sum = collect($this->cart)->sum(
            fn ($item) => $item['price'] * $item['quantity']
        );
    }

    public function render()
    {
        return view('livewire.admin.user.cart')->layout('components.layouts.admin');
    }
}