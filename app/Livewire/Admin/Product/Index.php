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

    // آرایه برای ذخیره تاریخ‌های بروزرسانی
    public $priceUpdates = [];
    public $price_for_show = [], $installment_price_for_show = [];

    public function mount()
    {
        $this->profitPercent = Session::get('profit_percent', 20);
        $this->loadProducts();
        $this->calculatePriceFromBulk();
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
        $this->initPriceUpdates();
    }

    public function initPrices()
    {
        foreach ($this->products as $product) {
            $this->prices[$product->id] = [
                'price' => (string)$product->price,
                'discounted_price' => $product->discounted_price ? (string)$product->discounted_price : '',
                'bulk_price' => $product->bulk_price ? (string)$product->bulk_price : '',
                'installment_price' => $product->installment_price ? (string)$product->installment_price : '',
                'discounted_installment_price' => $product->discounted_installment_price ? (string)$product->discounted_installment_price : '',
            ];
            $this->purchasePrices[$product->id] = '';
        }
    }

    // متد برای مقداردهی اولیه قیمت‌های قبلی
    public function initPreviousPrices()
    {
        foreach ($this->products as $product) {
            $this->previousPrices[$product->id] = [
                'price_previous' => $product->price_previous,
                'bulk_price_previous' => $product->bulk_price_previous,
                'installment_price_previous' => $product->installment_price_previous,
                'discounted_price_previous' => $product->discounted_price_previous,
                'discounted_installment_price_previous' => $product->discounted_installment_price_previous,
            ];
        }
    }

    // متد برای مقداردهی اولیه تاریخ‌های بروزرسانی
    public function initPriceUpdates()
    {
        foreach ($this->products as $product) {
            $this->priceUpdates[$product->id] = [
                'price_updated_at' => $product->price_updated_at,
                'bulk_price_updated_at' => $product->bulk_price_updated_at,
                'installment_price_updated_at' => $product->installment_price_updated_at,
                'discounted_price_updated_at' => $product->discounted_price_updated_at,
                'discounted_installment_price_updated_at' => $product->discounted_installment_price_updated_at,
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

//        foreach ($this->products as $product) {
//            $this->calculatePriceFromPurchase($product->id);
//        }
    }


    public function calculatePriceFromBulk()
    {
        foreach ($this->products as $product) {
            $bulk = $this->prices[$product->id]['bulk_price'];
            $this->price_for_show[$product->id] = (int)$bulk * (1 + ($this->profitPercent / 100));
            $this->installment_price_for_show[$product->id] = (int)$this->price_for_show[$product->id] * (1 + ($this->profitPercent / 100));
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
            $bulkPrice = $this->prices[$productId]['bulk_price'] ?? null;
            $installmentPrice = $this->prices[$productId]['installment_price'] ?? null;
            $discountedInstallmentPrice = $this->prices[$productId]['discounted_installment_price'] ?? null;

            // پاکسازی اعداد
            $price = str_replace([',', '،', ' ', '_'], '', $price);
            $discountedPrice = $discountedPrice ? str_replace([',', '،', ' ', '_'], '', $discountedPrice) : null;
            $bulkPrice = $bulkPrice ? str_replace([',', '،', ' ', '_'], '', $bulkPrice) : null;
            $installmentPrice = $installmentPrice ? str_replace([',', '،', ' ', '_'], '', $installmentPrice) : null;
            $discountedInstallmentPrice = $discountedInstallmentPrice ? str_replace([',', '،', ' ', '_'], '', $discountedInstallmentPrice) : null;

            // اعتبارسنجی قیمت اصلی
            if (!is_numeric($price) || $price < 0) {
                session()->flash('error', 'قیمت باید عدد مثبت باشد.');
                return;
            }

            // اعتبارسنجی قیمت تخفیفی
            if ($discountedPrice !== null && $discountedPrice !== '' && $discountedPrice >= $price) {
                session()->flash('error', 'قیمت تخفیفی باید کمتر از قیمت اصلی باشد.');
                return;
            }

            // اعتبارسنجی قیمت اقساطی تخفیفی
            if ($discountedInstallmentPrice !== null && $discountedInstallmentPrice !== '' && $installmentPrice !== null && $discountedInstallmentPrice >= $installmentPrice) {
                session()->flash('error', 'قیمت اقساطی تخفیفی باید کمتر از قیمت اقساطی باشد.');
                return;
            }

            // ذخیره قیمت‌های قبلی و به‌روزرسانی تاریخ‌ها
            $this->updatePriceHistory($product, (int)$price, $bulkPrice, $installmentPrice, $discountedPrice, $discountedInstallmentPrice);

            // به‌روزرسانی محصول
            $product->price = (int)$price;
            $product->discounted_price = ($discountedPrice !== null && $discountedPrice !== '') ? (int)$discountedPrice : null;
            $product->bulk_price = ($bulkPrice !== null && $bulkPrice !== '') ? (int)$bulkPrice : null;
            $product->installment_price = ($installmentPrice !== null && $installmentPrice !== '') ? (int)$installmentPrice : null;
            $product->discounted_installment_price = ($discountedInstallmentPrice !== null && $discountedInstallmentPrice !== '') ? (int)$discountedInstallmentPrice : null;

            $product->save();

            // به‌روزرسانی آرایه‌ها
            $this->refreshProductData($productId, $product);

            session()->flash('message', "✅ قیمت‌های محصول '{$product->title}' با موفقیت به‌روزرسانی شد.");

        } catch (\Exception $e) {
            Log::error('Error updating product price: ' . $e->getMessage());
            session()->flash('error', '❌ خطا در به‌روزرسانی قیمت.');
        }
    }

    // متد کمکی برای به‌روزرسانی تاریخچه قیمت‌ها
    private function updatePriceHistory($product, $newPrice, $newBulkPrice, $newInstallmentPrice, $newDiscountedPrice, $newDiscountedInstallmentPrice)
    {
        // قیمت اصلی
        if ($product->price != $newPrice) {
            $product->price_previous = $product->price;
            $product->price_updated_at = now();
        }

        // قیمت عمده
        if ($product->bulk_price != $newBulkPrice) {
            $product->bulk_price_previous = $product->bulk_price;
            $product->bulk_price_updated_at = now();
        }

        // قیمت اقساطی
        if ($product->installment_price != $newInstallmentPrice) {
            $product->installment_price_previous = $product->installment_price;
            $product->installment_price_updated_at = now();
        }

        // قیمت تخفیفی
        if ($product->discounted_price != $newDiscountedPrice) {
            $product->discounted_price_previous = $product->discounted_price;
            $product->discounted_price_updated_at = now();
        }

        // قیمت اقساطی تخفیفی
        if ($product->discounted_installment_price != $newDiscountedInstallmentPrice) {
            $product->discounted_installment_price_previous = $product->discounted_installment_price;
            $product->discounted_installment_price_updated_at = now();
        }
    }

    // متد کمکی برای به‌روزرسانی آرایه‌ها
    private function refreshProductData($productId, $product)
    {
        // به‌روزرسانی قیمت‌ها
        $this->prices[$productId] = [
            'price' => (string)$product->price,
            'discounted_price' => $product->discounted_price ? (string)$product->discounted_price : '',
            'bulk_price' => $product->bulk_price ? (string)$product->bulk_price : '',
            'installment_price' => $product->installment_price ? (string)$product->installment_price : '',
            'discounted_installment_price' => $product->discounted_installment_price ? (string)$product->discounted_installment_price : '',
        ];

        // به‌روزرسانی قیمت‌های قبلی
        $this->previousPrices[$productId] = [
            'price_previous' => $product->price_previous,
            'bulk_price_previous' => $product->bulk_price_previous,
            'installment_price_previous' => $product->installment_price_previous,
            'discounted_price_previous' => $product->discounted_price_previous,
            'discounted_installment_price_previous' => $product->discounted_installment_price_previous,
        ];

        // به‌روزرسانی تاریخ‌های بروزرسانی
        $this->priceUpdates[$productId] = [
            'price_updated_at' => $product->price_updated_at,
            'bulk_price_updated_at' => $product->bulk_price_updated_at,
            'installment_price_updated_at' => $product->installment_price_updated_at,
            'discounted_price_updated_at' => $product->discounted_price_updated_at,
            'discounted_installment_price_updated_at' => $product->discounted_installment_price_updated_at,
        ];

        $this->purchasePrices[$productId] = '';
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