<?php

namespace App\Livewire\Admin\Static;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SearchQuery;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $query , $ip , $user;
    public $queries;

    public function mount()
    {
        $this->queries = SearchQuery::query()->orderBy('created_at', 'desc')
            ->take(300)
            ->get();
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
            ->where('created_at', '<', now()->subDays(1))
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
