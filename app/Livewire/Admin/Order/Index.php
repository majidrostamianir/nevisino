<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Livewire\Component;

class Index extends Component
{
    /** @var array<string, bool> */
    public array $statusFilter = [
        'pending'   => true,
        'paid'      => true,
        'shipped'   => true,
        'delivered' => true,
        'returned'  => true,
        'canceled'  => false,
    ];

    public function toggleAllStatuses(): void
    {
        $allChecked = !in_array(false, $this->statusFilter, true);

        foreach ($this->statusFilter as $key => $value) {
            $this->statusFilter[$key] = !$allChecked;
        }
    }

    public function render()
    {
        $query = Order::query()->latest();

        $activeStatuses = array_keys(array_filter($this->statusFilter));

        if (empty($activeStatuses)) {
            $orders = collect();
        } else {
            $query->where(function ($q) use ($activeStatuses) {
                if (in_array('pending', $activeStatuses)) {
                    $q->orWhere('status', 'pending');
                }
                if (in_array('paid', $activeStatuses)) {
                    $q->orWhere('status', 'paid');
                }
                if (in_array('canceled', $activeStatuses)) {
                    $q->orWhere('status', 'canceled');
                }

                $shippingStatuses = array_intersect($activeStatuses, ['shipped', 'delivered', 'returned']);
                if (!empty($shippingStatuses)) {
                    $q->orWhereIn('shipping_status', $shippingStatuses);
                }
            });

            $orders = $query->get();
        }

        return view('livewire.admin.order.index', compact('orders'))
            ->layout('components.layouts.admin');
    }
}