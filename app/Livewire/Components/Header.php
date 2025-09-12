<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public int $cartCount = 0;
    protected $listeners = ['cart-updated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        if (Auth::check()) {
            $this->cartCount = \App\Models\CartItem::query()->whereHas('cart', fn($q) => $q->where('user_id', Auth::id())
            )->sum('quantity');
        } else {
            $this->cartCount = collect(session()->get('cart', []))->sum('count');
        }

    }

    public function render()
    {
        return view('livewire.components.header');
    }
}
