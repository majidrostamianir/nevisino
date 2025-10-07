<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;

class ForgetPassword extends Component
{
    public string $mobile;

    protected function rules(): array
    {
        return [
            'mobile' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^09\d{9}$/', $value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('شماره موبایل یا ایمیل وارد شده معتبر نیست.');
                    }
                }
            ]
        ];
    }

    public function submit()
    {
        $this->mobile = persian_to_english_num($this->mobile);
        $this->validate();

        if (preg_match('/^09\d{9}$/', $this->mobile)) {
            if (User::where('mobile', $this->mobile)->whereNotNull('mobile_verified_at')->first()) {
                session()->put('mobile', $this->mobile);
                return redirect(route('reset-password'));
            }else{
                $this->addError('mobile' , 'شماره شما یافت نشد.');
            }
        } elseif (filter_var($this->mobile, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $this->mobile)->whereNotNull('email_verified_at')->first()) {
                session()->put('mobile', $this->mobile);
                return redirect(route('reset-password'));
            }else{
                $this->addError('mobile' , 'ایمیل شما یافت نشد.');

            }
        }
    }
    public function render()
    {
        return view('livewire.auth.forget-password')->layout('components.layouts.guest');
    }
}
