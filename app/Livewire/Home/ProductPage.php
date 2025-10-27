<?php

namespace App\Livewire\Home;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProductPage extends Component
{
    public string $title;
    public Product $product;
    public string $message = '';
    public string $quantity = "۱";
    public string $src;
    public Collection $images;
    public string $selectedVariant = "1";

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_title', '=', $this->title)->firstOrFail();
        $path = 'products/' . $this->product->id . '/large';

        $this->images = collect(Storage::disk('public')->files($path))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->sortBy(fn($name) => intval($name))
            ->values();

        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $this->images[0] . '.webp');
    }

    public function updatedSelectedVariant($id): void
    {
        $this->quantity = "۱";
        if ($id != "")
            $this->setImage($id);
    }

    public function setImage($id): void
    {
        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $id . '.webp');
    }


    public function stockCheck()
    {
        if ($this->selectedVariant == "") {
            return $this->product->stock;
        } else {
            return ProductVariant::query()->find($this->selectedVariant)->stock;
        }
    }

    public function increase(): void
    {
        $enQuantity = (int)persian_to_english_num($this->quantity);
        if ($enQuantity < $this->stockCheck()) {
            $this->quantity = english_to_persian_num($enQuantity + 1);
        } elseif ($enQuantity >= $this->stockCheck()) {
            $this->quantity = english_to_persian_num($this->stockCheck());

        }
    }

    public function decrease(): void
    {
        $enQuantity = (int)persian_to_english_num($this->quantity);
        if ($enQuantity > $this->stockCheck()) {
            $this->quantity = english_to_persian_num($this->stockCheck());
        } elseif ($enQuantity > 1) {
            $this->quantity = english_to_persian_num($enQuantity - 1);
        }
    }

    public function updatedQuantity(): void
    {
        $enQuantity = (int)persian_to_english_num($this->quantity);
        if ($enQuantity > $this->stockCheck()) {
            $this->quantity = english_to_persian_num($this->stockCheck());
        } elseif ($enQuantity < 1) {
            $this->quantity = english_to_persian_num(1);
        }
    }

    public function addToCart()
    {
        $this->validate([
            'selectedVariant' => [
                Rule::when($this->product->variant !== null, 'required'), // وقتی محصول variant داره
                Rule::when($this->product->variant === null, 'prohibited'), // وقتی نداره باید خالی باشه
            ],
        ]);

        $key = $this->product->id . '-' . ($this->selectedVariant ?? 'default');
        $maxStock = $this->stockCheck();
        if ($maxStock > 0) {
            if (Auth::check()) {
                $cart = Auth::user()->cart()->firstOrCreate();
                $cartItem = $cart->items()->where('product_id', $this->product->id)
                    ->where('variant_id', $this->selectedVariant ?? null)
                    ->first();
                $requestedQuantity = (int)persian_to_english_num($this->quantity);

                if ($cartItem) {
                    if ($cartItem->quantity >= $maxStock) {
                        $this->dispatch('showNotification',
                            message: $this->product->variant
                                ? 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید لطفا ' . '<strong>' . $this->product->variant . '</strong>' . ' دیگری را انتخاب کنید'
                                : 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید',
                            type: 'warning'
                        );
                    } elseif ($cartItem->quantity + $requestedQuantity > $maxStock) {
                        $cartItem->quantity = $maxStock;
                        $cartItem->save();
                        $this->dispatch('showNotification',
                            message: 'محصول با موفقیت به سبد خرید اضافه شد',
                            type: 'success'
                        );
                    } else {
                        $cartItem->quantity += $requestedQuantity;
                        $cartItem->save();
                        $this->dispatch('showNotification',
                            message: 'محصول با موفقیت به سبد خرید اضافه شد',
                            type: 'success'
                        );
                    }
                } else {
                    $cart->items()->create([
                        'product_id' => $this->product->id,
                        'variant_id' => $this->selectedVariant ?? null,
                        'quantity' => $requestedQuantity,
                    ]);
                    $this->dispatch('showNotification',
                        message: 'محصول با موفقیت به سبد خرید اضافه شد',
                        type: 'success'
                    );
                }
            } else {
                $cart = session()->get('cart', []);
                $requestedQuantity = (int)persian_to_english_num($this->quantity);

                if (isset($cart[$key])) {
                    if ($cart[$key]['quantity'] >= $maxStock) {
                        $this->dispatch('showNotification',
                            message: $this->product->variant
                                ? 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید لطفا ' . '<strong>' . $this->product->variant . '</strong>' . ' دیگری را انتخاب کنید'
                                : 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید',
                            type: 'warning'
                        );
                    } elseif ($cart[$key]['quantity'] + $requestedQuantity > $maxStock) {
                        $cart[$key]['quantity'] = $maxStock;
                        session()->put('cart', $cart);
                        $this->dispatch('showNotification',
                            message: 'محصول با موفقیت به سبد خرید اضافه شد',
                            type: 'success'
                        );
                    } else {
                        $cart[$key]['quantity'] += $requestedQuantity;
                        session()->put('cart', $cart);
                        $this->dispatch('showNotification',
                            message: 'محصول با موفقیت به سبد خرید اضافه شد',
                            type: 'success'
                        );
                    }
                } else {
                    $cart[$key] = [
                        'id' => $this->product->id,
                        'title' => $this->product->title,
                        'price' => ProductVariant::query()->find($this->selectedVariant)->price ?? $this->product->price,
                        'code' => $this->product->code,
                        'quantity' => $requestedQuantity,
                        'variant' => $this->selectedVariant ?? null,
                        'variantName' => ProductVariant::query()->find($this->selectedVariant)->name ?? null,
                    ];
                    session()->put('cart', $cart);
                    $this->dispatch('showNotification',
                        message: 'محصول با موفقیت به سبد خرید اضافه شد',
                        type: 'success'
                    );
                }
            }
            $this->dispatch('cart-updated');
        }else{
            $this->dispatch('showNotification',
                message: $this->product->variant
                    ? 'موجودی این محصول به اتمام رسیده است لطفا ' . '<strong>' . $this->product->variant . '</strong>' . ' دیگری را انتخاب کنید'
                    : 'موجودی این محصول به اتمام رسیده است',
                type: 'warning'
            );
        }
    }


    public function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.home.product-page')->layout('components.layouts.product');
    }
}
