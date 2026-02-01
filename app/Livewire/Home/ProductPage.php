<?php

namespace App\Livewire\Home;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ProductPage extends Component
{
    public string $title;
    public Product $product;
    public string $message = '';
    public string $quantity = "۱";
    public string $src;
    public Collection $images;
    public int $selectedVariant = 1;
    public int $stock;

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_url', '=', $this->title)->firstOrFail();

        $path = 'products/' . $this->product->id . '/large';
        $this->images = collect(Storage::disk('public')->files($path))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->sortBy(fn($name) => intval($name))
            ->values();

        $this->src = asset('storage/products/' . $this->product->id . '/large/' . $this->images[0] . '.webp');
        $this->stock = $this->stockCheck();
    }

    public function updatedSelectedVariant($id): void
    {
        $this->stock = $this->stockCheck();
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

        // 1. اعتبارسنجی
        $this->validate(
            [
            'selectedVariant' =>'required|int|min:1',
            'quantity' => 'required|string|min:1|max:5',
        ]);

        // 2. آماده‌سازی اولیه داده‌ها
        $requestedQuantity = (int)persian_to_english_num($this->quantity);
        if ($requestedQuantity <= 0) {
            $this->dispatch('showNotification', message: 'مقدار درخواستی نامعتبر است.', type: 'error');
            return;
        }


        // *** تعریف شرط‌های جدید برای رفع تداخل ***
        $isNoVariant = $this->product->variant === null; // حالت جدید: محصول بدون واریانت
        $isAutoDistribution = $this->product->variant !== null && $this->selectedVariant < 1000; // واریانت‌دار + توزیع خودکار
        $isSpecificVariant = $this->product->variant !== null && $this->selectedVariant >= 1000; // واریانت‌دار + واریانت خاص

        // 3. مسیریابی بر اساس وضعیت لاگین
        if (Auth::check()) {
            $this->handleAuthenticatedCart($requestedQuantity, $isAutoDistribution, $isSpecificVariant, $isNoVariant);
        } else {
            $this->handleGuestCart($requestedQuantity, $isAutoDistribution, $isSpecificVariant, $isNoVariant);
        }
        $this->dispatch('cart-updated');
    }


    protected function handleAuthenticatedCart(int $requestedQuantity, bool $isAutoDistribution, bool $isSpecificVariant, bool $isNoVariant) // آرگومان جدید
    {
        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();
        $productId = $this->product->id;
        $successMessage = 'محصول با موفقیت به سبد خرید اضافه شد';

        if ($isAutoDistribution) {
            // حالت ۱: کاربر لاگین + واریانت‌دار + توزیع خودکار (بدون تغییر)
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
            // حالت ۲: کاربر لاگین + واریانت‌دار + واریانت خاص (بدون تغییر)
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
            // *** حالت ۳: کاربر لاگین + بدون واریانت (جدید) ***

            // variant_id برای محصول بدون واریانت null یا 0 در نظر گرفته می‌شود.
            $variantId = null;
            $maxStock = $this->product->stock; // موجودی از فیلد stock خود محصول خوانده می‌شود

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
            // حالت نامعتبر (اگر هیچ یک از شرط‌ها صدق نکند)
            $this->dispatch('showNotification', message: 'عملیات نامعتبر.', type: 'error');
        }
    }

    /**
     * مدیریت سبد خرید کاربر مهمان (سشن)
     */
    protected function handleGuestCart(int $requestedQuantity, bool $isAutoDistribution, bool $isSpecificVariant, bool $isNoVariant) // آرگومان جدید
    {
        $cart = session()->get('cart', []);
        $productId = $this->product->id;
        $successMessage = 'محصول با موفقیت به سبد خرید اضافه شد';

        if ($isAutoDistribution) {
            // حالت ۴: مهمان + واریانت‌دار + توزیع خودکار (بدون تغییر)
            // ... (منطق قبلی)
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
            // حالت ۵: مهمان + واریانت‌دار + واریانت خاص (بدون تغییر)
            // ... (منطق قبلی)
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
            // *** حالت ۶: مهمان + بدون واریانت (جدید) ***

            // variant_id برای محصول بدون واریانت null یا 0 در نظر گرفته می‌شود.
            $variantId = 0; // در سشن از 0 به جای null استفاده می‌شود تا کلید معتبر باشد
            $key = $productId . '-' . $variantId;
            $maxStock = $this->product->stock; // موجودی از فیلد stock خود محصول خوانده می‌شود

            if ($maxStock <= 0) {
                $this->dispatch('showNotification', message: 'موجودی این محصول به اتمام رسیده است.', type: 'warning');
                return;
            }

            $inCartQuantity = $cart[$key]['quantity'] ?? 0;
            $newTotalQuantity = $inCartQuantity + $requestedQuantity;
            $qtyToSave = min($newTotalQuantity, $maxStock);

            // در اینجا variantId را 0 (به عنوان واریانت پیش‌فرض) ارسال می‌کنیم
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
            // حالت نامعتبر (اگر هیچ یک از شرط‌ها صدق نکند)
            $this->dispatch('showNotification', message: 'عملیات نامعتبر.', type: 'error');
        }
    }

    /**
     * متد کمکی: دریافت موجودی خالص واریانت‌ها از دیتابیس (پس از کسر سبد خرید کاربر)
     */
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

    /**
     * متد کمکی: توزیع تعداد درخواستی بین واریانت‌ها بر اساس اولویت حفظ موجودی
     */
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

        // مرحله نهایی: مصرف موجودی باقیمانده (تکرار سطح 0)
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

    /**
     * متد کمکی: افزودن یا به‌روزرسانی آیتم در سبد خرید دیتابیس (لاگین)
     */
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

    /**
     * متد کمکی: افزودن یا به‌روزرسانی آیتم در سبد خرید سشن (مهمان)
     */
    protected function updateSessionCartItem(array $cart, int $productId, int $variantId, int $quantity): array
    {
        $key = $productId . '-' . $variantId;

        // اگر variantId برابر 0 یا null بود، دیگر نباید از ProductVariant کوئری گرفت.
        if ($variantId >= 1000) {
            $variant = ProductVariant::query()->find($variantId);
        } else {
            $variant = null;
            $variantId = null;
        }

        $cart[$key] = [
            'id' => $productId,
            'title' => $this->product->title,
            'price' => $this->product->discounted_price ?? $this->product->price,
            'code' => $this->product->code,
            'quantity' => $quantity,
            'variant' => $variantId,
            'variantName' => $variant->name ?? null,
        ];

        session()->put('cart', $cart);
        return $cart;
    }

    public
    function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        $relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where(function ($query) {
                // محصول خودش موجودی داره
                $query->where('stock', '>', 0)
                    // یا حداقل یکی از واریانت‌ها موجود باشه
                    ->orWhereHas('variants', function ($q) {
                        $q->where('stock', '>', 0);
                    });
            })
            ->limit(8)
            ->get();
        return view('livewire.home.product-page' , compact('relatedProducts'))->layout('components.layouts.product');
    }
}
