<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $users = User::where('type','client')->latest()->paginate(100);
        return view('livewire.admin.user.index',compact('users'))->layout('components.layouts.admin');
    }
}
