<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use Livewire\Component;

class Index extends Component
{
    public string $query;
    public  $products;

    public function mount()
    {
        $this->products = Product::query()->latest()->get();

    }
    public function updatedQuery()
    {
        $this->products = Product::search($this->query, 3);
    }
    public function render()
    {
        return view('livewire.admin.product.index')->layout('components.layouts.admin');
    }
}
