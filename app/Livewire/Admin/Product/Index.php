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
    public $profitPercent = 20;       // درصد سود
    public $gatewayFeePercent = 13;   // درصد کارمزد درگاه
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
        $this->gatewayFeePercent = Session::get('gateway_fee_percent', 13);

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

        // خالی کردن آرایه‌ها قبل از پر کردن مجدد
        $this->prices = [];
        $this->purchasePrices = [];
        $this->previousPrices = [];
        $this->priceUpdates = [];
        $this->price_for_show = [];
        $this->installment_price_for_show = [];

        $this->initPrices();
        $this->initPreviousPrices();
        $this->initPriceUpdates();
        $this->calculatePriceFromBulk();
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
        $this->profitPercent = Session::get('profit_percent', 20);
        $this->calculatePriceFromBulk(); // بدون آرگومان = همه‌ی محصولات
    }

    public function updatedGatewayFeePercent($value)
    {
        Session::put('gateway_fee_percent', (float)$value);
        $this->gatewayFeePercent = Session::get('gateway_fee_percent', 20);
        $this->calculatePriceFromBulk(); // همه‌ی محصولات
    }

    public function updatedPrices($value, $key)
    {
        if (str_ends_with($key, '.bulk_price')) {
            // فقط همون محصول
            $productId = (int) explode('.', $key)[0];
            $this->calculatePriceFromBulk($productId);
        }
    }
    // محاسبه برای یک محصول یا همه‌ی محصولات
    public function calculatePriceFromBulk($productId = null)
    {
        $productIds = $productId !== null
            ? [$productId]
            : $this->products->pluck('id')->all();

        foreach ($productIds as $id) {

            $bulk = $this->prices[$id]['bulk_price'] ?? 0;

            // حذف جداکننده‌ها
            $bulk = (int) str_replace(
                [',', '،', ' ', '_'],
                '',
                $bulk
            );

            if ($bulk <= 0) {
                $this->price_for_show[$id] = 0;
                $this->installment_price_for_show[$id] = 0;
                continue;
            }

            // قیمت نقدی
            $cashPrice = $bulk * (1 + ($this->profitPercent / 100));
            $this->price_for_show[$id] = (int) $cashPrice;

            // قیمت اقساطی
            if ($this->gatewayFeePercent >= 100 || $this->gatewayFeePercent <= 0) {
                $this->installment_price_for_show[$id] = 0;
            } else {
                $this->installment_price_for_show[$id] = (int) round(
                    $cashPrice / (1 - ($this->gatewayFeePercent / 100))
                );
            }
        }
    }

    public function savePrice($productId)
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
            $price = $this->cleanNumber($price);
            $bulkPrice = $this->cleanNumber($bulkPrice);
            $installmentPrice = $this->cleanNumber($installmentPrice);
            $discountedPrice = $this->cleanNumber($discountedPrice);
            $discountedInstallmentPrice = $this->cleanNumber($discountedInstallmentPrice);

            // اعتبارسنجی قیمت اصلی
            if (!is_numeric($price) || (int)$price <= 0) {
                session()->flash('error', 'قیمت اصلی باید عددی بزرگتر از صفر باشد.');
                return;
            }
            if ($bulkPrice !== null && $bulkPrice !== '') {
                if (!is_numeric($bulkPrice) || (int)$bulkPrice <= 0) {
                    session()->flash('error', 'قیمت عمده باید عددی بزرگتر از صفر باشد.');
                    return;
                }
            }
            if ($installmentPrice !== null && $installmentPrice !== '') {

                if (!is_numeric($installmentPrice) || (int)$installmentPrice <= 0) {
                    session()->flash('error', 'قیمت اقساطی باید عددی بزرگتر از صفر باشد.');
                    return;
                }
            }
            // اعتبارسنجی قیمت تخفیفی
            if ($discountedPrice !== null && $discountedPrice !== '') {

                if (!is_numeric($discountedPrice) || (int)$discountedPrice <= 0) {
                    session()->flash('error', 'قیمت تخفیفی باید عددی بزرگتر از صفر باشد.');
                    return;
                }

                if ((int)$discountedPrice >= (int)$price) {
                    session()->flash('error', 'قیمت تخفیفی باید کمتر از قیمت اصلی باشد.');
                    return;
                }

            }
            // اعتبارسنجی قیمت اقساطی تخفیفی
            if ($discountedInstallmentPrice !== null && $discountedInstallmentPrice !== '') {

                if (!is_numeric($discountedInstallmentPrice) || (int)$discountedInstallmentPrice <= 0) {
                    session()->flash('error', 'قیمت اقساطی تخفیفی باید عددی بزرگتر از صفر باشد.');
                    return;
                }

                if ($installmentPrice === null || $installmentPrice === '') {
                    session()->flash('error', 'ابتدا قیمت اقساطی را وارد کنید.');
                    return;
                }

                if ((int)$discountedInstallmentPrice >= (int)$installmentPrice) {
                    session()->flash('error', 'قیمت اقساطی تخفیفی باید کمتر از قیمت اقساطی باشد.');
                    return;
                }
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

    private function cleanNumber($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return str_replace(
            [',', '،', ' ', '_'],
            '',
            $value
        );
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