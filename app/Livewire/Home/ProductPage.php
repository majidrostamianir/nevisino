<?php

namespace App\Livewire\Home;

use App\Helpers\PriceHelper;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProductPage extends Component
{
    public ?string $title = null;
    public int $npi, $nvi;
    public Product $product;
    public string $message = '';
    public string $quantity = "۱";
    public string $src;
    public Collection $images;
    public int $selectedVariant = 1;
    public int $stock;

    public $finalPrice;
    public $basePrice;
    public $hasDiscount;
    public $msg;

    /* ------------------------------------------------------------------ */
    /*  داده‌های نمایشی که در ویو استفاده می‌شوند                         */
    /* ------------------------------------------------------------------ */
    public bool $inStock = false;
    public ?Category $category = null;
    public ?Category $parentCategory = null;

    public string $crossTitle = '';
    public string $crossLabel = '';
    public string $crossDesc = '';
    public string $crossBaseUrl = '';
    public string $crossUrl = '';
    public array $crossTheme = [];

    public array $displayAttributes = [];
    public array $variantNameMap = [];   // [variant_id => name]
    public ?string $selectedVariantName = null;

    /* ------------------------------------------------------------------ */

    public function mount()
    {
        $this->product = Product::query()->findOrFail($this->npi);

        // ۱. اعتبارسنجی nvi
        if (request()->has('nvi')) {
            if (in_array((int)request()->query('nvi'), $this->product->variants()->pluck('id')->toArray())) {
                $this->selectedVariant = (int)request()->query('nvi');
            } else {
                return redirect()->to(
                    route('product-page', [
                        'npi' => $this->product->id,
                        'title' => $this->product->dashed_url,
                    ]),
                    301
                );
            }
        }

        // ۲. ریدایرکت دائمی در صورت نامعتبر بودن title
        if ($this->title !== $this->product->dashed_url) {
            $params = [
                'npi' => $this->product->id,
                'title' => $this->product->dashed_url,
            ];
            if (request()->has('nvi')) {
                $params['nvi'] = request()->query('nvi');
            }
            return redirect()->to(route('product-page', $params), 301);
        }

        // ۳. بارگذاری تصاویر و قیمت
        $path = 'products/' . $this->product->id . '/large';
        $this->images = collect(Storage::disk('public')->files($path))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->sortBy(fn($name) => intval($name))
            ->values();

        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $this->selectedVariant . '.webp' ?? $this->images[0] . '.webp');
        $this->stock = $this->stockCheck();

        $this->finalPrice = PriceHelper::getProductPrice($this->product);
        $this->basePrice = PriceHelper::getBasePrice($this->product);
        $this->hasDiscount = PriceHelper::hasDiscount($this->product);

        // ۴. آماده‌سازی داده‌های ویو
        $this->loadViewData();
    }

    protected function loadViewData(): void
    {
        $this->inStock = (is_null($this->product->variant) && $this->product->stock > 0)
            || (!is_null($this->product->variant) && $this->product->variants->sum('stock') > 0);

        $this->category = Category::query()->find($this->product->category_id);
        $this->parentCategory = $this->category
            ? Category::query()->find($this->category->parent_id)
            : null;

        $this->variantNameMap = $this->product->variants->pluck('name', 'id')->toArray();

        $this->displayAttributes = [];
        foreach ($this->product->attributes as $attr) {
            $value = AttributeValue::query()->find($attr->pivot->attribute_value_id);
            if ($value) {
                $this->displayAttributes[] = [
                    'name' => $attr->name,
                    'value' => $value->value,
                ];
            }
        }

        $this->selectedVariantName = $this->resolveSelectedVariantName();
        $this->buildCrossSite();
    }

    protected function buildCrossSite(): void
    {
        $isGhestiSite = str_starts_with(request()->getHost(), 'ghesti.');
        $path = '/product/' . $this->product->id . '/' . $this->product->dashed_url;

        if ($isGhestiSite) {
            // مقصد: سایت نقدی (استفاده از رنگ‌های استاندارد آبی برای جلوگیری از تداخل با تم بنفش)
            $this->crossTitle = 'خرید نقدی';
            $this->crossLabel = 'برای خرید نقدی کلیک کنید';
            $this->crossDesc = 'همین کالا را بصورت نقدی با قیمت ' . english_to_persian_num(number_format($this->product->price)) . ' تومان بخر';
            $this->msg = 'با تایید، به فروشگاه نقدی ما منتقل می‌شوید. توجه داشته باشید که در فروشگاه نقدی، کل سبد خرید شما به صورت نقدی محاسبه می‌شود.';
            $this->crossBaseUrl = 'https://nevisino.ir' . $path;
            $this->crossTheme = [
                'border' => 'border-blue-300',
                'bg' => 'bg-gradient-to-l from-blue-50 via-blue-100/40 to-white',
                'iconBg' => 'bg-blue-500',
                'textDark' => 'text-blue-900',
                'textSoft' => 'text-blue-700',
                'btn' => 'bg-blue-500 hover:bg-blue-600',
                'icon' => 'cash',
            ];
        } else {
            // مقصد: سایت قسطی (استفاده از رنگ‌های بنفش)
            $this->crossTitle = 'خرید اقساطی';
            $this->crossLabel = 'برای خرید قسطی کلیک کنید';
            $this->crossDesc = 'همین کالا را در ۴ قسط ' . english_to_persian_num(number_format(($this->product->installment_price ?? 0) / 4)) . ' تومانی بخر';
            $this->crossBaseUrl = 'https://ghesti.nevisino.ir' . $path;
            $this->msg = 'با تایید، به فروشگاه قسطی ما منتقل می‌شوید. توجه داشته باشید که در نویسینو قسطی، کل سبد خرید شما به صورت اقساطی محاسبه می‌شود.';
            $this->crossTheme = [
                'border' => 'border-purple-300',
                'bg' => 'bg-gradient-to-l from-purple-50 via-purple-100/40 to-white',
                'iconBg' => 'bg-purple-500',
                'textDark' => 'text-purple-900',
                'textSoft' => 'text-purple-700',
                'btn' => 'bg-purple-500 hover:bg-purple-600',
                'icon' => 'installment',
            ];
        }

        $this->refreshCrossUrl();
    }
    protected function refreshCrossUrl(): void
    {
        $this->crossUrl = $this->crossBaseUrl
            . ($this->selectedVariant >= 1000 ? '?nvi=' . $this->selectedVariant : '');
    }

    protected function resolveSelectedVariantName(): ?string
    {
        if ($this->product->variant === null || $this->selectedVariant < 1000) {
            return null;
        }
        return ProductVariant::query()->find($this->selectedVariant)?->name;
    }

    /* ------------------------------------------------------------------ */
    /*  بقیه‌ی متدهای قبلی                                                */
    /* ------------------------------------------------------------------ */

    public function updatedSelectedVariant($id): void
    {
        $this->stock = $this->stockCheck();
        $this->quantity = "۱";
        $this->selectedVariantName = $this->resolveSelectedVariantName();
        $this->refreshCrossUrl();

        if ($id != "") {
            $this->setImage($id);
        }
    }

    public function setImage($id): void
    {
        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $id . '.webp');
    }

    public function stockCheck()
    {
        if ($this->product->variant && $this->selectedVariant < 1000) {
            $this->stock = $this->product->variants()->sum('stock');
        } elseif ($this->product->variant && $this->selectedVariant >= 1000) {
            $this->stock = ProductVariant::query()->find($this->selectedVariant)->stock;
        } else {
            $this->stock = $this->product->stock;
        }
        return $this->stock;
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
        $this->stock = 0;
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
            'selectedVariant' => 'required|int|min:1',
            'quantity' => 'required|string|min:1|max:5',
        ]);

        $requestedQuantity = (int)persian_to_english_num($this->quantity);
        if ($requestedQuantity <= 0) {
            $this->dispatch('showNotification', message: 'مقدار درخواستی نامعتبر است.', type: 'error');
            return;
        }

        $isNoVariant = $this->product->variant === null;
        $isAutoDistribution = $this->product->variant !== null && $this->selectedVariant < 1000;
        $isSpecificVariant = $this->product->variant !== null && $this->selectedVariant >= 1000;

        if (Auth::check()) {
            $this->handleAuthenticatedCart($requestedQuantity, $isAutoDistribution, $isSpecificVariant, $isNoVariant);
        } else {
            $this->handleGuestCart($requestedQuantity, $isAutoDistribution, $isSpecificVariant, $isNoVariant);
        }
        $this->dispatch('cart-updated');
    }

    protected function handleAuthenticatedCart(int $requestedQuantity, bool $isAutoDistribution, bool $isSpecificVariant, bool $isNoVariant)
    {
        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();
        $productId = $this->product->id;
        $successMessage = 'محصول با موفقیت به سبد خرید اضافه شد';

        if ($isAutoDistribution) {
            $availableVariants = $this->getAvailableVariantsStock();
            $totalAvailableStock = $availableVariants->sum('available_stock');

            $qtyToProcess = min($requestedQuantity, $totalAvailableStock);

            if ($qtyToProcess <= 0) {
                $this->dispatch('showNotification', message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.', type: 'warning');
                return;
            }

            $distributionResult = $this->distributeQuantityToVariants($availableVariants, $qtyToProcess);

            if (empty($distributionResult['updates'])) {
                $this->dispatch('showNotification', message: 'متاسفانه موجودی کافی برای انجام درخواست شما وجود ندارد.', type: 'warning');
                return;
            }

            foreach ($distributionResult['updates'] as $variantId => $addToQuantity) {
                $cartItem = $cart->items()
                    ->where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->first();

                $newQuantity = ($cartItem ? $cartItem->quantity : 0) + $addToQuantity;
                $this->updateDatabaseCartItem($cart, $productId, $variantId, $newQuantity);
            }

            $this->dispatch('showNotification', message: $successMessage, type: 'success');

        } elseif ($isSpecificVariant) {
            $variantId = $this->selectedVariant;
            $variant = ProductVariant::query()->find($variantId);

            if (!$variant || $variant->stock <= 0) {
                $this->dispatch('showNotification',
                    message: 'موجودی ' . '<strong>' . $this->product->variant . ' ' . $variant->name . '</strong>' . ' تمام شده است',
                    type: 'warning'
                );
                return;
            }

            $maxVariantStock = $variant->stock;
            $cartItem = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $inCartQuantity = $cartItem ? $cartItem->quantity : 0;
            $newTotalQuantity = $inCartQuantity + $requestedQuantity;
            $qtyToSave = min($newTotalQuantity, $maxVariantStock);

            $this->updateDatabaseCartItem($cart, $productId, $variantId, $qtyToSave);

            if ($inCartQuantity >= $maxVariantStock) {
                $this->dispatch('showNotification',
                    message: 'شما تمام موجودی ' . '<strong>' . $this->product->variant . ' ' . $variant->name . '</strong>' . ' را در سبد خرید خود قرار داده اید.', type: 'warning');
            } elseif ($newTotalQuantity > $maxVariantStock) {
                $addedQuantity = $maxVariantStock - $inCartQuantity;
                $this->dispatch('showNotification', message: 'تنها ' . '<strong>' . english_to_persian_num($addedQuantity) . '</strong>' . ' عدد از ' . '<strong>' . $this->product->variant . ' ' . $variant->name . '</strong>' . ' به دلیل محدودیت موجودی، به سبد خرید اضافه شد.', type: 'success');
            } else {
                $this->dispatch('showNotification', message: $successMessage, type: 'success');
            }

        } elseif ($isNoVariant) {
            $variantId = null;
            $maxStock = $this->product->stock;

            if ($maxStock <= 0) {
                $this->dispatch('showNotification', message: 'موجودی این محصول به اتمام رسیده است.', type: 'warning');
                return;
            }

            $cartItem = $cart->items()
                ->where('product_id', $productId)
                ->where('variant_id', $variantId)
                ->first();

            $inCartQuantity = $cartItem ? $cartItem->quantity : 0;
            $newTotalQuantity = $inCartQuantity + $requestedQuantity;
            $qtyToSave = min($newTotalQuantity, $maxStock);

            $this->updateDatabaseCartItem($cart, $productId, $variantId, $qtyToSave);

            if ($inCartQuantity >= $maxStock) {
                $this->dispatch('showNotification', message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.', type: 'warning');
            } elseif ($newTotalQuantity > $maxStock) {
                $addedQuantity = $maxStock - $inCartQuantity;
                $this->dispatch('showNotification', message: 'تنها ' . '<strong>' . english_to_persian_num($addedQuantity) . '</strong>' . ' عدد از این محصول به دلیل محدودیت موجودی، به سبد خرید اضافه شد.', type: 'success');
            } else {
                $this->dispatch('showNotification', message: $successMessage, type: 'success');
            }

        } else {
            $this->dispatch('showNotification', message: 'عملیات نامعتبر.', type: 'error');
        }
    }

    protected function handleGuestCart(int $requestedQuantity, bool $isAutoDistribution, bool $isSpecificVariant, bool $isNoVariant)
    {
        $cart = session()->get('cart', []);
        $productId = $this->product->id;
        $successMessage = 'محصول با موفقیت به سبد خرید اضافه شد';

        if ($isAutoDistribution) {
            $variants = $this->product->variants;
            $availableVariants = collect([]);

            $variants->each(function ($variant) use (&$availableVariants, $cart, $productId) {
                $sessionKey = $productId . '-' . $variant->id;
                $inCart = $cart[$sessionKey]['quantity'] ?? 0;
                $availableStock = $variant->stock - $inCart;

                if ($availableStock > 0) {
                    $availableVariants->push((object)[
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'stock' => $variant->stock,
                        'in_cart' => $inCart,
                        'available_stock' => $availableStock,
                    ]);
                }
            });

            $totalAvailableStock = $availableVariants->sum('available_stock');
            $qtyToProcess = min($requestedQuantity, $totalAvailableStock);

            if ($qtyToProcess <= 0) {
                $this->dispatch('showNotification', message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.', type: 'warning');
                return;
            }

            $distributionResult = $this->distributeQuantityToVariants($availableVariants, $qtyToProcess);

            if (empty($distributionResult['updates'])) {
                $this->dispatch('showNotification', message: 'متاسفانه موجودی کافی برای انجام درخواست شما وجود ندارد.', type: 'warning');
                return;
            }

            foreach ($distributionResult['updates'] as $variantId => $addToQuantity) {
                $sessionKey = $productId . '-' . $variantId;
                $currentQuantity = $cart[$sessionKey]['quantity'] ?? 0;
                $newQuantity = $currentQuantity + $addToQuantity;

                $cart = $this->updateSessionCartItem($cart, $productId, $variantId, $newQuantity);
            }

            $this->dispatch('showNotification', message: 'محصولات با موفقیت به سبد خرید اضافه شدند.', type: 'success');

        } elseif ($isSpecificVariant) {
            $variantId = $this->selectedVariant;
            $variant = ProductVariant::query()->find($variantId);
            $key = $productId . '-' . $variantId;

            if (!$variant || $variant->stock <= 0) {
                $this->dispatch('showNotification', message: 'موجودی این واریانت به اتمام رسیده است.', type: 'warning');
                return;
            }

            $maxVariantStock = $variant->stock;
            $inCartQuantity = $cart[$key]['quantity'] ?? 0;
            $newTotalQuantity = $inCartQuantity + $requestedQuantity;
            $qtyToSave = min($newTotalQuantity, $maxVariantStock);

            $cart = $this->updateSessionCartItem($cart, $productId, $variantId, $qtyToSave);

            if ($inCartQuantity >= $maxVariantStock) {
                $this->dispatch('showNotification', message: 'شما تمام موجودی این ' . '<strong>' . $variant->name . '</strong>' . ' را در سبد خرید خود قرار داده اید.', type: 'warning');
            } elseif ($newTotalQuantity > $maxVariantStock) {
                $addedQuantity = $maxVariantStock - $inCartQuantity;
                $this->dispatch('showNotification', message: 'تنها ' . '<strong>' . english_to_persian_num($addedQuantity) . '</strong>' . ' عدد از ' . '<strong>' . $variant->name . '</strong>' . ' به دلیل محدودیت موجودی، به سبد خرید اضافه شد.', type: 'success');
            } else {
                $this->dispatch('showNotification', message: $successMessage, type: 'success');
            }

        } elseif ($isNoVariant) {
            $variantId = 0;
            $key = $productId . '-' . $variantId;
            $maxStock = $this->product->stock;

            if ($maxStock <= 0) {
                $this->dispatch('showNotification', message: 'موجودی این محصول به اتمام رسیده است.', type: 'warning');
                return;
            }

            $inCartQuantity = $cart[$key]['quantity'] ?? 0;
            $newTotalQuantity = $inCartQuantity + $requestedQuantity;
            $qtyToSave = min($newTotalQuantity, $maxStock);

            $cart = $this->updateSessionCartItem($cart, $productId, $variantId, $qtyToSave);

            if ($inCartQuantity >= $maxStock) {
                $this->dispatch('showNotification', message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.', type: 'warning');
            } elseif ($newTotalQuantity > $maxStock) {
                $addedQuantity = $maxStock - $inCartQuantity;
                $this->dispatch('showNotification', message: 'تنها ' . '<strong>' . english_to_persian_num($addedQuantity) . '</strong>' . ' عدد از این محصول به دلیل محدودیت موجودی، به سبد خرید اضافه شد.', type: 'success');
            } else {
                $this->dispatch('showNotification', message: $successMessage, type: 'success');
            }

        } else {
            $this->dispatch('showNotification', message: 'عملیات نامعتبر.', type: 'error');
        }
    }

    protected function getAvailableVariantsStock(): \Illuminate\Support\Collection
    {
        $variants = $this->product->variants;

        $cartItems = Auth::user()->cart
            ?->items()
            ->where('product_id', $this->product->id)
            ->get()
            ->keyBy('variant_id');

        return $variants->map(function ($variant) use ($cartItems) {
            $inCart = $cartItems->get($variant->id)?->quantity ?? 0;
            $availableStock = $variant->stock - $inCart;

            return (object)[
                'id' => $variant->id,
                'name' => $variant->name,
                'stock' => $variant->stock,
                'in_cart' => $inCart,
                'available_stock' => max(0, $availableStock),
            ];
        })->filter(fn($v) => $v->available_stock > 0);
    }

    protected function distributeQuantityToVariants(\Illuminate\Support\Collection $availableVariants, int $requestedQuantity): array
    {
        $updates = [];
        $remainingQuantity = $requestedQuantity;
        $remainingStocks = $availableVariants->keyBy('id');
        $minStockLevels = [3, 2, 1, 0];

        foreach ($minStockLevels as $minReserve) {
            if ($remainingQuantity <= 0) break;

            $variantsToDistribute = $remainingStocks
                ->filter(fn($v) => $v->available_stock > $minReserve)
                ->sortByDesc('available_stock');

            foreach ($variantsToDistribute as $variant) {
                if ($remainingQuantity <= 0) break;

                $canTake = $variant->available_stock - $minReserve;
                $take = min($remainingQuantity, $canTake);

                if ($take > 0) {
                    $updates[$variant->id] = ($updates[$variant->id] ?? 0) + $take;
                    $remainingQuantity -= $take;
                    $remainingStocks[$variant->id]->available_stock -= $take;
                }
            }
        }

        if ($remainingQuantity > 0) {
            $variantsToDistribute = $remainingStocks
                ->filter(fn($v) => $v->available_stock > 0)
                ->sortByDesc('available_stock');

            foreach ($variantsToDistribute as $variant) {
                if ($remainingQuantity <= 0) break;

                $take = min($remainingQuantity, $variant->available_stock);

                if ($take > 0) {
                    $updates[$variant->id] = ($updates[$variant->id] ?? 0) + $take;
                    $remainingQuantity -= $take;
                    $remainingStocks[$variant->id]->available_stock -= $take;
                }
            }
        }

        return [
            'updates' => $updates,
            'remaining_quantity' => $remainingQuantity,
            'success' => $remainingQuantity === 0,
        ];
    }

    protected function updateDatabaseCartItem($cart, $productId, $variantId, $quantity)
    {
        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->where('variant_id', $variantId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
    }

    protected function updateSessionCartItem(array $cart, int $productId, int $variantId, int $quantity): array
    {
        $key = $productId . '-' . $variantId;

        if ($variantId >= 1000) {
            $variant = ProductVariant::query()->find($variantId);
        } else {
            $variant = null;
            $variantId = null;
        }

        $cart[$key] = [
            'id' => $productId,
            'title' => $this->product->title,
            'code' => $this->product->code,
            'quantity' => $quantity,
            'variant' => $variantId,
            'variantName' => $variant->name ?? null,
        ];

        session()->put('cart', $cart);
        return $cart;
    }

    public function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        $relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where(function ($query) {
                $query->where('stock', '>', 0)
                    ->orWhereHas('variants', function ($q) {
                        $q->where('stock', '>', 0);
                    });
            })
            ->limit(8)
            ->get();

        return view('livewire.home.product-page', compact('relatedProducts'))
            ->layout('components.layouts.product')
            ->title($this->product->title . ' | نویسینو');
    }
}