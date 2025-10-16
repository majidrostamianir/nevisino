<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class ForgetPassword extends Component
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

    public function submit()
    {
        $this->validate();

        if (preg_match('/^09\d{9}$/', $this->enMobile)) {
            if (User::where('mobile', $this->enMobile)->whereNotNull('mobile_verified_at')->first()) {
                session()->put('mobile', $this->enMobile);
                return redirect(route('verify-mobile'));
            } else {
                $this->addError('enMobile', 'شماره شما یافت نشد.');
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.forget-password')->layout('components.layouts.guest');
    }
}
