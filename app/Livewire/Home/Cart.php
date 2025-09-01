<?php

namespace App\Livewire\Home;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Livewire\Component;

class Cart extends Component
{

    public array|null $cart = [];
    public int $sum = 0;

    public function mount()
    {
        $this->cart = session()->get('cart');
        if ($this->cart)
            $this->sumPrice();
    }

    public function sumPrice()
    {
        foreach ($this->cart as $product) {
            $this->sum = $this->sum + ($product['price'] * $product['count']);
        }
    }

    public function stockCheck($id)
    {
        $variant = Str::after($id, '-');
        $product = Product::query()->find(Str::before($id, '-'));

        if ($variant == "default") {
            return $product->stock;
        } else {
            return ProductVariant::query()->find($variant)->stock;
        }
    }

    public function checkout()
    {
        session()->put('product_url', '/checkout');
        return redirect()->route('checkout');
    }

    public function increase($id)
    {

        $count = $this->cart[$id]['count'];
        if ($count < $this->stockCheck($id)) {
            $this->cart[$id]['count'] = $count + 1;
        }
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');

        $this->sumPrice();

    }

    public function decrease($id)
    {
        $count = $this->cart[$id]['count'];
        if ($count > 1) {
            $this->cart[$id]['count'] = $count - 1;
        }
        session()->put('cart', $this->cart);
        $this->dispatch('cart-updated');
        $this->sumPrice();
    }

    public function removeFromCart($id)
    {
        if (isset($this->cart[$id])) {
            unset($this->cart[$id]); // حذف از آرایه
            session()->put('cart', $this->cart); // آپدیت سشن
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.home.cart');
    }
}
