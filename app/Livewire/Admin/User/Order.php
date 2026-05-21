<?php

namespace App\Livewire\Admin\User;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;

class Order extends Component
{
    public User $user;
    public array $trackingCodes = [];

    public function nextStep($orderId)
    {
        $order = \App\Models\Order::query()->findOrFail($orderId);

        switch ($order->shipping_status) {
            case 'processing':
                $order->shipping_status = 'preparing';
                break;
            case 'preparing':
                $order->shipping_status = 'shipped';
                break;
            case 'shipped':
                $order->shipping_status = 'delivered';
                break;
        }
        $order->save();
    }

    public function mount()
    {
        foreach ($this->user->orders as $order) {
            $this->trackingCodes[$order->id] = $order->tracking_code;
        }
    }

    public function saveTrackingCode($orderId)
    {
        $order = \App\Models\Order::query()->findOrFail($orderId);

        $this->validate([
            "trackingCodes.$orderId" => [
                'required',
                'string',
                'regex:/^\d{13}$|^\d{14}$|^\d{24}$/'
            ],
        ], [
            "trackingCodes.$orderId.regex" => 'کد مرسوله باید ۱۳ یا ۱۴ یا ۲۴ رقم باشد.',
        ]);

        $order->tracking_code = $this->trackingCodes[$orderId];
        $order->save();
        $this->sendTrackingSms($this->trackingCodes[$orderId]);
        $this->dispatch('showNotification', message: 'کد مرسوله ذخیره شد');
    }

    public function sendTrackingSms($tracking): void
    {
        $link = 'https://tracking.post.ir/?id=' . $tracking;
        $username = '09169889759';
        $password = 'Faraz@1920115072';
        $from = '3000505';
        $pattern_code = 'kvuck8sq6r7mwgd';
        $to = array('98' . substr($this->user->mobile, 1));
        $input_data = array('tracking' => $link);

        $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" .
            urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) .
            "&pattern_code=$pattern_code";
        $handler = curl_init($url);

        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handler);
        curl_close($handler);
    }

    public function verifyTransaction($id)
    {

        $transaction = Transaction::query()->findOrFail($id);
        $order = $transaction->order;

        foreach ($order->items as $item) {
            if ($item->variant_id && $item->variant) {
                if ($item->variant->stock < $item->quantity) {
                    abort('403', 'موجودی ' . Product::query()->find($item->product_id)->title . '-' . ProductVariant::query()->find($item->variant_id)->title . ' کمتر از این سفارش است.');
                }
            } else {
                if (Product::query()->find($item->product_id)->stock < $item->quantity) {
                    abort('403', 'موجودی ' . Product::query()->find($item->product_id)->title . ' کمتر از این سفارش است.');
                }
            }
        }

        if ($transaction->status == 'pending') {
            $transaction->status = 'success';
            $transaction->save();

            $order->status = 'paid';
            $order->shipping_status = 'processing';
            $order->save();

            foreach ($transaction->order->items as $item) {

                if ($item->variant_id && $item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
            $this->dispatch('showNotification', message: 'تراکنش تایید شد و موجودی اقلام سفارش کاهش یافت');

        } else {
            $this->dispatch('showNotification', message: 'فقط تراکنش های در حال انتظار قابل تایید هستند');
        }
    }

    public function failedTransaction($id)
    {
        $transaction = Transaction::query()->findOrFail($id);
        if ($transaction->status == 'pending') {
            $transaction->status = 'failed';
            $transaction->save();

            $this->dispatch('showNotification', message: 'تراکنش تایید نشد');

        } else {
            $this->dispatch('showNotification', message: 'فقط تراکنش های در حال انتظار قابل تایید یا لغو هستند');
        }
    }

    public function cancelTorobpayOrder($orderId)
    {
        $order = \App\Models\Order::query()->findOrFail($orderId);

        $transaction = $order->transactions()
            ->where('payment_gateway', 'torobpay')
            ->where('status', 'success')
            ->first();

        if (!$transaction) {
            $this->dispatch('showNotification', message: 'تراکنش ترب‌پی معتبری یافت نشد');
            return;
        }

        try {
            app(\App\Services\TorobpayService::class)->cancel($transaction->payment_token);

            $transaction->update([
                'status' => 'failed',
                'torobpay_status' => \App\Enums\TorobpayStatusEnum::CANCELLED->value,
            ]);

            $order->update([
                'status' => 'canceled',
                'shipping_status' => 'pending',
            ]);

            // برگردوندن موجودی
            foreach ($order->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                } else {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $this->dispatch('showNotification', message: 'سفارش با موفقیت کنسل شد و موجودی برگشت');

        } catch (\Exception $e) {
            $this->dispatch('showNotification', message: 'خطا در کنسل کردن: ' . $e->getMessage());
        }
    }
    public function checkTorobpayStatus($transactionId)
    {
        $transaction = Transaction::query()->findOrFail($transactionId);

        try {
            $result = app(\App\Services\TorobpayService::class)->getStatus($transaction->payment_token);

            $this->dispatch('showNotification', message:
                'وضعیت ترب‌پی: ' . $result['status'] .
                ' | مبلغ: ' . number_format($result['amount'] / 10) . ' تومان' .
                ' | شناسه: ' . $result['transactionId']
            );

        } catch (\Exception $e) {
            $this->dispatch('showNotification', message: 'خطا: ' . $e->getMessage());
        }
    }
    public function render()
    {
        $orders = $this->user->orders()->latest()->get();
        return view('livewire.admin.user.order', compact('orders'))->layout('components.layouts.admin');
    }
}
