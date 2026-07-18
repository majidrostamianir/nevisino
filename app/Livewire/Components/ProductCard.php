<?php

namespace App\Livewire\Components;

use App\Models\Product;
use App\Helpers\PriceHelper;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;

    // متغیرهای قیمت
    public $finalPrice;
    public $basePrice;
    public $hasDiscount;
    public $discountPercent;

    public function mount()
    {
        $this->finalPrice = PriceHelper::getProductPrice($this->product);
        $this->basePrice = PriceHelper::getBasePrice($this->product);
        $this->hasDiscount = PriceHelper::hasDiscount($this->product);

        // محاسبه درصد تخفیف
        if ($this->hasDiscount && $this->basePrice > 0) {
            $this->discountPercent = round((($this->basePrice - $this->finalPrice) / $this->basePrice) * 100);
        } else {
            $this->discountPercent = 0;
        }
    }

    public function render()
    {
        return view('livewire.components.product-card');
    }
}