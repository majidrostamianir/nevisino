<?php

namespace App\Livewire\Home;

use App\Models\Url;
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
        $rawProducts = \App\Models\Product::query()
            ->whereNotNull('discounted_price')
            ->inRandomOrder()
            ->limit(8)
            ->get(['id', 'title', 'dashed_url', 'price', 'discounted_price']);

        $discounted_products = $rawProducts->map(function ($p) {
            return [
                'image' => asset('storage/products/' . $p->id . '/small/1.webp'),
                'name' => $p->title ?? 'بدون نام',
                'discounted_price' => (int)$p->discounted_price,
                'price' => (int)$p->price,
                'link' => route('product-page', ['title' => $p->dashed_url ?? 'unknown'])
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

        return view('livewire.home.category-page',compact('products' , 'discounted_products'))->layout('components.layouts.category')->title($this->url->title_tag);
    }
}
