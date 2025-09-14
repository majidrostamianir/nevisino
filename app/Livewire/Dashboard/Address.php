<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Address extends Component
{
    public User $user;
    public Address $address;
    public $addresses ;
    public \App\Models\Address|null $selectedAddress = null;

    public bool $showPopup = false;


    public function mount()
    {
        $this->user = Auth::user();
        $this->addresses = $this->user->addresses;
    }
    public function togglePopup()
    {
        $this->showPopup = !$this->showPopup;
    }
    public function selectAddress($value)
    {
        $this->togglePopup();
        $this->selectedAddress = \App\Models\Address::query()->find($value);
    }
    public function render()
    {
        return view('livewire.dashboard.address');
    }
}
