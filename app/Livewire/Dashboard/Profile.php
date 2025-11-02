<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public User $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function logout()
    {
        session()->forget('mobile');
        Auth::logout();
        return $this->redirect('/', navigate: true);

    }
    public function render()
    {
        return view('livewire.dashboard.profile');
    }
}
