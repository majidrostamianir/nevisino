<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Cart extends Component
{
    public array|null $cart = [];
    public int $sumCash = 0;
    public int $sumInstallment = 0;

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
                'id'                => $item->product_id,
                'title'             => $item->product->title,
                'cash_price'        => $item->product->discounted_price ?? $item->product->price,
                'installment_price' => $item->product->discounted_installment_price ?? $item->product->installment_price,
                'code'              => $item->product->code,
                'quantity'          => $item->quantity,
                'variant'           => $item->variant_id,
                'variantName'       => $item->variant?->name,
                'variantField'      => $item->product->variant,
                'dashed_url'        => $item->product->dashed_url,
            ];
        }

        if ($this->cart) {
            $this->sumPriceProducts();
        }
    }

    public function sumPriceProducts(): void
    {
        $this->sumCash = collect($this->cart)->sum(
            fn ($item) => $item['cash_price'] * $item['quantity']
        );

        $this->sumInstallment = collect($this->cart)->sum(
            fn ($item) => $item['installment_price'] * $item['quantity']
        );
    }

    public function render()
    {
        return view('livewire.admin.user.cart')->layout('components.layouts.admin');
    }
}