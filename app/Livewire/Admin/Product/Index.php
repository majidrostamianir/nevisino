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
        $this->products = Product::query()->orderBy('title')->get();
    }
    public function updatedQuery()
    {
        $queries = SearchNormalizer::expand($this->query);

        $this->products = collect();

        foreach ($queries as $q) {
            $this->products = $this->products->merge(
                Product::search($q)->get()
            );

        }

        $this->products = $this->products->unique('id')->values();
    }
    public function render()
    {
        return view('livewire.admin.product.index')->layout('components.layouts.admin');
    }
}
