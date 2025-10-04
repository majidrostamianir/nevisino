<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    public User $user;
    #[Validate('required|string|min:4|max:255')]
    public string $password = '';
    public string $mobile;


    public function mount()
    {
        if (!session()->has('mobile')) {
            return $this->redirect('/register', navigate: true);
        }

        $this->mobile = session()->get('mobile');

        if (preg_match('/^09\d{9}$/', $this->mobile)) {
            $this->user = User::where('mobile', $this->mobile)->whereNotNull('mobile_verified_at')->firstOrFail();
        }
        return null;
    }

    public function submit()
    {
        $this->validate([
            'password' => 'required|string|min:4|max:255',
        ]);

        if (Hash::check($this->password, $this->user->password)) {
            Auth::login($this->user  , true);

            $this->transferCartFromSession($this->user);

            session()->forget('mobile');
            if (session('previous_url')) {
                $url = session('previous_url');
                session()->forget('previous_url');
                return $this->redirect($url);
            }
            return $this->redirect('/', navigate: true);
        } else {
            $this->addError('password', 'رمز عبور اشتباه است.');
            return null;
        }
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

            foreach ($sessionCart as  $item) {
                $cart->items()->create([
                    'product_id' => $item['id'],
                    'variant_id' => $item['variant'] ?? null,
                    'quantity' =>(int) persian_to_english_num($item['quantity']),
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
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}
