<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Index extends Component
{
    public $products;
    public $prices = [];
    public $selectedCategory = 'all';
    public $selectedUrl = 'all';

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $query = Product::query();

        // فیلتر بر اساس دسته‌بندی
        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        // فیلتر بر اساس URL
        if ($this->selectedUrl !== 'all') {
            $query->whereHas('urls', function($q) {
                $q->where('urls.id', $this->selectedUrl);
            });
        }

        // مرتب‌سازی بر اساس دسته‌بندی
        $query->orderBy('id','desc');

        // دریافت همه محصولات (بدون صفحه‌بندی)
        $this->products = $query->get();
        $this->initPrices();
    }

    public function initPrices()
    {
        foreach ($this->products as $product) {
            $this->prices[$product->id] = [
                'price' => (string) $product->price,
                'discounted_price' => $product->discounted_price ? (string) $product->discounted_price : '',
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

    public function updatePrice($productId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                session()->flash('error', 'محصول یافت نشد.');
                return;
            }

            // دریافت قیمت‌ها از آرایه
            $price = $this->prices[$productId]['price'] ?? null;
            $discountedPrice = $this->prices[$productId]['discounted_price'] ?? null;

            // حذف کاما و فاصله
            $price = str_replace([',', '،', ' ', '_'], '', $price);
            $discountedPrice = $discountedPrice ? str_replace([',', '،', ' ', '_'], '', $discountedPrice) : null;

            // اعتبارسنجی
            if (!is_numeric($price) || $price < 0) {
                session()->flash('error', 'قیمت باید عدد مثبت باشد.');
                return;
            }

            if ($discountedPrice !== null && $discountedPrice !== '' && $discountedPrice >= $price) {
                session()->flash('error', 'قیمت تخفیفی باید کمتر از قیمت اصلی باشد.');
                return;
            }

            // به‌روزرسانی - اگر قیمت تخفیفی خالی بود null بذار
            $product->price = (int) $price;
            $product->discounted_price = ($discountedPrice !== null && $discountedPrice !== '') ? (int) $discountedPrice : null;
            $product->save();

            // به‌روزرسانی آرایه قیمت‌ها
            $this->prices[$productId]['price'] = (string) $product->price;
            $this->prices[$productId]['discounted_price'] = $product->discounted_price ? (string) $product->discounted_price : '';

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