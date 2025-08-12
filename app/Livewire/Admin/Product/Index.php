<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $products = Product::query()->latest()->paginate(50);
        return view('livewire.admin.product.index',compact('products'))->layout('components.layouts.admin');
    }
}
