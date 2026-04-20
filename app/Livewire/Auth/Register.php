<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class Register extends Component
{
    public string $faMobile = '', $enMobile = '';

    protected function rules(): array
    {
        return [
            'enMobile' => [
                'required',
                'string',
                'digits:11',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^09\d{9}$/', $value)) {
                        $fail('شماره موبایل وارد شده معتبر نیست.');
                    }
                }
            ]
        ];
    }

    public function mount()
    {
        session()->forget('mobile');
    }

    public function submit()
    {
        $this->validate();
        $otp = rand(1234, 9876);

        $user = User::where('mobile', $this->enMobile)->first();
        if ($user) {
            $user->update([
                'mobile_otp' => $otp,
                'mobile_otp_sent_count' => ($user->mobile_otp_sent_count ?? 0) + 1,
            ]);
        } else {
            User::create([
                'mobile' => $this->enMobile,
                'mobile_otp' => $otp,
                'mobile_otp_sent_count' => 0,
                'referrer' => session('newReferer', '-'),
            ]);
        }
        session()->put('mobile', $this->enMobile);
        return $this->redirect(route('verify-mobile'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.guest')->title('ثبت نام - نویسینو');
    }
}
