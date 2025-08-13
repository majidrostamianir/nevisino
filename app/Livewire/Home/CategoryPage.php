<?php

namespace App\Livewire\Home;

use App\Models\Url;
use Livewire\Component;

class CategoryPage extends Component
{
    public string $dashed;
    public Url $url;

    public function mount(): void
    {
        $this->url =  Url::query()->where('dashed_title',  $this->dashed)->firstOrFail();
    }
    public function render()
    {
        $products = $this->url->products()->latest('id')->get();

        return view('livewire.home.category-page',compact('products'))->title($this->url->title);
    }
}
