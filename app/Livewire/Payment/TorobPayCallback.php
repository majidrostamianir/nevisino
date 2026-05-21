<?php

namespace App\Livewire\Payment;

use Livewire\Component;

class TorobPayCallback extends Component
{
    public bool $success = false;
    public array $data = [];

    public function mount()
    {
        $result = session('torobpay_result');

        if (!$result) {
            return redirect('/');
        }

        $this->success = $result['success'];

        if ($this->success) {
            $this->data = $result;
        }
    }
    public function render()
    {
        return view('livewire.payment.torob-pay-callback');
    }
}
