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
    public $purchasePrices = []; // آرایه برای قیمت خرید
    public $profitPercent = 20; // درصد سود پیش‌فرض
    public $selectedCategory = 'all';
    public $selectedUrl = 'all';

    public function mount()
    {
        // بازیابی درصد سود از سشن
        $this->profitPercent = Session::get('profit_percent', 20);
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
            // مقداردهی اولیه قیمت خرید (خالی)
            $this->purchasePrices[$product->id] = '';
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

    // متد برای ذخیره درصد سود در سشن
    public function updatedProfitPercent($value)
    {
        // ذخیره در سشن
        Session::put('profit_percent', (int) $value);

        // محاسبه مجدد قیمت‌ها برای همه محصولات
        foreach ($this->products as $product) {
            $this->calculatePriceFromPurchase($product->id);
        }
    }

    // متد برای محاسبه قیمت از روی قیمت خرید
    public function calculatePriceFromPurchase($productId)
    {
        $purchasePrice = $this->purchasePrices[$productId] ?? '';

        // حذف کاما و فاصله
        $purchasePrice = str_replace([',', '،', ' ', '_'], '', $purchasePrice);

        if (is_numeric($purchasePrice) && $purchasePrice > 0) {
            // محاسبه قیمت اصلی با سود
            $calculatedPrice = $purchasePrice * (1 + ($this->profitPercent / 100));
            // به‌روزرسانی قیمت اصلی در آرایه prices
            $this->prices[$productId]['price'] = (string) round($calculatedPrice);
        } else {
            // اگر قیمت خرید خالی یا نامعتبر بود، قیمت اصلی را خالی می‌کنیم
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

            // پاک کردن قیمت خرید بعد از ذخیره
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