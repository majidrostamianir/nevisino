<?php

namespace App\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public function render()
    {
        return view('livewire.components.product-card');
    }
}
