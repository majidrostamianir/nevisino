<?php

namespace App\Livewire\Payment;


use App\Models\Address;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TorobPayService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class Checkout extends Component
{

    public $isTorobpayEligible = false;
    public $torobpayTitle = '';
    public $torobpayDescription = '';

    public string $payment_method = 'gateway';
    public User $user;
    public Address|null $selectedAddress = null;
    public array|null $cart = [];
    public Collection $addresses;
    public $cities = [];
    public int $shipping = 0, $amount = 0, $sum = 0;
    public $recipient_name, $recipient_mobile, $province_id, $city_id, $postal_address, $zipcode, $description;
    public $showPopup = false;

    public function mount()
    {
        $this->user = Auth::user();
        $this->addresses = $this->user->addresses()->get();
        $this->selectedAddress = $this->addresses[0] ?? null;
        $this->calculateAmount();
        $this->checkTorobpayEligibility();
    }

    public function checkTorobpayEligibility()
    {
        try {
            $torobpay = new TorobPayService();
            $result = $torobpay->checkEligibility($this->amount);

            $this->isTorobpayEligible = $result['eligible'] ?? false;
            $this->torobpayTitle = $result['title_message'] ?? 'پرداخت اقساطی با ترب پی';
            $this->torobpayDescription = $result['description'] ?? 'پرداخت اقساطی با ترب پی';

        } catch (\Exception $e) {
            // اگر خطایی بود، گزینه رو نشون نده
            $this->isTorobpayEligible = false;
            \Log::error('TorobPay eligibility error: ' . $e->getMessage());
        }
    }

    public function payWithTorobpay()
    {
        return redirect()->route('torobpay.initiate');
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
        'postal_address' => 'required|string|min:10|max:1000',
        'zipcode' => 'required|digits:10',
        'description' => 'nullable|string|max:1000'
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

        switch ($this->payment_method) {
            case 'gateway':
                $order = $cart->convertToOrder($this->selectedAddress->province->id, $this->selectedAddress->city->id, $this->selectedAddress->recipient_name, $this->selectedAddress->recipient_mobile, $this->selectedAddress->postal_address, $this->selectedAddress->zipcode, $this->description);
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
                $order = $cart->convertToOrder($this->selectedAddress->province->id, $this->selectedAddress->city->id, $this->selectedAddress->recipient_name, $this->selectedAddress->recipient_mobile, $this->selectedAddress->postal_address, $this->selectedAddress->zipcode, $this->description);
                Transaction::query()->create([
                    'order_id' => $order->id,
                    'amount' => $this->amount,
                    'status' => 'pending',
                    'payment_gateway' => 'card',
                    'authority' => '5022291533610273',
                ]);
                return $this->redirect('/dashboard/order?open=' . $order->order_number, navigate: true);
            case 'torobpay':
                $order = $cart->convertToOrder($this->selectedAddress->province->id, $this->selectedAddress->city->id, $this->selectedAddress->recipient_name, $this->selectedAddress->recipient_mobile, $this->selectedAddress->postal_address, $this->selectedAddress->zipcode, $this->description);
                $transaction = Transaction::query()->create([
                    'order_id' => $order->id,
                    'amount' => $this->amount,
                    'status' => 'pending',
                    'payment_gateway' => 'torobpay',
                    'authority' => 'pending_' . $order->id, // مقدار موقتی
                ]);
                // 3. ذخیره order_id در سشن تا کنترلر ترب پی بتونه پیدا کنه
                session(['torobpay_order_id' => $order->id]);
                return redirect()->route('torobpay.initiate');
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
        $this->recipient_mobile = $this->selectedAddress->recipient_mobile ?? $this->user->mobile;
        return view('livewire.payment.checkout');
    }
}
