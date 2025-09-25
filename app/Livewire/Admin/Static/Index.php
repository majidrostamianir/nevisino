<?php

namespace App\Livewire\Admin\Static;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
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
                'variant:id,name' // واریانت ممکنه null باشه
            ])
            ->groupBy('product_id', 'variant_id')
            ->orderByDesc('total_sold')
            ->get();
        return view('livewire.admin.static.index' , compact('soldProducts'))->layout('components.layouts.admin');
    }
}
