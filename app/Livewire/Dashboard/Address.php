<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Address extends Component
{
    public User $user;
    public Address $address;
    public \App\Models\Address|null $selectedAddress = null;

    public $cities = [];
    public $recipient_name, $recipient_mobile, $province_id, $city_id, $postal_address, $zipcode;
    public bool $showPopup = false;

    protected $rules = [
        'recipient_name' => 'required|min:3|string',
        'recipient_mobile' => 'required|digits:11|regex:/^09\d{9}$/',
        'province_id' => 'required|string|min:1|max:3',
        'city_id' => 'required|string|min:1|max:5',
        'postal_address' => 'required|string|min:10|max:1000',
        'zipcode' => 'required|digits:10',
    ];

    public function updatedProvinceId($provinceId)
    {
        $this->cities = \App\Models\City::where('province_id', $provinceId)->get();
        $this->city_id = null;
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function togglePopup()
    {
        $this->showPopup = !$this->showPopup;
    }

    public function selectAddress($value)
    {
        $this->togglePopup();
        $this->selectedAddress = \App\Models\Address::query()->find($value);
        $this->cities = \App\Models\City::where('province_id', $this->selectedAddress->province_id)->get();
        $this->recipient_name = $this->selectedAddress->recipient_name;
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile;
        $this->province_id = (string)$this->selectedAddress->province_id;
        $this->city_id = (string)$this->selectedAddress->city_id;
        $this->postal_address = $this->selectedAddress->postal_address;
        $this->zipcode = $this->selectedAddress->zipcode;

    }

    public function newAddress()
    {
        $this->selectedAddress = null;
        $this->recipient_name = '';
        $this->recipient_mobile = '';
        $this->province_id = '';
        $this->city_id = '';
        $this->postal_address = '';
        $this->zipcode = '';
        $this->togglePopup();
    }

    public function delete()
    {
        $this->selectedAddress->delete();
        $this->togglePopup();
    }

    public function save()
    {
        $this->validate();
        $this->selectedAddress = \App\Models\Address::query()->updateOrCreate(
            ['id' => $this->selectedAddress->id ?? null],
            [
                'recipient_name' => $this->recipient_name,
                'recipient_mobile' => $this->recipient_mobile,
                'province_id' => $this->province_id,
                'city_id' => $this->city_id,
                'postal_address' => $this->postal_address,
                'zipcode' => $this->zipcode,
                'user_id' => auth()->id(),
            ]
        );

        $this->togglePopup();
    }

    public function render()
    {
        $addresses = $this->user->addresses;
        return view('livewire.dashboard.address', compact('addresses'));
    }
}
