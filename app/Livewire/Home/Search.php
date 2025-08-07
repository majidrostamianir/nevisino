<?php

namespace App\Livewire\Home;

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
        if ($this->query != '') {
            $this->urls = Url::query()->where('title', 'like', '%' . $this->query . '%')->limit(2)->get();
            $this->products = Product::query()->where('title', 'like', '%' . $this->query . '%')->limit(3)->get();
        }else{
            $this->urls = collect();
            $this->products = collect();
        }
    }

    public function clearSearch()
    {
        $this->isFocused = false;
        $this->query = '';
    }
    public function render()
    {
        return view('livewire.home.search');
    }
}
