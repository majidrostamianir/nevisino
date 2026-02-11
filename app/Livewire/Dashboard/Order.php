<?php

namespace App\Livewire\Dashboard;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class Order extends Component
{
    protected User $user;
    public $orders ;
    public $openOrderId = null;

    public function toggleOrder($orderId)
    {
        if ($this->openOrderId === $orderId) {
            $this->openOrderId = null;
        } else {
            $this->openOrderId = $orderId;
        }
    }

//    public function check()
//    {
//
//        try {
//            $receipt = Payment::amount(1000)->transactionId(4252488982)->verify();
//            $this->receipt = $receipt->getReferenceId();
//
//        } catch (InvalidPaymentException $exception) {
//
//            $this->receipt = $exception->getMessage();
//        }
//    }
    public function mount()
    {
        $this->user = Auth::user();
        $this->orders = $this->user->orders()
            ->orderByDesc('created_at')
            ->get();
    }

    public function payAgain($orderId)
    {
        $order = \App\Models\Order::query()
            ->where('id', $orderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $invoice = (new Invoice)->amount($order->amount);
        $payment = Payment::purchase($invoice, function ($driver, $transactionId) use ($order) {
            Transaction::query()->create([
                'order_id' => $order->id,
                'amount' => $order->amount,
                'status' => 'pending',
                'payment_gateway' => 'zibal',
                'authority' => (string)$transactionId,
            ]);
        });
        return redirect()->away($payment->pay()->getAction());
    }

    public function render()
    {
        return view('livewire.dashboard.order')->layout('components.layouts.admin');
    }
}
