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
        'canceled'  => false, // ← پیش‌فرض true تا در ابتدا همه سفارش‌ها دیده بشن
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
        $query = Order::query()
            ->with(['user', 'transactions'])
            ->latest();

        $activeStatuses = array_keys(array_filter($this->statusFilter));

        // حالت ۱: هیچ فیلتری فعال نیست → نتیجه خالی
        if (empty($activeStatuses)) {
            $orders = collect();
        }
        // حالت ۲: همه فیلترها فعال هستن → همه سفارش‌ها بدون هیچ شرطی
        elseif (count($activeStatuses) === count($this->statusFilter)) {
            $orders = $query->get();
        }
        // حالت ۳: فیلتر جزئی
        else {
            $query->where(function ($q) use ($activeStatuses) {
                // فیلترهای ستون status
                $statusFilters = array_intersect($activeStatuses, ['pending', 'paid', 'canceled']);
                if (!empty($statusFilters)) {
                    $q->orWhereIn('status', $statusFilters);
                }

                // فیلترهای ستون shipping_status
                $shippingFilters = array_intersect($activeStatuses, ['shipped', 'delivered', 'returned']);
                if (!empty($shippingFilters)) {
                    $q->orWhereIn('shipping_status', $shippingFilters);
                }
            });

            $orders = $query->get();
        }

        return view('livewire.admin.order.index', compact('orders'))
            ->layout('components.layouts.admin');
    }
}