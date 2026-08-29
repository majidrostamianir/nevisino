<?php

namespace App\Livewire\Payment;

use App\Models\Address;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TorobpayService;
use App\Helpers\PriceHelper;

// <-- اضافه کن
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class Checkout extends Component
{
    public $recipient_name, $recipient_mobile, $province_id, $city_id, $postal_address, $zipcode, $description;
    public User $user;
    public Address|null $selectedAddress = null;
    public Collection $addresses;
    public $cities = [];
    public $showPopup = false;
    public string $shipping_method = 'post_cod';
    public string $payment_method = 'gateway';

    public int $packaging_price = 0, $shipping_price = 0;

    public int $free_packaging_threshold = 0, $free_shipping_threshold = 0;

    public int $sum = 0, $amount = 0, $maxPackagingSize = 1;


    // ترب‌پی
    public bool $torobpayEligible = false;
    public string $torobpayTitle = 'پرداخت اقساطی با ترب پی';
    public string $torobpayDescription = '';


    public function mount()
    {
        $this->user = Auth::user();
        $this->addresses = $this->user->addresses()->get();
        $this->selectedAddress = $this->addresses[0] ?? null;


        $this->calculateSum();
        $this->calculatePackaging();
        $this->calculateShipping();
        $this->calculateAmount();

        if ($this->sum == 0) {
            return redirect()->route('cart');
        }
        $this->checkTorobpayEligibility();
    }

    private function checkTorobpayEligibility(): void
    {

        $this->torobpayEligible = false;


       /* if ($this->amount <= 0) {
            $this->torobpayEligible = false;
            return;
        }
        try {
            $service = app(TorobpayService::class);
            $result = $service->checkEligible($this->amount);
            $this->torobpayEligible = $result['eligible'];

            if ($result['eligible']) {
                $this->torobpayTitle = $result['message_title'] ?? 'پرداخت اقساطی با ترب پی';
                $this->torobpayDescription = $result['description'] ?? '';
            }
        } catch (\Exception $e) {
            $this->torobpayEligible = false;
        }*/
    }

    private function calculateSum()
    {
        $cartItems = \App\Models\CartItem::query()->whereHas('cart', fn($q) => $q->where('user_id', Auth::id()))
            ->with('product', 'variant')->get();

        $this->sum = $cartItems->sum(function ($item) {
            $price = PriceHelper::getProductPrice($item->product); // <-- اصلاح شده
            return $price * $item->quantity;
        });

        $this->maxPackagingSize = $cartItems->max(function ($item) {
            return (int)($item->product->size ?? 1);
        }) ?? 1;

        if ($this->maxPackagingSize == 0) {
            $this->maxPackagingSize = 1;
        }
    }

    private function calculatePackaging()
    {
        $this->free_packaging_threshold = Setting::get('free_packaging_threshold');
        if ($this->sum < $this->free_packaging_threshold) {
            $this->packaging_price = Setting::get('packaging_' . $this->maxPackagingSize);
        } else {
            $this->packaging_price = 0;
        }
    }

    private function calculateShipping()
    {
        $this->free_shipping_threshold = Setting::get('free_shipping_threshold');

        if ($this->sum < $this->free_shipping_threshold && in_array($this->shipping_method, ['post_free', 'tipax_free'])) {
            $this->shipping_method = 'post_cod';
        }
        if ($this->sum >= $this->free_shipping_threshold && in_array($this->shipping_method, ['post_cash', 'tipax_cash', 'post_cod', 'tipax_cod'])) {
            $this->shipping_method = 'post_free';
        }

        if ($this->sum < $this->free_shipping_threshold) {
            $this->shipping_price = match ($this->shipping_method) {
                'post_cash' => Setting::get('post_price'),
                'tipax_cash' => Setting::get('tipax_price'),
                'post_cod', 'post_free', 'tipax_cod', 'tipax_free' => 0,
            };
        } else {
            $this->shipping_price = 0;
        }
    }

    public function calculateAmount()
    {
        $this->amount = $this->sum + $this->shipping_price + $this->packaging_price;
    }


    public function updateShippingMethod($method)
    {
        $this->shipping_method = $method;
        $this->calculateSum();
        $this->calculatePackaging();
        $this->calculateShipping();
        $this->calculateAmount();

        $this->checkTorobpayEligibility();
    }

    public function selectAddress($value)
    {
        $this->dispatch('close-popup');
        $this->selectedAddress = Address::query()
            ->where('user_id', Auth::id())
            ->findOrFail($value);
    }

    public function changeAddress()
    {
        $this->dispatch('close-popup');
        $this->selectedAddress = null;
        $this->recipient_mobile = $this->user->mobile;
    }

    protected $rules = [
        'recipient_name' => 'required|min:3|string',
        'recipient_mobile' => 'required|digits:11|regex:/^09\d{9}$/',
        'province_id' => 'required|string|min:1|max:3',
        'city_id' => 'required|string|min:1|max:5',
        'postal_address' => 'required|string|min:10|max:200',
        'zipcode' => 'required|digits:10',
        'description' => 'nullable|string|max:200',
    ];

    public function pay()
    {
        $this->description = str_replace(["\r\n", "\r", "\n"], ' ', $this->description);
        $this->postal_address = str_replace(["\r\n", "\r", "\n"], ' ', $this->postal_address);

        $this->validateOnly('description');

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

        $this->calculateSum();
        $this->calculatePackaging();
        $this->calculateShipping();
        $this->calculateAmount();
        $finalAmount = $this->amount; // snapshot بگیر

        $cart = $this->user->cart()
            ->with('items.product', 'items.variant')
            ->first();
        if (!$cart) {
            return redirect()->route('cart');
        }

        Auth::user()->orders()->where('user_id', Auth::id())->where('status', 'pending')->update(['status' => 'canceled']);
        $orderParams = $this->getOrderParams();

        switch ($this->payment_method) {
            case 'gateway':
                $order = $cart->convertToOrder($orderParams);
                $invoice = (new Invoice)->amount($finalAmount);
                $payment = Payment::purchase($invoice, function ($driver, $transactionId) use ($finalAmount, $order) {
                    Transaction::query()->create([
                        'order_id' => $order->id,
                        'amount' => $finalAmount,
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
                    'amount' => $finalAmount,
                    'status' => 'pending',
                    'payment_gateway' => 'card',
                    'authority' => '5022291533610273',
                ]);
                return $this->redirect('/dashboard/order?open=' . $order->order_number, navigate: true);

            case 'torobpay':
                if (!$this->torobpayEligible) {
                    abort(403, 'پرداخت اقساطی در حال حاضر در دسترس نیست.');
                }

                $order = $cart->convertToOrder($orderParams);

                $transaction = Transaction::query()->create([
                    'order_id' => $order->id,
                    'amount' => $this->amount,
                    'status' => 'pending',
                    'payment_gateway' => 'torobpay',
                    'authority' => '',
                ]);

                try {
                    $result = app(TorobpayService::class)->createPaymentToken($transaction, $orderParams);
                    $transaction->update(['payment_token' => $result['paymentToken']]);

                    return redirect()->away($result['paymentPageUrl']);

                } catch (\Exception $e) {
                    $transaction->update(['status' => 'failed']);
                    Log::error($e->getMessage());
                    abort(403, 'خطا در اتصال به درگاه ترب‌پی. لطفاً مجدداً تلاش کنید.');
                }
        }
    }

    public function updatedProvinceId($provinceId)
    {
        $this->cities = \App\Models\City::where('province_id', $provinceId)->get();
        $this->city_id = null;
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
            'shipping_price' => $this->shipping_price,
            'packaging_price' => $this->packaging_price,
        ];
    }

    public function render()
    {
        return view('livewire.payment.checkout');
    }
}