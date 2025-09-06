<?php

namespace App\Livewire\Home;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProductPage extends Component
{
    public string $title;
    public Product $product;
    public string $message = '';
    public string $count = "۱";
    public string $src;
    public Collection $images;
    public string|null $selectedVariant = null;

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_title', '=', $this->title)->firstOrFail();
        $path = 'products/' . $this->product->id . '/large';

        $this->images = collect(Storage::disk('public')->files($path))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME)) // گرفتن فقط اسم بدون پسوند
            ->sortBy(fn($name) => intval($name)) // مرتب‌سازی عددی
            ->values(); // ریسِت کلیدها

        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $this->images[0] . '.webp');

    }

    public function updatedSelectedVariant($id): void
    {
        $this->count = "۱";
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
        $enCount = (int)persian_to_english_num($this->count);
        if ($enCount < $this->stockCheck()) {
            $this->count = english_to_persian_num($enCount + 1);
        } elseif ($enCount >= $this->stockCheck()) {
            $this->count = english_to_persian_num($this->stockCheck());

        }
    }

    public function decrease(): void
    {
        $enCount = (int)persian_to_english_num($this->count);
        if ($enCount > $this->stockCheck()) {
            $this->count = english_to_persian_num($this->stockCheck());
        } elseif ($enCount > 1) {
            $this->count = english_to_persian_num($enCount - 1);
        }
    }

    public function updatedCount(): void
    {
        $enCount = (int)persian_to_english_num($this->count);
        if ($enCount > $this->stockCheck()) {
            $this->count = english_to_persian_num($this->stockCheck());
        } elseif ($enCount < 1) {
            $this->count = english_to_persian_num(1);
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
        $cart = session()->get('cart', []);
        $key = $this->product->id . '-' . ($this->selectedVariant ?? 'default');


        if (isset($cart[$key])) {
            if ($cart[$key]['count'] == $this->stockCheck()) {
                if ($this->product->variant) {
                    $this->dispatch('showNotification',
                        message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید لطفا ' . '<strong>' .  $this->product->variant . '</strong>' . ' دیگری را انتخاب کنید',
                        type: 'warning'
                    );
                } else {
                    $this->dispatch('showNotification',
                        message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید',
                        type: 'warning'
                    );
                }
                return;
            } elseif ($cart[$key]['count'] + (int)persian_to_english_num($this->count) > $this->stockCheck()) {
                $cart[$key]['count'] = $this->stockCheck();
            } else {
                $cart[$key]['count'] += (int)persian_to_english_num($this->count);
            }
        } else {
            $cart[$key] = [
                'id' => $this->product->id,
                'title' => $this->product->title,
                'price' => ProductVariant::query()->find($this->selectedVariant)->price ?? $this->product->price,
                'code' => $this->product->code,
                'count' => (int)persian_to_english_num($this->count),
                'variant' => $this->selectedVariant ?? null,
                'variantName' => ProductVariant::query()->find($this->selectedVariant)->name ?? null,
            ];
        }
        session()->put('cart', $cart);
        $this->dispatch('showNotification',
            message: 'محصول با موفقیت به سبد خرید اضافه شد',
            type: 'success'
        );
        $this->dispatch('cart-updated');
    }


    public function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        $product = $this->product;
        $similar = Product::whereHas('urls', function ($query) use ($product) {
            $query->whereIn('urls.id', $product->urls->pluck('id'));
        })
            ->where('id', '!=', $product->id) // خود محصول فعلی رو نیاره
            ->distinct()
            ->get();
        return view('livewire.home.product-page', compact('product', 'similar'))->layout('components.layouts.product');
    }
}
