<?php

namespace App\Livewire\Home;

use Livewire\Component;

class Toast extends Component
{
    public $message = '';
    public $type = 'info'; // مقدار پیش‌فرض به info تغییر کرد
    public $show = false;
    public $notificationId = null;

    protected $listeners = ['showNotification' => 'show'];

    public function show($message, $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;
        $this->notificationId = uniqid();
        // تنظیم تایمر برای حذف خودکار
        $this->dispatch('start-notification-timer', notificationId: $this->notificationId);
    }

    public function hide()
    {
        $this->show = false;
        $this->message = '';
        $this->notificationId = null;
    }

//    public function startTimer($notificationId)
//    {
//        // فقط اگر notificationId مطابقت دارد تایمر را شروع کن
//        if ($this->notificationId === $notificationId) {
//            $this->dispatch('hide-notification-after-delay', notificationId: $notificationId);
//        }
//    }

//    public function hideAfterDelay($notificationId)
//    {
//        // فقط اگر notificationId مطابقت دارد مخفی کن
//        if ($this->notificationId === $notificationId) {
//            sleep(2); // تاخیر 2 ثانیه
//            $this->hide();
//        }
//    }
    // متد کمکی برای دریافت کلاس‌های رنگ بر اساس نوع
    public function getColorClasses()
    {
        return match($this->type) {
            'success' => [
                'border' => 'border-green-500',
                'text' => 'text-green-500',
                'bg' => 'bg-green-50',
                'icon' => 'green'
            ],
            'error' => [
                'border' => 'border-red-500',
                'text' => 'text-red-500',
                'bg' => 'bg-red-50',
                'icon' => 'red'
            ],
            'warning' => [
                'border' => 'border-yellow-500',
                'text' => 'text-yellow-500',
                'bg' => 'bg-yellow-50',
                'icon' => 'yellow'
            ],
            'info' => [
                'border' => 'border-blue-500',
                'text' => 'text-blue-500',
                'bg' => 'bg-blue-50',
                'icon' => 'blue'
            ],
            'primary' => [
                'border' => 'border-purple-500',
                'text' => 'text-purple-500',
                'bg' => 'bg-purple-50',
                'icon' => 'purple'
            ],
            'secondary' => [
                'border' => 'border-gray-500',
                'text' => 'text-gray-500',
                'bg' => 'bg-gray-50',
                'icon' => 'gray'
            ],
            'dark' => [
                'border' => 'border-gray-800',
                'text' => 'text-gray-800',
                'bg' => 'bg-gray-100',
                'icon' => 'gray'
            ],
            'light' => [
                'border' => 'border-gray-300',
                'text' => 'text-gray-700',
                'bg' => 'bg-white',
                'icon' => 'gray'
            ],
            default => [
                'border' => 'border-blue-500',
                'text' => 'text-blue-500',
                'bg' => 'bg-blue-50',
                'icon' => 'blue'
            ]
        };
    }

    public function render()
    {
        return view('livewire.home.toast');
    }
}
