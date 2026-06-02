<?php

namespace App\Livewire\Admin\Static;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SearchQuery;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $query , $ip , $user ,$total_stock, $total_value;
    public $queries;

    public function mount()
    {
        $this->queries = SearchQuery::query()->orderBy('created_at', 'desc')
            ->take(300)
            ->get();

        $this->calculate();
    }

    public function calculate()
    {
        $this->total_stock = \App\Models\Product::query()
                ->whereNull('variant')
                ->sum('stock')
            +
            \App\Models\ProductVariant::query()
                ->whereHas('product', fn($q) => $q->whereNotNull('variant'))
                ->sum('stock');

        $this->total_value = \App\Models\Product::query()
            ->whereNull('variant')
            ->selectRaw('SUM(stock * COALESCE(discounted_price, price)) as total')
            ->value('total') ?? 0;

        $this->total_value += \App\Models\ProductVariant::query()
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->whereNotNull('products.variant')
            ->selectRaw('SUM(product_variants.stock * COALESCE(products.discounted_price, products.price)) as total')
            ->value('total') ?? 0;
    }
    public function updatedQuery()
    {
        $this->queries = SearchQuery::querySearch($this->query);
    }
    public function updatedIp()
    {
        $this->queries = SearchQuery::ipSearch($this->ip);
    }
    public function updatedUser()
    {

        $this->queries = SearchQuery::userSearch($this->user);
    }
    public function cancelOrders()
    {
        Order::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(30))
            ->update([
                'status' => 'canceled'
            ]);
    }
    public function render()
    {
        $soldProducts = OrderItem::query()
            ->select(
                'product_id',
                'variant_id',
                DB::raw('SUM(quantity) as total_sold')
            )
            ->whereHas('order', fn($q) => $q->where('status', 'paid'))
            ->with([
                'product:id,title',
                'variant:id,name'
            ])
            ->groupBy('product_id', 'variant_id')
            ->orderByDesc('total_sold')
            ->get();

        return view('livewire.admin.static.index' , compact('soldProducts'))->layout('components.layouts.admin');
    }
}
