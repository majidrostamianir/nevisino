<?php

namespace App\Livewire\Payment;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Helpers\PriceHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Cart extends Component
{
    public array|null $cart = [];
    public int $sum = 0;
    public int $total = 0;

    protected $listeners = ['cart-updated' => 'updateCart'];
    public ?User $user = null;
    public Collection $orders;
    public int $free_shipping_threshold = 0;

    public function mount()
    {
        $this->orders = collect();

        if (Auth::check()) {
            $this->user = Auth::user();

            $this->orders = $this->user->orders()
                ->where('status', 'pending')
                ->latest()
                ->get();
        }
        $this->updateCart();
    }

    public function updateCart()
    {
        if (Auth::check()) {
            $cartItems = \App\Models\CartItem::with('product', 'variant')
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->get();

            $this->cart = [];
            foreach ($cartItems as $item) {
                $key = $item->product_id . '-' . ($item->variant_id ?? 'default');
                $this->cart[$key] = [
                    'id' => $item->product_id,
                    'title' => $item->product->title,
                    'price' => PriceHelper::getProductPrice($item->product),
                    'code' => $item->product->code,
                    'quantity' => $item->quantity,
                    'variant' => $item->variant_id,
                    'variantName' => $item->variant?->name,
                ];
            }
        } else {
            $sessionCart = session('cart', []);
            $this->cart = [];

            foreach ($sessionCart as $key => $item) {
                $product = \App\Models\Product::find($item['id']);
                if (! $product) {
                    continue; // محصول پاک شده، از نمایش حذف شود
                }

                $this->cart[$key] = [
                    'id' => $item['id'],
                    'title' => $item['title'] ?? $product->title,
                    'price' => PriceHelper::getProductPrice($product),
                    'quantity' => $item['quantity'],
                    'variant' => $item['variant'] ?? null,
                    'variantName' => $item['variantName'] ?? null,
                ];
            }
            // ← دیگر session()->put نداریم
        }

        if ($this->cart) {
            $this->sumPriceProducts();
        }
    }
    public function sumPriceProducts()
    {
        if (Auth::check()) {
            $cartItems = \App\Models\CartItem::query()
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->with('product', 'variant')
                ->get();

            $this->sum = $cartItems->sum(function ($item) {
                return PriceHelper::getProductPrice($item->product) * $item->quantity;
            });
        } else {
            $this->sum = collect($this->cart)->sum(
                fn($item) => $item['price'] * $item['quantity']
            );
        }
    }

    public function stockCheck($id)
    {
        $variant = Str::after($id, '-');
        $product = Product::query()->find(Str::before($id, '-'));

        return $variant == "default"
            ? $product->stock
            : ProductVariant::query()->find($variant)->stock;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            $this->dispatch('showNotification', message: 'سبد خرید خالی است', type: 'warning');
        } else {
            session()->put('previous_url', '/checkout');
            return $this->redirect('/checkout', navigate: true);
        }
    }

    public function removeFromCart($id)
    {
        if (Auth::check()) {
            $productId = Str::before($id, '-');
            $variantId = Str::after($id, '-') === 'default' ? null : Str::after($id, '-');

            $cartItem = \App\Models\CartItem::query()
                ->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
            }
        } else {
            $sessionCart = session('cart', []);
            unset($sessionCart[$id]);
            session()->put('cart', $sessionCart);
        }

        $this->dispatch('cart-updated');
        $this->updateCart();
    }
    public function render()
    {
        return view('livewire.payment.cart');
    }
}