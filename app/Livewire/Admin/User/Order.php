<?php

namespace App\Livewire\Admin\User;

use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

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

        $this->dispatch('showNotification', message: 'کد مرسوله ذخیره شد');
    }

    public function verifyTransaction($authority)
    {
        $transaction = Transaction::query()->where('authority', $authority)->first();

        try {
            Payment::amount($transaction->amount)
                ->transactionId($transaction->authority)
                ->verify();

            $transaction->status = 'success';
            $transaction->save();

            $order = $transaction->order;
            $order->status = 'paid';
            $order->shipping_status = 'processing';
            $order->save();

        } catch (InvalidPaymentException $exception) {

        }
    }

    public function render()
    {
        $orders = $this->user->orders()->latest()->get();
        return view('livewire.admin.user.order', compact('orders'))->layout('components.layouts.admin');
    }
}
