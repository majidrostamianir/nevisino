<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\SearchQuery;
use App\Models\Url;
use App\Services\SearchNormalizer;
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
//        $this->products = Product::search($this->query, 3);
        $queries = SearchNormalizer::expand($this->query);

        $this->products = collect();

        foreach ($queries as $q) {
            $this->products = $this->products->merge(
                Product::search($q)->get()
            );

        }

        $this->products = $this->products->unique('id')->take(3)->values();
    }
    public function render()
    {
        return view('livewire.admin.product.index')->layout('components.layouts.admin');
    }
}
