<?php

namespace App\Livewire\Home;

use Livewire\Component;
use App\Models\ContactMessage;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;

class ContactPage extends Component
{
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:255')]
    public $contact = '';

    #[Rule('required|string|min:10')]
    public $message = '';

    public function sendMessage()
    {
        $this->validate();

        // 🔒 محافظت در برابر XSS
        $safeName = htmlspecialchars(trim($this->name), ENT_QUOTES, 'UTF-8');
        $safeContact = htmlspecialchars(trim($this->contact), ENT_QUOTES, 'UTF-8');
        $safeMessage = htmlspecialchars(trim($this->message), ENT_QUOTES, 'UTF-8');

        $data = [
            'name' => $safeName,
            'contact' => $safeContact,
            'message' => $safeMessage,
        ];

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        ContactMessage::create($data);
        $this->reset(['name', 'contact', 'message']);
        session()->flash('success', 'پیام شما با موفقیت ارسال شد. باتشکر.');
    }

    public function render()
    {
        return view('livewire.home.contact-page')->title('تماس با ما | نویسینو');
    }
}