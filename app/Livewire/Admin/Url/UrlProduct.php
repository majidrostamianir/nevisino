<?php

namespace App\Livewire\Admin\Url;

use App\Models\Product;
use App\Models\Url;
use Livewire\Component;

class UrlProduct extends Component
{

    public string $query;
    public array $products = [];
    public array $selectedProducts = [];
    public ?Url $url = null;
    public $successMessage = '';

    protected $listeners = [
        'url-updated' => 'getProductsUrl',
    ];

    public function getProductsUrl($urlId)
    {
        $this->url = Url::query()->find($urlId);;
        $this->selectedProducts = $this->url->products->pluck('title', 'id')->toArray();
        $this->products = Product::whereNotIn('id', array_keys($this->selectedProducts))
            ->pluck('title', 'id')
            ->toArray();
    }

    public function selectProduct($key)
    {
        if (!in_array($key, $this->selectedProducts)) {
            $this->selectedProducts[$key] = Product::query()->find($key)->title;
        }
        $this->products = array_diff(Product::all()->pluck('title', 'id')->toArray(), $this->selectedProducts);
    }


    public function removeProduct($urlId)
    {
        unset($this->selectedProducts[$urlId]);
        $this->products = array_diff(Product::all()->pluck('title', 'id')->toArray(), $this->selectedProducts);
    }


    public function save()
    {
        $this->url->products()->sync(array_keys($this->selectedProducts));
        $this->successMessage = 'تغییرات با موفقیت ذخیره شد';
        // پاک کردن پیام بعد از 5 ثانیه
        $this->dispatch('hide-message');
    }

    public function updatedQuery()
    {
        $query = '%' . $this->query . '%';
        $this->products = Product::where('title', 'like', $query)
            ->pluck('title', 'id')
            ->toArray();
    }
    public function render()
    {
        return view('livewire.admin.url.url-product');
    }
}
