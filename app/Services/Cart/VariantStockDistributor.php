<?php

namespace App\Services\Cart;

use Illuminate\Support\Collection;

class VariantStockDistributor
{
    /** تا جای ممکن این تعداد موجودی نگه داشته می‌شود؛ مرحله‌ی ۰ یعنی مصرف همه‌ی موجودی */
    private const RESERVE_LEVELS = [3, 2, 1, 0];

    /**
     * موجودی خالص هر واریانت پس از کسر آنچه در سبد است
     *
     * @param  array<int,int>  $inCart  variant_id => تعداد داخل سبد
     */
    public function availableVariants(Collection $variants, array $inCart): Collection
    {
        return $variants
            ->map(fn($variant) => (object) [
                'id'              => $variant->id,
                'available_stock' => max(0, $variant->stock - ($inCart[$variant->id] ?? 0)),
            ])
            ->filter(fn($variant) => $variant->available_stock > 0);
    }

    /**
     * @return array<int,int> variant_id => تعداد افزوده‌شده
     */
    public function distribute(Collection $available, int $quantity): array
    {
        $updates = [];

        foreach (self::RESERVE_LEVELS as $reserve) {
            $candidates = $available
                ->filter(fn($variant) => $variant->available_stock > $reserve)
                ->sortByDesc('available_stock');

            foreach ($candidates as $variant) {
                if ($quantity <= 0) {
                    break 2;
                }

                $take = min($quantity, $variant->available_stock - $reserve);

                if ($take > 0) {
                    $updates[$variant->id] = ($updates[$variant->id] ?? 0) + $take;
                    $quantity -= $take;
                    $variant->available_stock -= $take;
                }
            }
        }

        return $updates;
    }
}