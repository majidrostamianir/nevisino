<?php

namespace App\Livewire\Admin\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
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

    public ?string $tapinMessage = null;
    public bool $tapinSuccess = false;

    public function toggleAllStatuses(): void
    {
        $allChecked = !in_array(false, $this->statusFilter, true);

        foreach ($this->statusFilter as $key => $value) {
            $this->statusFilter[$key] = !$allChecked;
        }
    }

    public function sendToTapin(int $orderId): void
    {
        $order = Order::with(['items.product', 'items.variant.product'])->find($orderId);

        if (!$order) {
            $this->tapinMessage = 'سفارش پیدا نشد.';
            $this->tapinSuccess = false;
            return;
        }

        $result = app(\App\Services\Tapin\SendOrderToTapinAction::class)->execute($order);

        $this->tapinMessage = $result['message'];
        $this->tapinSuccess = (bool) $result['success'];
    }

    public function dismissTapinMessage(): void
    {
        $this->tapinMessage = null;
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
        // حالت ۲: همه فیلترها فعال هستن → همه سفارش‌ها
        elseif (count($activeStatuses) === count($this->statusFilter)) {
            $orders = $query->get();
        }
        // حالت ۳: فیلتر جزئی
        else {
            $query->where(function ($q) use ($activeStatuses) {
                $statusFilters = array_intersect($activeStatuses, ['pending', 'paid', 'canceled']);
                if (!empty($statusFilters)) {
                    $q->orWhereIn('status', $statusFilters);
                }

                $shippingFilters = array_intersect($activeStatuses, ['shipped', 'delivered', 'returned']);
                if (!empty($shippingFilters)) {
                    $q->orWhereIn('shipping_status', $shippingFilters);
                }
            });

            $orders = $query->get();
        }

        // شناسهٔ سفارش‌هایی که قبلاً به تاپین ارسال شده‌اند (از Cache)
        $sentTapinIds = $orders
            ->filter(fn ($o) => Cache::has("tapin_sent:{$o->id}"))
            ->pluck('id')
            ->toArray();

        return view('livewire.admin.order.index', compact('orders', 'sentTapinIds'))
            ->layout('components.layouts.admin');
    }
}