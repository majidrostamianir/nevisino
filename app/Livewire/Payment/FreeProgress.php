<?php

namespace App\Livewire\Payment;

use App\Models\Setting;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class FreeProgress extends Component
{
    public int $free_packaging_threshold = 0, $free_shipping_threshold = 0;
    #[Reactive]
    public $sum;

    public function mount()
    {
        $this->free_packaging_threshold = Setting::get('free_packaging_threshold');
        $this->free_shipping_threshold = Setting::get('free_shipping_threshold');
    }

    public function render()
    {
        return view('livewire.payment.free-progress');
    }
}
