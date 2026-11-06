<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Models\ProductVariant;

/**
 * انتخاب فعلی کاربر روی یک محصول:
 *  - بدون واریانت
 *  - واریانت‌دار + «به انتخاب نویسینو» (auto)
 *  - واریانت‌دار + یک واریانت مشخص
 */
final class VariantSelection
{
    private const SPECIFIC_VARIANT_MIN_ID = 1000;

    public function __construct(
        public readonly Product $product,
        private readonly int $selected,
    ) {
    }

    public function hasVariants(): bool
    {
        return $this->product->variant !== null;
    }

    public function isAuto(): bool
    {
        return $this->hasVariants() && $this->selected < self::SPECIFIC_VARIANT_MIN_ID;
    }

    /** واریانت مشخص‌شده؛ فقط از بین واریانت‌های همین محصول. در حالت auto یا بدون واریانت null است. */
    public function variant(): ?ProductVariant
    {
        if (!$this->hasVariants() || $this->isAuto()) {
            return null;
        }

        return $this->product->variants->firstWhere('id', $this->selected);
    }

    /** مجموع موجودی محصول (همه‌ی واریانت‌ها یا خود محصول) */
    public function totalStock(): int
    {
        return (int) ($this->hasVariants()
            ? $this->product->variants->sum('stock')
            : $this->product->stock);
    }

    /** موجودی قابل‌خرید با انتخاب فعلی */
    public function stock(): int
    {
        if ($this->hasVariants() && !$this->isAuto()) {
            return (int) ($this->variant()?->stock ?? 0);
        }

        return $this->totalStock();
    }
}