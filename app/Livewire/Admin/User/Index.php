<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $users;
    public $queryName , $queryMobile;

    public function mount()
    {
        $this->users = User::where('type', 'client')->latest()->get();
    }

    public function loginUser($id)
    {
        Auth::loginUsingId($id);
    }

    public function updatedQueryName()
    {
        $this->users = \App\Models\User::search($this->queryName);

    }

    public function updatedQueryMobile()
    {
        $this->users = \App\Models\User::search($this->queryMobile);

    }

    public function render()
    {
        return view('livewire.admin.user.index')->layout('components.layouts.admin');
    }
}
