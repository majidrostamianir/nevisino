<?php

namespace App\Livewire\Payment;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Cart extends Component
{

    public array|null $cart = [];
    public int $sum = 0;
    public int $shipping = 0;
    public int $total = 0;

    protected $listeners = ['cart-updated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        if (Auth::check()) {
            $cartItems = \App\Models\CartItem::with('product','variant')
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->get();

            $this->cart = [];
            foreach ($cartItems as $item) {
                $key = $item->product_id . '-' . ($item->variant_id ?? 'default');
                $this->cart[$key] = [
                    'id' => $item->product_id,
                    'title' => $item->product->title,
                    'price' => $item->product->price,
                    'code' => $item->product->code,
                    'quantity' => $item->quantity,
                    'variant' => $item->variant_id,
                    'variantName' => $item->variant?->name,
                ];
            }
        } else {
            $this->cart = session('cart');
        }
        if ($this->cart)
            $this->sumPriceProducts();
    }

    public function sumPriceProducts()
    {
        if (Auth::check()) {
            $cartItems = \App\Models\CartItem::query()->whereHas('cart', fn($q) =>
            $q->where('user_id', Auth::id())
            )->with('product','variant')->get();

            $this->sum = $cartItems->sum(function($item){
                $price = $item->variant?->price ?? $item->product->price;
                return $price * $item->quantity;
            });

        } else {
            $cart = session()->get('cart', []);
            $this->sum = collect($cart)->sum(function($item){
                return $item['price'] * $item['quantity'];
            });
        }

        if ($this->sum > 0) {
            $this->shipping = config('shop.shipping');
            $this->total = $this->sum + $this->shipping;
        } else {
            $this->shipping = 0;
            $this->total = 0;
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
        if (empty($this->cart)) {
            $this->dispatch('showNotification',
                message: 'سبد خرید خالی است',
                type: 'warning'
            );
        } else {
            session()->put('previous_url', '/checkout');
            return $this->redirect('/checkout', navigate: true);
        }
    }

    public function increase($id)
    {
        if (Auth::check()) {

            $productId = Str::before($id, '-');
            $variantId = Str::after($id, '-') === 'default' ? null : Str::after($id, '-');

            $cartItem = \App\Models\CartItem::query()->whereHas('cart', fn($q) =>
            $q->where('user_id', Auth::id())
            )
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->firstOrFail();

            if ($cartItem->quantity < $this->stockCheck($id)) {
                $cartItem->increment('quantity');
            }
        } else {
            $quantity = $this->cart[$id]['quantity'];
            if ($quantity < $this->stockCheck($id)) {
                $this->cart[$id]['quantity'] = $quantity + 1;
            }
            session()->put('cart', $this->cart);
        }

        $this->dispatch('cart-updated');
        $this->sumPriceProducts();
    }



    public function decrease($id)
    {
        if (Auth::check()) {
            $productId = Str::before($id, '-');
            $variantId = Str::after($id, '-') === 'default' ? null : Str::after($id, '-');

            $cartItem = \App\Models\CartItem::query()->whereHas('cart', fn($q) =>
            $q->where('user_id', Auth::id())
            )
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->firstOrFail();

            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            }
        } else {
            $quantity = $this->cart[$id]['quantity'];
            if ($quantity > 1) {
                $this->cart[$id]['quantity'] = $quantity - 1;
            }
            session()->put('cart', $this->cart);
        }

        $this->dispatch('cart-updated');
        $this->sumPriceProducts();
    }


    public function removeFromCart($id)
    {
        if (Auth::check()) {
            $productId = Str::before($id, '-');
            $variantId = Str::after($id, '-') === 'default' ? null : Str::after($id, '-');

            $cartItem = \App\Models\CartItem::query()->whereHas('cart', fn($q) =>
            $q->where('user_id', Auth::id())
            )
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
            }
        } else {
            if (isset($this->cart[$id])) {
                unset($this->cart[$id]);
                session()->put('cart', $this->cart);
            }
        }

        $this->dispatch('cart-updated');
        $this->sumPriceProducts();
    }


    public function render()
    {
        return view('livewire.payment.cart');
    }
}
