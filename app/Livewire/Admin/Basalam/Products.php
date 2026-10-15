<?php

namespace App\Livewire\Admin\Basalam;

use App\Jobs\SyncProductToBasalam;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public array  $selected = [];
    public bool   $skipPhotos = false;
    public int    $perPage = 25;

    public function updatedSearch()       { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }
    public function updatedPerPage()      { $this->resetPage(); }

    public function syncSelected(): void
    {
        if (empty($this->selected)) {
            session()->flash('warning', 'هیچ محصولی انتخاب نشده.');
            return;
        }

        $products = Product::whereIn('id', $this->selected)->get();
        foreach ($products as $product) {
            SyncProductToBasalam::dispatch($product, false, $this->skipPhotos);
        }

        session()->flash('success', count($products) . ' محصول در صف سینک قرار گرفت.');
        $this->selected = [];
    }

    public function syncFailed(): void
    {
        $products = Product::whereHas('basalamProducts', fn ($q) => $q->where('status', 'failed'))->get();
        foreach ($products as $product) {
            SyncProductToBasalam::dispatch($product, false, $this->skipPhotos);
        }

        session()->flash('success', count($products) . ' محصول ناموفق در صف سینک قرار گرفت.');
    }

    public function clearFailed(): void
    {
        \App\Models\BasalamProduct::where('status', 'failed')->update([
            'status'     => 'pending',
            'last_error' => null,
        ]);
        session()->flash('success', 'وضعیت محصولات ناموفق ریست شد.');
    }

    public function toggleSelectAll($ids): void
    {
        $this->selected = array_map('intval', $ids);
    }

    public function render()
    {
        $query = Product::with(['category', 'basalamProducts'])->latest('id');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        match ($this->statusFilter) {
            'none'    => $query->whereDoesntHave('basalamProducts'),
            'failed'  => $query->whereHas('basalamProducts', fn ($q) => $q->where('status', 'failed')),
            'pending' => $query->whereHas('basalamProducts', fn ($q) => $q->where('status', 'pending')),
            'synced'  => $query->whereHas('basalamProducts', fn ($q) => $q->where('status', 'synced'))
                ->whereDoesntHave('basalamProducts', fn ($q) => $q->whereIn('status', ['failed', 'pending'])),
            default   => null,
        };

        $products = $query->get();

        // آمار کلی
        $stats = [
            'total'   => Product::count(),
            'synced'  => \App\Models\BasalamProduct::where('status', 'synced')->count(),
            'failed'  => \App\Models\BasalamProduct::where('status', 'failed')->count(),
            'pending' => \App\Models\BasalamProduct::where('status', 'pending')->count(),
        ];

        return view('livewire.admin.basalam.products', [
            'products' => $products,
            'stats'    => $stats,
        ])->layout('components.layouts.admin');
    }

    public function updateSkus(): void
    {
        if (empty($this->selected)) {
            session()->flash('warning', 'هیچ محصولی انتخاب نشده.');
            return;
        }

        $listings = \App\Models\BasalamProduct::whereIn('product_id', $this->selected)
            ->whereNotNull('basalam_product_id')
            ->get();

        foreach ($listings as $listing) {
            \App\Jobs\UpdateBasalamSku::dispatch($listing->id);
        }

        session()->flash('success', count($listings) . ' SKU در صف آپدیت قرار گرفت.');
        $this->selected = [];
    }
    public function toggleForceStock(): void
    {
        $current = \Cache::get('basalam_force_stock_one', false);
        \Cache::forever('basalam_force_stock_one', !$current);
        session()->flash('success', !$current
            ? '✅ حالت «موجودی = ۱» روشن شد.'
            : '❌ حالت «موجودی = ۱» خاموش شد.');
    }

    public function getForceStockProperty(): bool
    {
        return (bool) \Cache::get('basalam_force_stock_one', false);
    }
}