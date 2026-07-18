<?php

namespace App\Livewire\Home;

use App\Models\Url;
use App\Helpers\PriceHelper; // <-- اضافه کن
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CategoryPage extends Component
{
    public string $dashed;
    public Url $url;

    public function mount(): void
    {
        $this->url =  Url::query()->where('dashed_url',  $this->dashed)->firstOrFail();
    }
    public function render()
    {
        $priceType = \App\Models\Setting::get('display_price_type', 'installment');

        // اگر حالت قسطی است، محصولاتی که discounted_installment_price دارند
        if ($priceType === 'installment') {
            $availableIds = \App\Models\Product::whereNotNull('discounted_installment_price')
                ->get()
                ->filter(fn($p) => $p->hasValidStock())
                ->pluck('id')
                ->toArray();
        } else {
            // حالت نقدی: محصولاتی که discounted_price دارند
            $availableIds = \App\Models\Product::whereNotNull('discounted_price')
                ->get()
                ->filter(fn($p) => $p->hasValidStock())
                ->pluck('id')
                ->toArray();
        }

        if (empty($availableIds)) {
            $rawProducts = collect();
        } else {
            $rawProducts = \App\Models\Product::whereIn('id', $availableIds)
                ->inRandomOrder()
                ->limit(15)
                ->get(['id', 'title', 'dashed_url', 'price', 'discounted_price', 'installment_price', 'discounted_installment_price']); // <-- اصلاح شده
        }

        $discounted_products = $rawProducts->map(function ($p) {
            $finalPrice = PriceHelper::getProductPrice($p); // <-- اصلاح شده
            $basePrice = PriceHelper::getBasePrice($p); // <-- اصلاح شده
            $hasDiscount = PriceHelper::hasDiscount($p); // <-- اصلاح شده

            return [
                'image' => asset('storage/products/' . $p->id . '/small/1.webp'),
                'name' => $p->title ?? 'بدون نام',
                'final_price' => $finalPrice, // <-- اصلاح شده
                'base_price' => $basePrice, // <-- اصلاح شده
                'has_discount' => $hasDiscount, // <-- اصلاح شده
                'link' => route('product-page', ['title' => $p->dashed_url ?? 'unknown' , 'npi'=>$p->id])
            ];
        })->values()->toArray();

        $products = $this->url->products()
            ->select([
                'products.*',
                DB::raw("
                    CASE
                        WHEN (variant IS NULL OR variant = '') AND stock > 0 THEN 0
                        WHEN (variant IS NOT NULL AND EXISTS (
                            SELECT 1
                            FROM product_variants
                            WHERE product_variants.product_id = products.id
                              AND product_variants.stock > 0
                        )) THEN 0
                        ELSE 1
                    END AS is_unavailable
                ")
            ])
            ->orderBy('is_unavailable', 'asc')
            ->orderBy('title', 'asc')
            ->get();

        return view('livewire.home.category-page', compact('products', 'discounted_products'))
            ->layout('components.layouts.category')
            ->title($this->url->title_tag);
    }
}