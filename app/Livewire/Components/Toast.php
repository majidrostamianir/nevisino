<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Toast extends Component
{
    public $message = '';
    public $type = 'info';
    public $show = false;
    public $notificationId = null;

    protected $listeners = ['showNotification' => 'show'];

    public function show($message, $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;
        $this->notificationId = uniqid();
        $this->dispatch('start-notification-timer', notificationId: $this->notificationId);
    }

    public function hide()
    {
        $this->show = false;
        $this->message = '';
        $this->notificationId = null;
    }

    public function render()
    {
        return view('livewire.components.toast');
    }
}
