<?php

namespace App\Services\Tapin;

use App\Exceptions\TapinException;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class SendOrderToTapinAction
{
    public function __construct(
        protected TapinClient $client,
        protected OrderToTapinMapper $mapper,
    ) {}

    /**
     * @return array{success: bool, message: string}
     */
    public function execute(Order $order): array
    {
        if ($order->status !== 'paid') {
            return ['success' => false, 'message' => 'فقط سفارش‌های پرداخت‌شده قابل ارسال به تاپین هستند.'];
        }

        if (!blank($order->tracking_code)) {
            return [
                'success' => false,
                'message' => 'این سفارش قبلاً کد رهگیری دارد: ' . $order->tracking_code,
            ];
        }

        try {
            $payload = $this->mapper->build($order);
            $this->client->register($payload);
        } catch (TapinException $e) {
            return ['success' => false, 'message' => 'خطای تاپین: ' . $e->getMessage()];
        }

        // بارکد از پاسخ خوانده نمی‌شود — کد رهگیری بعداً دستی وارد می‌شود
        Cache::put("tapin_sent:{$order->id}", now()->toDateTimeString(), now()->addDays(30));

        return [
            'success' => true,
            'message' => '✅ سفارش با موفقیت به تاپین ارسال شد (وضعیت: تحت بررسی). حالا در پنل تاپین وضعیت را به «آماده به ارسال» تغییر دهید و کد رهگیری را در فیلد tracking_code وارد کنید.',
        ];
    }
}