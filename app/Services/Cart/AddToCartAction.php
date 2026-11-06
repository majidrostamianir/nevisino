<?php

namespace App\Services\Cart;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class AddToCartAction
{
    private const SUCCESS_MESSAGE = 'محصول با موفقیت به سبد خرید اضافه شد';

    public function __construct(private VariantStockDistributor $distributor)
    {
    }

    public function handle(Product $product, int $selectedVariant, int $quantity): CartResult
    {
        $selection = new VariantSelection($product, $selectedVariant);
        $cart = Auth::check() ? new DatabaseCartStore(Auth::user()) : new SessionCartStore();

        return $selection->isAuto()
            ? $this->addDistributed($product, $cart, $quantity)
            : $this->addSingle($selection, $cart, $quantity);
    }

    /** «به انتخاب نویسینو»: تعداد بین واریانت‌ها پخش می‌شود */
    private function addDistributed(Product $product, CartStore $cart, int $quantity): CartResult
    {
        $inCart = $cart->quantities($product);
        $available = $this->distributor->availableVariants($product->variants, $inCart);

        $quantityToAdd = min($quantity, $available->sum('available_stock'));
        if ($quantityToAdd <= 0) {
            return CartResult::warning('شما تمام موجودی این محصول را در سبد خرید خود قرار داده اید.');
        }

        $updates = $this->distributor->distribute($available, $quantityToAdd);
        if (empty($updates)) {
            return CartResult::warning('متاسفانه موجودی کافی برای انجام درخواست شما وجود ندارد.');
        }

        foreach ($updates as $variantId => $added) {
            $cart->save($product, $variantId, ($inCart[$variantId] ?? 0) + $added);
        }

        return CartResult::success(self::SUCCESS_MESSAGE);
    }

    /** یک واریانت مشخص، یا محصول بدون واریانت */
    private function addSingle(VariantSelection $selection, CartStore $cart, int $quantity): CartResult
    {
        $product = $selection->product;
        $variant = $selection->variant();

        if ($selection->hasVariants() && !$variant) {
            return CartResult::error('واریانت انتخاب‌شده معتبر نیست.');
        }

        $variantId = $variant?->id;
        $maxStock = $selection->stock();
        $label = $variant ? '<strong>' . $product->variant . ' ' . $variant->name . '</strong>' : 'این محصول';

        if ($maxStock <= 0) {
            return CartResult::warning('موجودی ' . $label . ' تمام شده است');
        }

        $inCart = $cart->quantity($product, $variantId);
        $newTotal = $inCart + $quantity;

        $cart->save($product, $variantId, min($newTotal, $maxStock));

        return $this->resultAfterAdding($inCart, $newTotal, $maxStock, $label);
    }

    private function resultAfterAdding(int $inCart, int $newTotal, int $maxStock, string $label): CartResult
    {
        if ($inCart >= $maxStock) {
            return CartResult::warning('شما تمام موجودی ' . $label . ' را در سبد خرید خود قرار داده اید.');
        }

        if ($newTotal > $maxStock) {
            $added = english_to_persian_num($maxStock - $inCart);

            return CartResult::success('تنها <strong>' . $added . '</strong> عدد از ' . $label . ' به دلیل محدودیت موجودی، به سبد خرید اضافه شد.');
        }

        return CartResult::success(self::SUCCESS_MESSAGE);
    }
}