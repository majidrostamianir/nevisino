<?php

namespace App\Livewire\Dashboard;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TorobpayService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class Order extends Component
{
    protected User $user;
    public $orders;
    public $openOrderId = null;
    public string $payment_method = 'gateway';

    // ترب‌پی - یکدست با Checkout.php
    public bool $torobpayEligible = false;
    public string $torobpayTitle = 'پرداخت اقساطی با ترب پی';
    public string $torobpayDescription = '';

    public function mount()
    {
        $this->user   = Auth::user();
        $this->orders = $this->user->orders()
            ->with('transactions')
            ->orderByDesc('created_at')
            ->get();

        // چک eligibility برای سفارش pending (همیشه حداکثر یکی هست)
        $pendingOrder = $this->orders->firstWhere('status', 'pending');
        if ($pendingOrder) {
            $this->checkTorobpayEligibility($pendingOrder->amount);
        }
    }

    public function toggleOrder($orderId)
    {

        $this->openOrderId = $this->openOrderId === $orderId ? null : $orderId;

    }

    // ─────────────────────────────────────────────
    //  بررسی صلاحیت ترب‌پی برای مبلغ سفارش
    // ─────────────────────────────────────────────

    private function checkTorobpayEligibility(int $amount): void
    {
        if ($amount <= 0) {
            $this->torobpayEligible = false;
            return;
        }

        try {
            $result = app(TorobpayService::class)->checkEligible($amount);
            $this->torobpayEligible = $result['eligible'];

            if ($result['eligible']) {
                $this->torobpayTitle       = $result['message_title'] ?? 'پرداخت اقساطی با ترب پی';
                $this->torobpayDescription = $result['description'] ?? '';
            }
        } catch (\Exception $e) {
            $this->torobpayEligible = false;
        }
    }
    // ─────────────────────────────────────────────
    //  پرداخت مجدد
    // ─────────────────────────────────────────────

    public function pay($orderId = null)
    {
        $order = \App\Models\Order::query()
            ->with('items.product')
            ->where('id', $orderId ?? $this->openOrderId)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        switch ($this->payment_method) {

            case 'gateway':
                $invoice = (new Invoice)->amount($order->amount);
                $payment = Payment::purchase($invoice, function ($driver, $transactionId) use ($order) {
                    Transaction::query()->create([
                        'order_id'        => $order->id,
                        'amount'          => $order->amount,
                        'status'          => 'pending',
                        'payment_gateway' => 'zibal',
                        'authority'       => (string) $transactionId,
                    ]);
                });
                return redirect()->away($payment->pay()->getAction());

            case 'card':
                Transaction::query()->create([
                    'order_id'        => $order->id,
                    'amount'          => $order->amount,
                    'status'          => 'pending',
                    'payment_gateway' => 'card',
                    'authority'       => '5022291533610273',
                ]);
                return $this->redirect('/dashboard/order?open=' . $order->order_number, navigate: true);

            case 'torobpay':
                if (!$this->torobpayEligible) {
                    abort(403 , 'پرداخت اقساطی در حال حاضر در دسترس نیست.');

                }

                $transaction = Transaction::query()->create([
                    'order_id'        => $order->id,
                    'amount'          => $order->amount,
                    'status'          => 'pending',
                    'payment_gateway' => 'torobpay',
                    'authority'       => '',
                ]);

                try {
                    $orderParams = [
                        'province_id'      => $order->province,
                        'city_id'          => $order->city,
                        'recipient_name'   => $order->recipient_name,
                        'recipient_mobile' => $order->recipient_mobile,
                        'postal_address'   => $order->postal_address,
                        'zipcode'          => $order->zipcode,
                        'description'      => $order->description,
                        'shipping_method'  => $order->shipping_method,
                        'shipping_price'   => $order->shipping_price,
                    ];

                    $result = app(TorobpayService::class)->createPaymentToken($transaction, $orderParams);
                    $transaction->update(['payment_token' => $result['paymentToken']]);

                    return redirect()->away($result['paymentPageUrl']);

                } catch (\Exception $e) {
                    $transaction->update(['status' => 'failed']);
                    abort(403 , 'خطا در اتصال به درگاه ترب‌پی. لطفاً مجدداً تلاش کنید.');

                }
        }
    }

    // متد قدیمی payAgain رو نگه میداریم تا چیزی نشکنه
    public function payAgain($orderId)
    {
        return $this->pay($orderId);
    }

    public function render()
    {
        return view('livewire.dashboard.order')->layout('components.layouts.admin');
    }
}