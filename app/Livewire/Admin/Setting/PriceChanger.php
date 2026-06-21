<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class PriceChanger extends Component
{
    public $priceRanges = [
        ['min' => 0, 'max' => 25000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 25000, 'max' => 50000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 50000, 'max' => 100000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 100000, 'max' => 200000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 200000, 'max' => 300000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 300000, 'max' => 500000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 500000, 'max' => 800000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 800000, 'max' => 1000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 1000000, 'max' => 1500000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 1500000, 'max' => 2000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 2000000, 'max' => 3000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 3000000, 'max' => 4000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 4000000, 'max' => 5000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 5000000, 'max' => 6000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 6000000, 'max' => 8000000, 'percent' => 0, 'type' => 'increase'],
        ['min' => 8000000, 'max' => 80000000, 'percent' => 0, 'type' => 'increase'],
    ];

    public $selectedRangeIndex = 0;
    public $selectedPercent = 0;
    public $selectedType = 'increase';
    public $affectedProducts = [];
    public $showProducts = false;
    public $totalIncrease = 0;
    public $totalDecrease = 0;
    public $isApplying = false;

    public function mount()
    {
        $this->showProductsForCurrentRange();
    }

    public function updatedSelectedType()
    {
        $this->applyPercentAutomatically();
    }

    public function updatedSelectedPercent()
    {
        $this->applyPercentAutomatically();
    }

    public function applyPercentAutomatically()
    {
        if ($this->selectedRangeIndex === null) {
            return;
        }

        if (!is_numeric($this->selectedPercent) || $this->selectedPercent <= 0) {
            $this->showProductsForCurrentRange();
            return;
        }

        $finalPercent = $this->selectedType == 'increase' ? $this->selectedPercent : -$this->selectedPercent;

        $this->priceRanges[$this->selectedRangeIndex]['percent'] = $finalPercent;
        $this->priceRanges[$this->selectedRangeIndex]['type'] = $this->selectedType;

        $this->showProductsWithNewPrices();
    }

    public function showProductsForCurrentRange()
    {
        if ($this->selectedRangeIndex === null) {
            $this->affectedProducts = [];
            $this->showProducts = false;
            return;
        }

        $range = $this->priceRanges[$this->selectedRangeIndex];

        $products = Product::query()->whereBetween('price', [$range['min'] + 1, $range['max']])->get();

        $this->affectedProducts = [];
        $this->totalIncrease = 0;
        $this->totalDecrease = 0;

        foreach ($products as $product) {
            $this->affectedProducts[] = [
                'id' => $product->id,
                'name' => $product->title,
                'old_price' => $product->price,
                'new_price' => null,
                'price_change' => null,
                'price_change_percent' => 0,
                'old_discounted_price' => $product->discounted_price,
                'new_discounted_price' => null,
            ];
        }

        $this->showProducts = count($this->affectedProducts) > 0;
    }

    public function showProductsWithNewPrices()
    {
        if ($this->selectedRangeIndex === null) {
            $this->affectedProducts = [];
            $this->showProducts = false;
            return;
        }

        $range = $this->priceRanges[$this->selectedRangeIndex];

        if ($range['percent'] == 0) {
            $this->showProductsForCurrentRange();
            return;
        }

        $percent = $range['percent'] / 100;
        $products = Product::query()->whereBetween('price', [$range['min'] + 1, $range['max']])->get();

        $this->affectedProducts = [];
        $this->totalIncrease = 0;
        $this->totalDecrease = 0;

        foreach ($products as $product) {
            $newPrice = $product->price * (1 + $percent);
            $newPrice = max(1000, round($newPrice / 1000) * 1000);
            $priceChange = $newPrice - $product->price;

            $newDiscountedPrice = null;
            if ($product->discounted_price && $product->discounted_price > 0) {
                $newDiscountedPrice = $product->discounted_price * (1 + $percent);
                $newDiscountedPrice = max(1000, round($newDiscountedPrice / 1000) * 1000);
            }

            if ($priceChange > 0) {
                $this->totalIncrease++;
            } elseif ($priceChange < 0) {
                $this->totalDecrease++;
            }

            $this->affectedProducts[] = [
                'id' => $product->id,
                'name' => $product->title,
                'old_price' => $product->price,
                'new_price' => $newPrice,
                'price_change' => $priceChange,
                'price_change_percent' => $range['percent'],
                'old_discounted_price' => $product->discounted_price,
                'new_discounted_price' => $newDiscountedPrice,
            ];
        }

        $this->showProducts = count($this->affectedProducts) > 0;
    }

    public function selectRange($index)
    {
        if ($this->selectedRangeIndex == $index) {
            return;
        }

        $this->selectedRangeIndex = $index;
        $this->selectedPercent = abs($this->priceRanges[$index]['percent']);
        $this->selectedType = $this->priceRanges[$index]['type'];

        if ($this->priceRanges[$index]['percent'] != 0) {
            $this->showProductsWithNewPrices();
        } else {
            $this->showProductsForCurrentRange();
        }
    }

    public function applyChanges()
    {
        if ($this->selectedRangeIndex === null) {
            Session::flash('error', 'لطفاً ابتدا یک بازه را انتخاب کنید');
            return;
        }

        $range = $this->priceRanges[$this->selectedRangeIndex];

        if ($range['percent'] == 0) {
            Session::flash('error', 'لطفاً یک درصد وارد کنید');
            return;
        }

        $this->isApplying = true;

        try {
            $percent = $range['percent'] / 100;
            $factor = 1 + $percent;
            $minPrice = $range['min'] + 1;
            $maxPrice = $range['max'];

            \DB::table('products')
                ->whereBetween('price', [$minPrice, $maxPrice])
                ->update([
                    'price' => \DB::raw("GREATEST(1000, ROUND(price * $factor / 1000) * 1000)"),
                    'updated_at' => now()
                ]);

            \DB::table('products')
                ->whereBetween('price', [$minPrice, $maxPrice])
                ->whereNotNull('discounted_price')
                ->where('discounted_price', '>', 0)
                ->update([
                    'discounted_price' => \DB::raw("GREATEST(1000, ROUND(discounted_price * $factor / 1000) * 1000)")
                ]);

            $updatedCount = \DB::table('products')
                ->whereBetween('price', [$minPrice * $factor, $maxPrice * $factor])
                ->count();

            $this->priceRanges[$this->selectedRangeIndex]['percent'] = 0;
            $this->selectedPercent = 0;
            $this->showProductsForCurrentRange();

            Session::flash('message', "✅ {$updatedCount} محصول با موفقیت بروزرسانی شد");

        } catch (\Exception $e) {
            Session::flash('error', 'خطا: ' . $e->getMessage());
        }

        $this->isApplying = false;
    }

    public function render()
    {
        return view('livewire.admin.setting.price-changer', [
            'priceRanges' => $this->priceRanges,
            'selectedRangeIndex' => $this->selectedRangeIndex,
            'selectedPercent' => $this->selectedPercent,
            'selectedType' => $this->selectedType,
            'affectedProducts' => $this->affectedProducts,
            'showProducts' => $this->showProducts,
            'totalIncrease' => $this->totalIncrease,
            'totalDecrease' => $this->totalDecrease,
            'isApplying' => $this->isApplying
        ]);
    }
}