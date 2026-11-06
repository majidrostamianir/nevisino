<?php

namespace App\Livewire\Home;

use App\Helpers\PriceHelper;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Services\Cart\AddToCartAction;
use App\Services\Cart\VariantSelection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProductPage extends Component
{
    public ?string $title = null;
    public int $npi;

    #[Locked]
    public Product $product;

    #[Locked]
    public Collection $images;

    public int $quantity = 1;
    public string $src;
    public int $selectedVariant = 1;

    public function mount()
    {
        $this->product = Product::query()->findOrFail($this->npi);

        $nvi = request()->has('nvi') ? (int) request()->query('nvi') : null;

        if ($redirect = $this->canonicalRedirect($nvi)) {
            return $redirect;
        }

        if ($nvi !== null) {
            $this->selectedVariant = $nvi;
        }

        $this->images = collect(Storage::disk('public')->files('products/' . $this->product->id . '/large'))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->sortBy(fn($name) => intval($name))
            ->values();

        $this->src = $this->imageUrl($this->initialImage());
    }

    /** اگر nvi نامعتبر یا title اشتباه باشد، ریدایرکت دائمی به آدرس درست */
    protected function canonicalRedirect(?int $nvi): ?RedirectResponse
    {
        if ($nvi !== null && !$this->product->variants->contains('id', $nvi)) {
            return redirect()->to($this->productUrl(), 301);
        }

        if ($this->title !== $this->product->dashed_url) {
            return redirect()->to($this->productUrl($nvi), 301);
        }

        return null;
    }

    public function updatedSelectedVariant($id): void
    {
        $this->quantity = 1;

        if ($id != '') {
            $this->setImage($id);
        }
    }

    public function setImage($id): void
    {
        $this->src = $this->imageUrl($id);
    }

    /* ---------------------------------------------------------------
     |  تعداد
     | --------------------------------------------------------------- */

    public function increase(): void
    {
        $this->quantity = min($this->quantity + 1, $this->stock);
    }

    public function decrease(): void
    {
        if ($this->quantity > $this->stock) {
            $this->quantity = $this->stock;
        } elseif ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max(1, min($this->quantity, $this->stock));
    }

    /* ---------------------------------------------------------------
     |  سبد خرید
     | --------------------------------------------------------------- */

    public function addToCart(AddToCartAction $addToCart): void
    {
        $this->validate([
            'selectedVariant' => 'required|int|min:1',
            'quantity'        => 'required|int|min:1',
        ]);

        $result = $addToCart->handle($this->product, $this->selectedVariant, $this->quantity);

        $this->dispatch('showNotification', message: $result->message, type: $result->type);
        $this->dispatch('cart-updated');
    }

    /* ---------------------------------------------------------------
     |  داده‌های نمایشی (در ویو با $this->name خوانده می‌شوند)
     | --------------------------------------------------------------- */

    #[Computed]
    public function titleFa(): string
    {
        return english_to_persian_num($this->product->title);
    }

    #[Computed]
    public function category(): ?Category
    {
        return Category::query()->find($this->product->category_id);
    }

    #[Computed]
    public function parentCategory(): ?Category
    {
        return $this->category ? Category::query()->find($this->category->parent_id) : null;
    }

    #[Computed]
    public function price(): array
    {
        return [
            'hasDiscount' => PriceHelper::hasDiscount($this->product),
            'base'        => english_to_persian_num(number_format(PriceHelper::getBasePrice($this->product))),
            'final'       => english_to_persian_num(number_format(PriceHelper::getProductPrice($this->product))),
        ];
    }

    #[Computed]
    public function inStock(): bool
    {
        return $this->selection()->totalStock() > 0;
    }

    #[Computed]
    public function stock(): int
    {
        return $this->selection()->stock();
    }

    /** پیام زیر دکمه‌های تعداد (ناموجود / سقف موجودی) */
    #[Computed]
    public function stockNotice(): ?string
    {
        $stock = $this->stock;

        if ($stock === 0) {
            return 'ناموجود';
        }

        if ($this->quantity < $stock) {
            return null;
        }

        $variant = $this->selection()->variant();

        if (!$variant) {
            return 'موجودی انبار ' . english_to_persian_num($stock) . ' عدد است';
        }

        return 'تنها ' . english_to_persian_num($stock) . ' عدد از '
            . $this->product->variant . ' ' . english_to_persian_num($variant->name) . ' باقی مانده است';
    }

    /** تصاویر گالری به همراه نام واریانت و alt */
    #[Computed]
    public function gallery(): Collection
    {
        $variantNames = $this->product->variants->pluck('name', 'id');

        return $this->images->map(function ($image) use ($variantNames) {
            $name = $variantNames->get((int) $image);
            $label = $name !== null ? english_to_persian_num($name) : null;

            return [
                'id'    => $image,
                'label' => $label,
                'alt'   => $label ? $this->titleFa . ' - ' . $label : $this->titleFa,
            ];
        });
    }

    /** ویژگی‌های محصول (نام و مقدار) آماده‌ی نمایش */
    #[Computed]
    public function specifications(): array
    {
        $attributes = $this->product->attributes;

        $values = AttributeValue::query()
            ->whereIn('id', $attributes->map(fn($attr) => $attr->pivot->attribute_value_id)->filter())
            ->pluck('value', 'id');

        return $attributes
            ->filter(fn($attr) => $values->has($attr->pivot->attribute_value_id))
            ->map(fn($attr) => [
                'name'  => english_to_persian_num($attr->name),
                'value' => english_to_persian_num($values[$attr->pivot->attribute_value_id]),
            ])
            ->values()
            ->all();
    }

    #[Computed]
    public function relatedProducts(): Collection
    {
        return Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where(function ($query) {
                // خود محصول موجودی دارد، یا حداقل یکی از واریانت‌ها
                $query->where('stock', '>', 0)
                    ->orWhereHas('variants', fn($q) => $q->where('stock', '>', 0));
            })
            ->limit(8)
            ->get();
    }

    /* ---------------------------------------------------------------
     |  Helpers
     | --------------------------------------------------------------- */

    protected function selection(): VariantSelection
    {
        return new VariantSelection($this->product, $this->selectedVariant);
    }

    /** تصویر اولیه: تصویر واریانت انتخاب‌شده، وگرنه اولین تصویر */
    protected function initialImage()
    {
        if ($this->images->contains($this->selectedVariant)) {
            return $this->selectedVariant;
        }

        return $this->images->first() ?? $this->selectedVariant;
    }

    protected function imageUrl($id): string
    {
        return asset('storage/products/' . $this->product->id . '/large/' . $id . '.webp');
    }

    protected function productUrl(?int $nvi = null): string
    {
        return route('product-page', array_filter([
            'npi'   => $this->product->id,
            'title' => $this->product->dashed_url,
            'nvi'   => $nvi,
        ], fn($value) => $value !== null));
    }

    public function render()
    {
        return view('livewire.home.product-page')
            ->layout('components.layouts.product')
            ->title($this->product->title . ' | نویسینو');
    }
}