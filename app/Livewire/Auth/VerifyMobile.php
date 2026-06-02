<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyMobile extends Component
{
    public string $faOtp = '' , $enOtp = ''  ;
    public User $user;
    public string $mobile = '';
    public int $remainingSeconds = 0; // ثانیه‌های باقی‌مانده برای تایمر

    public function mount()
    {
        $this->mobile = session()->get('mobile');
        if (!preg_match('/^09\d{9}$/', $this->mobile)){
            session()->forget('mobile');
            return redirect()->route('register');
        }
        $this->user = User::where('mobile', $this->mobile)->first();

        // محاسبه زمان باقی‌مانده از آخرین ارسال
        $this->calculateRemainingTime();

        // فقط اگر یک دقیقه از آخرین ارسال گذشته بود، کد جدید بفرست
        if ($this->remainingSeconds <= 0) {
            $this->sendOtp();
        }
    }

    /**
     * محاسبه زمان باقی‌مانده تا امکان ارسال مجدد
     */
    private function calculateRemainingTime(): void
    {
        if ($this->user->mobile_otp_sent_at) {
            $lastSent = Carbon::create($this->user->mobile_otp_sent_at);
            $secondsPassed = Carbon::now()->diffInSeconds($lastSent);
            dd($secondsPassed);

            if ($secondsPassed < 60) {
                $this->remainingSeconds = 60 - $secondsPassed;
            } else {
                $this->remainingSeconds = 0;
            }
        } else {
            $this->remainingSeconds = 0;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'enOtp' => 'required|string|digits:4',
        ]);

        $this->check_otp();
    }

    public function sendOtp(): void
    {
        // قبل از هر چیزی، دوباره چک کن که آیا زمان ارسال مجدد رسیده یا نه
        $this->calculateRemainingTime();

        if ($this->remainingSeconds > 0) {
            session()->flash('otp', 'لطفا ' . $this->remainingSeconds . ' ثانیه دیگر مجددا تلاش کنید...');
            session()->flash('color', 'text-orange-500');
            return;
        }

        // چک کردن محدودیت تعداد پیامک در کل (30 بار)
        if ($this->user->mobile_otp_sent_count >= 30) {
            session()->flash('otp','شماره شما مسدود شده است.');
            session()->flash('color', 'text-red-500');
            return;
        }

        // ارسال کد جدید
        $code = rand(1234, 9876);
        $this->user->mobile_otp_sent_at = Carbon::now()->toDateTimeString();
        $this->user->mobile_otp = $code;
        $this->user->mobile_otp_sent_count = $this->user->mobile_otp_sent_count + 1;
        $this->user->save();

        // ارسال پیامک
        $this->sendSms($code);

        // تنظیم مجدد تایمر
        $this->remainingSeconds = 60;

        session()->flash('otp', 'پیامک برای شما ارسال شد.');
        session()->flash('color', 'text-green-500');
    }

    /**
     * ارسال پیامک
     */
    private function sendSms(int $code): void
    {
        $username = '09169889759';
        $password = 'Faraz@1920115072';
        $from = '3000505';
        $pattern_code = '15zgbzfkih0dllr';
        $to = array('98' . substr($this->user->mobile, 1));
        $input_data = array('code' => $code, 'autocode' => $code);

//        $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" .
//            urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) .
//            "&pattern_code=$pattern_code";
//        $handler = curl_init($url);
//        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
//        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
//        curl_exec($handler);
//        curl_close($handler);
    }

    public function check_otp()
    {
        if (Carbon::create($this->user->mobile_otp_sent_at)->addMinutes(5) > Carbon::now()) {
            if ($this->enOtp === $this->user->mobile_otp) {
                $this->user->mobile_verified_at = Carbon::now()->toDateTimeString();
                $this->user->save();
                Auth::login($this->user, true);

                $this->transferCartFromSession($this->user);

                if (session('previous_url')) {
                    $url = session('previous_url');
                    session()->forget('previous_url');
                    return $this->redirect($url);
                }
                return $this->redirect('/', navigate: true);

            } else {
                session()->flash('otp','کد وارد شده اشتباه است.');
                session()->flash('color', 'text-red-500');
            }
        } else {
            session()->flash('otp','کد شما منقضی شده است.');
            session()->flash('color', 'text-red-500');
        }
        return null;
    }

    protected function transferCartFromSession($user)
    {
        $sessionCart = session()->get('cart', []);

        if (!empty($sessionCart)) {
            $previousCart = $user->cart()->first();
            if ($previousCart) {
                $previousCart->items()->delete();
                $previousCart->delete();
            }
            $cart = $user->cart()->create();

            foreach ($sessionCart as $item) {
                $cart->items()->create([
                    'product_id' => $item['id'],
                    'variant_id' => $item['variant'] ?? null,
                    'quantity' => (int) persian_to_english_num($item['quantity']),
                ]);
            }
            session()->forget('cart');
            session()->save();
        }
    }

    public function edit()
    {
        session()->forget('mobile');
        return $this->redirect('/register', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.verify-mobile')->layout('components.layouts.guest');
    }
}