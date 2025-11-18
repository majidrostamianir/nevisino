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
        $this->url =  Url::query()->where('dashed_title',  $this->dashed)->firstOrFail();
    }
    public function render()
    {
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

        return view('livewire.home.category-page',compact('products'))->layout('components.layouts.category')->title($this->url->title);
    }
}
