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

    public function nextStep($orderId)
    {
        $order = \App\Models\Order::query()->findOrFail($orderId);
        $order->shipping_status = 'preparing';
        $order->save();
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
//            $transaction->status = 'failed';
//            $transaction->save();

//            return "تراکنش ناموفق بود";
        }
    }

    public function render()
    {
        $orders = $this->user->orders;
        return view('livewire.admin.user.order', compact('orders'))->layout('components.layouts.admin');
    }
}
