<?php

namespace App\Livewire\Payment;


use App\Models\Address;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class Checkout extends Component
{


    public User $user;
    public Address|null $selectedAddress = null;
    public array|null $cart = [];
    public Collection $addresses;
    public $cities = [];
    public int $shipping = 0, $amount = 0, $sum = 0;
    public $recipient_name, $recipient_mobile, $province_id, $city_id, $postal_address, $zipcode, $description;

    public function mount()
    {
        $this->user = Auth::user();
        $this->addresses = $this->user->addresses()->get();
        $this->selectedAddress = $this->addresses[0] ?? null;
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile ?? english_to_persian_num($this->user->mobile);
        $this->calculateAmount();
    }

    public function selectAddress($value)
    {
        $this->dispatch('close-popup');
        $this->selectedAddress = Address::query()->find($value);
    }

    public function changeAddress()
    {
        $this->dispatch('close-popup');
        $this->selectedAddress = null;
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile ?? english_to_persian_num($this->user->mobile);
    }


    protected $rules = [
        'recipient_name' => 'required|min:3|string',
        'recipient_mobile' => 'required|digits:11|regex:/^09\d{9}$/',
        'province_id' => 'required|string|min:1|max:3',
        'city_id' => 'required|string|min:1|max:5',
        'postal_address' => 'required|string|min:10|max:1000',
        'zipcode' => 'required|digits:10',
        'description' => 'nullable|string|max:1000'
    ];

    public function pay()
    {
        if ($this->selectedAddress == null) {
            $this->validate();
            $this->selectedAddress = $this->user->addresses()->create([
                'recipient_name' => $this->recipient_name,
                'recipient_mobile' => $this->recipient_mobile,
                'province_id' => $this->province_id,
                'city_id' => $this->city_id,
                'postal_address' => $this->postal_address,
                'zipcode' => $this->zipcode,
            ]);
            if ($this->user->name == null) {
                $this->user->update(['name' => $this->recipient_name]);
            }
        }

        $cart = $this->user->cart()
            ->with('items.product', 'items.variant')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return $this->redirect('/cart', navigate: true);
        }
        $order = $cart->convertToOrder($this->selectedAddress->province->id , $this->selectedAddress->city->id ,$this->selectedAddress->recipient_name, $this->selectedAddress->recipient_mobile, $this->selectedAddress->postal_address, $this->selectedAddress->zipcode, $this->description);


        $invoice = (new Invoice)->amount($this->amount);
        $payment = Payment::purchase($invoice, function ($driver, $transactionId) use ($order) {
            Transaction::query()->create([
                'order_id' => $order->id,
                'amount' => $this->amount,
                'status' => 'pending',
                'payment_gateway' => 'zibal',
                'authority' => (string)$transactionId,
            ]);
        });


        return redirect()->away($payment->pay()->getAction());

    }

    public function updatedProvinceId($provinceId)
    {
        $this->cities = \App\Models\City::where('province_id', $provinceId)->get();
        $this->city_id = null;
    }


    public function calculateAmount()
    {
        $cartItems = \App\Models\CartItem::query()->whereHas('cart', fn($q) => $q->where('user_id', Auth::id())
        )->with('product', 'variant')->get();

        $this->sum = $cartItems->sum(function ($item) {
            $price = $item->variant?->price ?? $item->product->price;
            return $price * $item->quantity;
        });


        if ($this->sum > 0) {
            $this->shipping = config('shop.shipping');
            $this->amount = $this->sum + $this->shipping;
        } else {
            $this->shipping = 0;
            $this->amount = 0;
        }
    }

    public function render()
    {
        return view('livewire.payment.checkout');
    }
}
