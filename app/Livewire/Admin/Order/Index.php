<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $orders = Order::query()->latest()->get();
        return view('livewire.admin.order.index', compact('orders'))->layout('components.layouts.admin');
    }
}
