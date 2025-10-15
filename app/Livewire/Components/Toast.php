<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Toast extends Component
{
    public $message = '';
    public $type = 'info'; // success, error, warning, info
    public $show = false;

    protected $listeners = ['showNotification' => 'show'];

    public function show($message, $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.components.toast');
    }
}
