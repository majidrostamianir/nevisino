<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.setting.index')->layout('components.layouts.admin');
    }
}
