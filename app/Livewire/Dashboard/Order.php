<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

class Order extends Component
{
    protected User $user;
    public $orders ;
//    public $receipt = 'kk';
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
        $this->orders = $this->user->orders;
    }

    public function render()
    {
        return view('livewire.dashboard.order');
    }
}
