<?php

namespace App\Livewire\Payment;

use Livewire\Component;

class DigipayCallback extends Component
{
    public bool $success = false;
    public string $status = 'failure'; // success | cancel | failure
    public array $data = [];

    public function mount()
    {
        $result = session('digipay_result');

        if (!$result) {
            return redirect('/');
        }

        $this->success = $result['success'] ?? false;
        $this->status  = $result['status'] ?? 'failure';
        $this->data    = $result;
    }

    public function render()
    {
        return view('livewire.payment.digipay-callback');
    }
}