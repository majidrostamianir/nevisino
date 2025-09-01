<?php

namespace App\Livewire\Home;

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
        $this->cartCount = collect(session()->get('cart', []))->sum('count');

    }

    public function render()
    {
        return view('livewire.home.header');
    }
}
