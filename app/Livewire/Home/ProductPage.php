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
    public int $selectedVariant = 1;
    public int $stock;

    public function mount(): void
    {
        $this->product = Product::query()->where('dashed_title', '=', $this->title)->firstOrFail();

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
        $this->validate([
            'selectedVariant' => [
                Rule::when($this->product->variant !== null, 'required'), // وقتی محصول variant داره
                Rule::when($this->product->variant === null, 'required|in:1'), // وقتی نداره باید 1 باشه
            ],
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
        $isAutoDistribution = $this->product->variant !== null && $this->selectedVariant == 1; // واریانت‌دار + توزیع خودکار
        $isSpecificVariant = $this->product->variant !== null && $this->selectedVariant > 1; // واریانت‌دار + واریانت خاص

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
        $variant = ($variantId > 0) ? ProductVariant::query()->find($variantId) : null;

        $cart[$key] = [
            'id' => $productId,
            'title' => $this->product->title,
            // اگر واریانت پیدا نشد (حالت بدون واریانت)، از قیمت محصول استفاده می‌شود
            'price' => $variant->price ?? $this->product->price,
            'code' => $this->product->code,
            'quantity' => $quantity,
            'variant' => $variantId,
            'variantName' => $variant->name ?? null,
        ];

        session()->put('cart', $cart);
        return $cart;
    }

    /*if (Auth::check()) {

        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();
        $requestedQuantity = (int)persian_to_english_num($this->quantity);
        if ($this->product->variant && $this->selectedVariant == 1) {

            // 1. دریافت موجودی قابل استفاده واریانت‌ها
            $availableVariants = $this->getAvailableVariantsStock();

            // بررسی مجموع موجودی‌های قابل استفاده
            $totalAvailableStock = $availableVariants->sum('available_stock');
            if ($totalAvailableStock < (int)persian_to_english_num($this->quantity)) {
                // اگر موجودی کلی کافی نیست، تعداد درخواستی را به موجودی کلی محدود می‌کنیم
                $requestedQuantity = $totalAvailableStock;
            }

            if ($requestedQuantity > 0) {
                // 2. توزیع تعداد درخواستی بین واریانت‌ها
                $distributionResult = $this->distributeQuantityToVariants($availableVariants, $requestedQuantity);
                $variantUpdates = $distributionResult['updates'];

                if (!empty($variantUpdates)) {
                    // 3. اعمال تغییرات به سبد خرید
                    foreach ($variantUpdates as $variantId => $addToQuantity) {
                        // پیدا کردن یا ایجاد آیتم سبد خرید
                        $cartItem = $cart->items()
                            ->where('product_id', $this->product->id)
                            ->where('variant_id', $variantId)
                            ->first();

                        if ($cartItem) {
                            $cartItem->quantity += $addToQuantity;
                            $cartItem->save();
                        } else {
                            $cart->items()->create([
                                'product_id' => $this->product->id,
                                'variant_id' => $variantId,
                                'quantity' => $addToQuantity,
                            ]);
                        }
                    }

                    // 4. نمایش پیام موفقیت‌آمیز
                    $this->dispatch('showNotification',
                        message: 'محصول با موفقیت به سبد خرید اضافه شد',
                        type: 'success'
                    );

                    // 5. بررسی وجود باقی‌مانده از درخواست
                    if ($distributionResult['remaining_quantity'] > 0) {
                        // این نباید اتفاق بیفتد مگر در مواردی که $maxStock به درستی محاسبه نشده باشد
                        // یا منطق توزیع نتوانسته همه را مصرف کند.
                        // در این حالت می‌توان یک پیام هشدار اضافه داد.
                    }

                } else {
                    // اگر هیچ توزیعی انجام نشد (مثلاً اگر $maxStock به درستی محاسبه نشده و 0 بوده)
                    $this->dispatch('showNotification',
                        message: 'متاسفانه موجودی کافی برای انجام درخواست شما وجود ندارد.',
                        type: 'warning'
                    );
                }


            }
        } elseif ($this->product->variant && $this->selectedVariant > 1) {
            $variantId = $this->selectedVariant;

            // 1. اطلاعات واریانت و موجودی کل آن را از دیتابیس می‌خوانیم
            $variant = ProductVariant::query()->find($variantId);

            // اگر واریانت پیدا نشد یا موجودی آن صفر بود، هشدار می‌دهیم
            if (!$variant || $variant->stock <= 0) {
                $this->dispatch('showNotification',
                    message: 'موجودی این واریانت (' . $variant->name . ') به اتمام رسیده است.',
                    type: 'warning'
                );
                // از ادامه عملیات جلوگیری می‌کنیم
                $this->dispatch('cart-updated');
                return;
            }

            $maxVariantStock = $variant->stock;
            $variantName = $variant->name;

            // 2. بررسی می‌کنیم آیا این واریانت خاص از قبل در سبد خرید موجود است
            $cartItem = $cart->items()
                ->where('product_id', $this->product->id)
                ->where('variant_id', $variantId)
                ->first();

            $inCartQuantity = $cartItem ? $cartItem->quantity : 0;

            // 3. اعمال منطق بررسی و به‌روزرسانی موجودی
            if ($inCartQuantity >= $maxVariantStock) {
                // موجودی کل واریانت از قبل در سبد خرید است
                $this->dispatch('showNotification',
                    message: 'شما تمام موجودی این ' . '<strong>' . $variantName . '</strong>' . ' را در سبد خرید خود قرار داده اید.',
                    type: 'warning'
                );
            } elseif ($inCartQuantity + $requestedQuantity > $maxVariantStock) {
                // تعداد درخواستی از موجودی کل بیشتر است، پس تا سقف مجاز اضافه می‌کنیم
                $newQuantity = $maxVariantStock;
                $addedQuantity = $newQuantity - $inCartQuantity;

                if ($cartItem) {
                    $cartItem->quantity = $newQuantity;
                    $cartItem->save();
                } else {
                    // اگر آیتم قبلاً نبود و درخواست اولیه از سقف بیشتر بود
                    $cart->items()->create([
                        'product_id' => $this->product->id,
                        'variant_id' => $variantId,
                        'quantity' => $newQuantity,
                    ]);
                }

                $this->dispatch('showNotification',
                    message: 'تنها ' . '<strong>' . $addedQuantity . '</strong>' . ' عدد از ' . '<strong>' . $variantName . '</strong>' . ' به دلیل محدودیت موجودی، به سبد خرید اضافه شد.',
                    type: 'success'
                );
            } else {
                // به اندازه تعداد درخواستی اضافه می‌کنیم
                if ($cartItem) {
                    $cartItem->quantity += $requestedQuantity;
                    $cartItem->save();
                } else {
                    $cart->items()->create([
                        'product_id' => $this->product->id,
                        'variant_id' => $variantId,
                        'quantity' => $requestedQuantity,
                    ]);
                }
                $this->dispatch('showNotification',
                    message: 'محصول با موفقیت به سبد خرید اضافه شد',
                    type: 'success'
                );
            }
        }
    } else {
        // فرض بر این است که این کد داخل بلاک 'else' برای حالت مهمان قرار می‌گیرد
        $cart = session()->get('cart', []);
        $requestedQuantity = (int)persian_to_english_num($this->quantity);

        if ($this->product->variant && $this->selectedVariant == 1) {

            // 1. دریافت تمام واریانت‌های محصول و موجودی کلی آن‌ها
            // فرض می‌کنیم محصول دارای رابطه‌ای به نام 'variants' است
            $variants = $this->product->variants;
            $availableVariants = collect([]);

            // 2. محاسبه موجودی قابل استفاده (موجودی کل - موجودی در سشن)
            $variants->each(function ($variant) use (&$availableVariants, $cart) {
                // کلید سشن برای این واریانت خاص (فقط در حالت انتخاب واریانت خاص استفاده می‌شود اما اینجا نیاز به استخراج آن داریم)
                $sessionKey = $this->product->id . '-' . $variant->id;

                $inCart = $cart[$sessionKey]['quantity'] ?? 0;
                $availableStock = $variant->stock - $inCart;

                if ($availableStock > 0) {
                    $availableVariants->push((object)[
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'stock' => $variant->stock,
                        'in_cart' => $inCart,
                        'available_stock' => $availableStock,
                        'session_key' => $sessionKey, // کلید سشن برای این واریانت
                    ]);
                }
            });

            $totalAvailableStock = $availableVariants->sum('available_stock');

            if ($totalAvailableStock < $requestedQuantity) {
                $requestedQuantity = $totalAvailableStock;
            }

            if ($requestedQuantity > 0) {
                // 3. استفاده از همان متد توزیع (distributeQuantityToVariants) که قبلاً تعریف کردیم
                // اگر این متد در لایو وایر کامپوننت تعریف شده باشد، می‌توان از آن استفاده کرد.
                $distributionResult = $this->distributeQuantityToVariants($availableVariants, $requestedQuantity);
                $variantUpdates = $distributionResult['updates'];

                if (!empty($variantUpdates)) {
                    // 4. اعمال تغییرات به سبد خرید در سشن
                    foreach ($variantUpdates as $variantId => $addToQuantity) {
                        $variantInfo = $availableVariants->firstWhere('id', $variantId);
                        $sessionKey = $variantInfo->session_key;

                        $currentQuantity = $cart[$sessionKey]['quantity'] ?? 0;
                        $newQuantity = $currentQuantity + $addToQuantity;

                        // به‌روزرسانی یا ایجاد آیتم سشن
                        if (isset($cart[$sessionKey])) {
                            $cart[$sessionKey]['quantity'] = $newQuantity;
                        } else {
                            // بازیابی اطلاعات برای ذخیره در سشن
                            $variantModel = ProductVariant::query()->find($variantId);

                            $cart[$sessionKey] = [
                                'id' => $this->product->id,
                                'title' => $this->product->title,
                                'price' => $variantModel->price ?? $this->product->price,
                                'code' => $this->product->code,
                                'quantity' => $newQuantity, // مقدار جدید
                                'variant' => $variantId,
                                'variantName' => $variantModel->name,
                            ];
                        }
                    }

                    session()->put('cart', $cart);

                    $this->dispatch('showNotification',
                        message: 'محصولات با موفقیت به سبد خرید اضافه شدند.',
                        type: 'success'
                    );
                } else {
                    $this->dispatch('showNotification',
                        message: 'متاسفانه موجودی کافی برای انجام درخواست شما وجود ندارد.',
                        type: 'warning'
                    );
                }

            } else {
                $this->dispatch('showNotification',
                    message: 'شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.',
                    type: 'warning'
                );
            }
        } elseif ($this->product->variant && $this->selectedVariant > 1) {
            $variantId = $this->selectedVariant;
            $key = $this->product->id . '-' . $variantId;

            // 1. اطلاعات واریانت و موجودی کل آن را از دیتابیس می‌خوانیم
            $variant = ProductVariant::query()->find($variantId);

            if (!$variant || $variant->stock <= 0) {
                $this->dispatch('showNotification',
                    message: 'موجودی این واریانت به اتمام رسیده است.',
                    type: 'warning'
                );
                $this->dispatch('cart-updated');
                return;
            }

            $maxVariantStock = $variant->stock;
            $variantName = $variant->name;

            // 2. استخراج موجودی فعلی در سشن
            $inCartQuantity = $cart[$key]['quantity'] ?? 0;

            // 3. اعمال منطق بررسی و به‌روزرسانی موجودی
            if ($inCartQuantity >= $maxVariantStock) {
                // موجودی کل واریانت از قبل در سبد خرید است
                $this->dispatch('showNotification',
                    message: 'شما تمام موجودی این ' . '<strong>' . $variantName . '</strong>' . ' را در سبد خرید خود قرار داده اید.',
                    type: 'warning'
                );
            } elseif ($inCartQuantity + $requestedQuantity > $maxVariantStock) {
                // تعداد درخواستی از موجودی کل بیشتر است، پس تا سقف مجاز اضافه می‌کنیم
                $newQuantity = $maxVariantStock;
                $addedQuantity = $newQuantity - $inCartQuantity;

                // به‌روزرسانی یا ایجاد آیتم سشن
                $cart[$key] = [
                    'id' => $this->product->id,
                    'title' => $this->product->title,
                    'price' => $variant->price ?? $this->product->price,
                    'code' => $this->product->code,
                    'quantity' => $newQuantity,
                    'variant' => $variantId,
                    'variantName' => $variantName,
                ];

                session()->put('cart', $cart);

                $this->dispatch('showNotification',
                    message: 'تنها ' . '<strong>' . $addedQuantity . '</strong>' . ' عدد از ' . '<strong>' . $variantName . '</strong>' . ' به دلیل محدودیت موجودی، به سبد خرید اضافه شد.',
                    type: 'success'
                );
            } else {
                // به اندازه تعداد درخواستی اضافه می‌کنیم
                $newQuantity = $inCartQuantity + $requestedQuantity;

                // به‌روزرسانی یا ایجاد آیتم سشن
                if (isset($cart[$key])) {
                    $cart[$key]['quantity'] = $newQuantity;
                } else {
                    $cart[$key] = [
                        'id' => $this->product->id,
                        'title' => $this->product->title,
                        'price' => $variant->price ?? $this->product->price,
                        'code' => $this->product->code,
                        'quantity' => $newQuantity,
                        'variant' => $variantId,
                        'variantName' => $variantName,
                    ];
                }

                session()->put('cart', $cart);

                $this->dispatch('showNotification',
                    message: 'محصول با موفقیت به سبد خرید اضافه شد',
                    type: 'success'
                );
            }
        }
    }
    $this->dispatch('cart-updated');

}


protected
function getAvailableVariantsStock(): \Illuminate\Support\Collection
{
    $variants = $this->product->variants;

    // پیدا کردن آیتم‌های سبد خرید مربوط به این محصول
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
            'name' => $variant->name, // برای نمایش
            'stock' => $variant->stock, // موجودی کل
            'in_cart' => $inCart, // مقدار فعلی در سبد خرید
            'available_stock' => max(0, $availableStock), // موجودی قابل استفاده جدید
        ];
    })->filter(fn($v) => $v->available_stock > 0); // فقط واریانت‌های با موجودی قابل استفاده
}

protected
function distributeQuantityToVariants(\Illuminate\Support\Collection $availableVariants, int $requestedQuantity): array
{
    $updates = [];
    $remainingQuantity = $requestedQuantity;
    $remainingStocks = $availableVariants->keyBy('id');

    // چهار سطح اولویت برای حداقل موجودی باقی‌مانده
    $minStockLevels = [3, 2, 1, 0];

    foreach ($minStockLevels as $minReserve) {
        if ($remainingQuantity <= 0) break;

        // مرتب‌سازی بر اساس موجودی قابل استفاده نزولی (از زیاد به کم)
        $variantsToDistribute = $remainingStocks
            ->filter(fn($v) => $v->available_stock > $minReserve)
            ->sortByDesc('available_stock');

        foreach ($variantsToDistribute as $variant) {
            if ($remainingQuantity <= 0) break;

            // حداکثر مقداری که می‌توان از این واریانت برداشت کرد
            // = (موجودی فعلی قابل استفاده) - (حداقل موجودی رزرو)
            $canTake = $variant->available_stock - $minReserve;
            $take = min($remainingQuantity, $canTake);

            if ($take > 0) {
                // به‌روزرسانی مقادیر
                $updates[$variant->id] = ($updates[$variant->id] ?? 0) + $take;
                $remainingQuantity -= $take;
                $remainingStocks[$variant->id]->available_stock -= $take; // به‌روزرسانی برای تکرار بعدی
            }
        }
    }

    // اگر باز هم مقداری باقی مانده بود و تنها گزینه صفر کردن موجودی است (سطح 0)
    // این در واقع تکرار مرحله آخر است اما برای اطمینان واریانت‌ها را مرتب می‌کنیم.
    if ($remainingQuantity > 0) {
        $variantsToDistribute = $remainingStocks
            ->filter(fn($v) => $v->available_stock > 0)
            ->sortByDesc('available_stock'); // توزیع به ترتیب بیشترین موجودی

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
}*/

    public
    function render(): \Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
    {
        return view('livewire.home.product-page')->layout('components.layouts.product');
    }
}
