<?php

namespace App\Livewire\Admin\Setting;

use Livewire\Component;
use App\Models\Setting;
use App\Models\Product;

class Index extends Component
{
    public $settings = [];
    public $message = '';
    public $messageType = 'success';

    public function mount()
    {
        // بارگذاری تنظیمات با مقادیر اصلی (0 و 1 برای بولی)
        $settings = Setting::all();
        foreach ($settings as $setting) {
            $this->settings[$setting->key] = $setting->value;
        }
    }

    public function save()
    {
        try {
            foreach ($this->settings as $key => $value) {
                // پیدا کردن رکورد
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    // اگر نوع بولی بود، مقدار رو به عدد تبدیل کن
                    if ($setting->type === 'boolean') {
                        $value = (int) $value;
                    }
                    $setting->update(['value' => $value]);
                }
            }

            // تنظیمات رو دوباره بارگذاری کن
            $settings = Setting::all();
            $this->settings = [];
            foreach ($settings as $setting) {
                $this->settings[$setting->key] = $setting->value;
            }

            $this->message = '✅ تنظیمات با موفقیت ذخیره شد!';
            $this->messageType = 'success';

        } catch (\Exception $e) {
            $this->message = '❌ خطا: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function getTotalProducts()
    {
        return Product::count();
    }

    public function render()
    {
        return view('livewire.admin.setting.index', [
            'totalProducts' => $this->getTotalProducts(),
            'settingsCount' => count($this->settings),
        ])->layout('components.layouts.admin');
    }
}