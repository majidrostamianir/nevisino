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


    public string $payment_method = 'gateway';
    public User $user;
    public Address|null $selectedAddress = null;
    public array|null $cart = [];
    public Collection $addresses;
    public $cities = [];
    public int $shipping_price = 0, $amount = 0, $sum = 0;
    public $recipient_name, $recipient_mobile, $province_id, $city_id, $postal_address, $zipcode, $description;
    public $showPopup = false;
    public string $shipping_method = 'post_cod';

    public function mount()
    {
        $this->user = Auth::user();
        $this->addresses = $this->user->addresses()->get();
        $this->selectedAddress = $this->addresses[0] ?? null;
        $this->calculateAmount();
        $this->shipping_method = 'post_cod';
        $this->shipping_price = 0;
    }

    public function updateShippingMethod($method)
    {
        $this->shipping_method = $method;
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
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile ?? $this->user->mobile;
    }


    protected $rules = [
        'recipient_name' => 'required|min:3|string',
        'recipient_mobile' => 'required|digits:11|regex:/^09\d{9}$/',
        'province_id' => 'required|string|min:1|max:3',
        'city_id' => 'required|string|min:1|max:5',
        'postal_address' => 'required|string|min:10|max:200',
        'zipcode' => 'required|digits:10',
        'description' => 'nullable|string|max:200',
        'shipping_method' => 'required|in:post_cod,post_cash,tipax_cod,tipax_cash',
        'shipping_price' => 'required|integer',
    ];

    public function pay()
    {
        $this->description = str_replace(["\r\n", "\r", "\n"], ' ', $this->description);
        $this->postal_address = str_replace(["\r\n", "\r", "\n"], ' ', $this->postal_address);

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

        Auth::user()->orders()->where('status', 'pending')->update(['status' => 'canceled']);
        $orderParams = $this->getOrderParams();

        switch ($this->payment_method) {
            case 'gateway':
                $order = $cart->convertToOrder($orderParams);
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

            case 'card':
                $order = $cart->convertToOrder($orderParams);
                Transaction::query()->create([
                    'order_id' => $order->id,
                    'amount' => $this->amount,
                    'status' => 'pending',
                    'payment_gateway' => 'card',
                    'authority' => '5022291533610273',
                ]);
                return $this->redirect('/dashboard/order?open=' . $order->order_number, navigate: true);
            case 'torobpay':
//                $order = $cart->convertToOrder($orderParams);
//                $transaction = Transaction::query()->create([
//                    'order_id' => $order->id,
//                    'amount' => $this->amount,  //به تومان
//                    'status' => 'pending',
//                    'payment_gateway' => 'torobpay',
//                ]);

                return redirect()->route('');

        }
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
            $price = $item->product->discounted_price ?? $item->product->price;
            return $price * $item->quantity;
        });


        if ($this->sum <= 0) {
            $this->shipping_price = 0;
            $this->amount = 0;
            return;
        }


        switch ($this->shipping_method) {
            case 'post_cash':
                $this->shipping_price = config('shop.post_price');
                break;
            case 'tipax_cash':
                $this->shipping_price = config('shop.tipax_price');
                break;
            default:
                $this->shipping_price = 0;
                break;
        }

        $this->amount = $this->sum + $this->shipping_price;
    }

    private function getOrderParams(): array
    {
        return [
            'province_id' => $this->selectedAddress->province->id,
            'city_id' => $this->selectedAddress->city->id,
            'recipient_name' => $this->selectedAddress->recipient_name,
            'recipient_mobile' => $this->selectedAddress->recipient_mobile,
            'postal_address' => $this->selectedAddress->postal_address,
            'zipcode' => $this->selectedAddress->zipcode,
            'description' => $this->description,
            'shipping_method' => $this->shipping_method,
            'shipping_price' => $this->shipping_price
        ];
    }

    public function render()
    {
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile ?? $this->user->mobile;
        return view('livewire.payment.checkout');
    }
}
