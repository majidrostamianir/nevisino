<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Index extends Component
{
    public function mount()
    {
        \Auth::logout();
    }
    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
