<?php

namespace App\Livewire\Home;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public function render()
    {
        return view('livewire.home.product-card');
    }
}
