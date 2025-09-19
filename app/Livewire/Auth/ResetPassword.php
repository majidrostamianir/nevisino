<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $mobile;
    public User $user;
    #[Validate('required|numeric|digits:4')]
    public string $otp;
    #[Validate('required|string|min:4|max:255')]
    public string $password;

    public function mount()
    {
        if (!session()->has('mobile')) {
            return $this->redirect(route('register'));
        }

        $this->mobile = session()->get('mobile');
        if (preg_match('/^09\d{9}$/', $this->mobile)) {
            $this->user = User::where('mobile', $this->mobile)->whereNotNull('mobile_verified_at')->firstOrFail();
            $this->sendMobileOtp();
        }
    }

    public function submit()
    {
        if (preg_match('/^09\d{9}$/', $this->mobile)){
            $this->checkMobileOtp();
        }
    }



    public function sendMobileOtp(): void
    {
        if ($this->user->mobile_otp_sent_count < 10) {
            if ($this->user->mobile_otp_sent_at == null || Carbon::create($this->user->mobile_otp_sent_at)->addMinute() < Carbon::now()) {
                session()->flash('otp', 'پیامک برای شما ارسال شد.');
                session()->flash('color', 'text-green-500');
                $code = rand(1234, 9876);
                $this->user->mobile_otp_sent_at = Carbon::now()->toDateTimeString();
                $this->user->mobile_otp = $code;
                $this->user->mobile_otp_sent_count = $this->user->mobile_otp_sent_count + 1;
                $this->user->save();

                $username = '09169889759';
                $password = 'Faraz@1920115072';
                $from = '3000505';
                $pattern_code = '3cslb0fnh3htun7';
                $to = array('98' . substr($this->user->mobile, 1));
                $input_data = array('vcode' => $code);

                $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" .
                    urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) .
                    "&pattern_code=$pattern_code";
                $handler = curl_init($url);
                curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
                curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
                curl_exec($handler);
                curl_close($handler);
            } else {
                session()->flash('otp', 'لطفا یک دقیقه دیگر مجددا تلاش کنید...');
                session()->flash('color', 'text-orange-500');
            }
        } else {
            session()->flash('otp', 'شماره شما مسدود شده است.');
            session()->flash('color', 'text-red-500');
        }
    }

    public function checkMobileOtp()
    {
        $this->validate();
        if (Carbon::create($this->user->mobile_otp_sent_at)->addMinutes(5) > Carbon::now()) {
            if ($this->otp === $this->user->mobile_otp) {
                $this->user->mobile_verified_at = Carbon::now()->toDateTimeString();
                $this->user->password = Hash::make($this->password);
                $this->user->save();
                \Auth::login($this->user);
                if (session('previous_url')) {
                    $url = session('previous_url');
                    session()->forget('previous_url');
                    return $this->redirect($url);
                }
                return $this->redirect('/', navigate: true);

            } else {
                session()->flash('otp', 'کد وارد شده اشتباه است.');
                session()->flash('color', 'text-red-500');
            }
        } else {
            session()->flash('otp', 'کد شما منقضی شده است.');
            session()->flash('color', 'text-red-500');
        }
    }

    public function edit()
    {
        session()->forget('mobile');
        return $this->redirect('/register', navigate: true);


    }

    public function render()
    {
        return view('livewire.auth.reset-password')->layout('components.layouts.guest');
    }
}
