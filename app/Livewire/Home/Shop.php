<?php

namespace App\Livewire\Home;

use App\Models\Product;
use Livewire\Component;

class Shop extends Component
{
    public function render()
    {
        $products = Product::query()
            ->select('products.*')
            ->selectSub("
                                CASE
                                    -- موجود: بدون واریانت و stock > 0
                                    WHEN (variant IS NULL OR variant = '')
                                         AND stock > 0
                                    THEN 0

                                    -- موجود: دارای واریانت و حداقل یک واریانت موجود
                                    WHEN (variant IS NOT NULL AND (
                                        SELECT COUNT(*)
                                        FROM product_variants
                                        WHERE product_variants.product_id = products.id
                                          AND product_variants.stock > 0
                                    ) > 0)
                                    THEN 0

                                    -- بقیه ناموجود هستند
                                    ELSE 1
                                END
                            ", 'is_unavailable')
            ->orderBy('is_unavailable')   // موجودها اول
            ->orderBy('title', 'asc')     // مرتب‌سازی داخل هر گروه
            ->get();


        return view('livewire.home.shop', compact('products'))->layout('components.layouts.shop');
    }
}
