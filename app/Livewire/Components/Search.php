<?php

namespace App\Livewire\Components;

use App\Models\Product;
use App\Models\SearchQuery;
use App\Models\Url;
use App\Services\SearchNormalizer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
        $queries = SearchNormalizer::expand($this->query);

        $this->products = collect();
        $this->urls = collect();

        foreach ($queries as $q) {
            $this->products = $this->products->merge(
                Product::search($q)->get()
            );

            $this->urls = $this->urls->merge(
                Url::search($q)->get()
            );
        }

        $this->products = $this->products->unique('id')->take(3)->values();
        $this->urls = $this->urls->unique('id')->take(3)->values();

        $this->validate(['query' => 'string']);

        if (!(Auth::id() === 1 || Auth::id() === 32)) {
            SearchQuery::query()->create([
                'query' => $this->query,
                'ip' => request()->ip(),
                'user_id' => auth()->id() ?? null,
            ]);
        }
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
