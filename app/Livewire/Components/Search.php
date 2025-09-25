<?php

namespace App\Livewire\Components;

use App\Models\Product;
use App\Models\Url;
use Illuminate\Support\Collection;
use Livewire\Component;

class Search extends Component
{
    public string $query;
    public Collection $urls;
    public Collection $products;
    public bool $isFocused = false;

    public function mount()
    {
        $this->urls = collect();
        $this->products = collect();
    }

    public function focus()
    {
        $this->isFocused = true;
    }

    public function blur()
    {
        $this->isFocused = false;
    }

    public function updatedQuery(): void
    {
        $this->products = Product::search($this->query, 3);
        $this->urls = Url::search($this->query, 3);

    }

    public function clearSearch()
    {
        $this->isFocused = false;
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.components.search');
    }
}
