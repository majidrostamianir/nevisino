<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Index extends Component
{
    public $products;
    public $prices = [];
    public $purchasePrices = [];
    public $profitPercent = 20;
    public $selectedCategory = 'all';
    public $selectedUrl = 'all';

    // آرایه برای ذخیره قیمت‌های قبلی در حین ویرایش
    public $previousPrices = [];

    public function mount()
    {
        $this->profitPercent = Session::get('profit_percent', 20);
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::query();

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedUrl !== 'all') {
            $query->whereHas('urls', function ($q) {
                $q->where('urls.id', $this->selectedUrl);
            });
        }

        $query->orderBy('id', 'desc');

        $this->products = $query->get();
        $this->initPrices();
        $this->initPreviousPrices();
    }

    public function initPrices()
    {
        foreach ($this->products as $product) {
            $this->prices[$product->id] = [
                'price' => (string)$product->price,
                'discounted_price' => $product->discounted_price ? (string)$product->discounted_price : '',
            ];
            $this->purchasePrices[$product->id] = '';
        }
    }

    // متد جدید برای مقداردهی اولیه قیمت‌های قبلی
    public function initPreviousPrices()
    {
        foreach ($this->products as $product) {
            $this->previousPrices[$product->id] = [
                'previous_price' => $product->previous_price,
                'price_updated_at' => $product->price_updated_at,
            ];
        }
    }

    public function updatedSelectedCategory()
    {
        $this->loadProducts();
    }

    public function updatedSelectedUrl()
    {
        $this->loadProducts();
    }

    public function updatedProfitPercent($value)
    {
        Session::put('profit_percent', (int)$value);

        foreach ($this->products as $product) {
            $this->calculatePriceFromPurchase($product->id);
        }
    }

    public function calculatePriceFromPurchase($productId)
    {
        $purchasePrice = $this->purchasePrices[$productId] ?? '';

        $purchasePrice = str_replace([',', '،', ' ', '_'], '', $purchasePrice);

        if (is_numeric($purchasePrice) && $purchasePrice > 0) {
            $calculatedPrice = $purchasePrice * (1 + ($this->profitPercent / 100));
            $this->prices[$productId]['price'] = (string)round($calculatedPrice);
        } else {
            $this->prices[$productId]['price'] = '';
        }
    }

    public function updatePrice($productId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                session()->flash('error', 'محصول یافت نشد.');
                return;
            }

            $price = $this->prices[$productId]['price'] ?? null;
            $discountedPrice = $this->prices[$productId]['discounted_price'] ?? null;

            $price = str_replace([',', '،', ' ', '_'], '', $price);
            $discountedPrice = $discountedPrice ? str_replace([',', '،', ' ', '_'], '', $discountedPrice) : null;

            if (!is_numeric($price) || $price < 0) {
                session()->flash('error', 'قیمت باید عدد مثبت باشد.');
                return;
            }

            if ($discountedPrice !== null && $discountedPrice !== '' && $discountedPrice >= $price) {
                session()->flash('error', 'قیمت تخفیفی باید کمتر از قیمت اصلی باشد.');
                return;
            }

            // ذخیره قیمت قبلی اگر قیمت تغییر کرده باشد
            $oldPrice = $product->price;
            $newPrice = (int)$price;

            // به‌روزرسانی
            $product->price = $newPrice;
            $product->discounted_price = ($discountedPrice !== null && $discountedPrice !== '') ? (int)$discountedPrice : null;

            // اگر قیمت تغییر کرده، قیمت قبلی را ذخیره کن
            if ($oldPrice != $newPrice) {
                $product->previous_price = $oldPrice;
                $product->price_updated_at = now();
            }

            $product->save();

            // به‌روزرسانی آرایه قیمت‌ها
            $this->prices[$productId]['price'] = (string)$product->price;
            $this->prices[$productId]['discounted_price'] = $product->discounted_price ? (string)$product->discounted_price : '';

            // به‌روزرسانی قیمت قبلی در آرایه
            $this->previousPrices[$productId] = [
                'previous_price' => $product->previous_price,
                'price_updated_at' => $product->price_updated_at,
            ];

            $this->purchasePrices[$productId] = '';

            session()->flash('message', "✅ قیمت محصول '{$product->title}' با موفقیت به‌روزرسانی شد.");

        } catch (\Exception $e) {
            Log::error('Error updating product price: ' . $e->getMessage());
            session()->flash('error', '❌ خطا در به‌روزرسانی قیمت.');
        }
    }

    public function getCategoriesProperty()
    {
        return Category::orderBy('title')->get();
    }

    public function getUrlsProperty()
    {
        return Url::orderBy('id')->get();
    }

    public function render()
    {
        return view('livewire.admin.product.index', [
            'categories' => $this->categories,
            'urls' => $this->urls,
        ])->layout('components.layouts.admin');
    }
}