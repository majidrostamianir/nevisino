<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Toast extends Component
{
    public $message = '';
    public $type = 'info'; // success, error, warning, info
    public $show = false;
    public $showCartButton = false; // متغیر جدید برای نمایش دکمه سبد خرید

    protected $listeners = ['showNotification' => 'show'];

    public function show($message, $type = 'info', $showCartButton = true)
    {
        $this->message = $message;
        $this->type = $type;
        $this->showCartButton = $showCartButton; // مقداردهی متغیر جدید
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.components.toast');
    }
}