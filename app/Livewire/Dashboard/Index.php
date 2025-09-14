<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Index extends Component
{

    public string $page = 'order';

    public array $menu = [
        'order'    => 'سفارش‌ها',
        'address' => 'آدرس‌ها',
        'profile'   => 'پروفایل',
    ];



    public function mount(?string $page = 'order'): void
    {
        $this->page = array_key_exists($page, $this->menu) ? $page : '404';
    }

    public function pageSet(string $slug): void
    {
        if (array_key_exists($slug, $this->menu)) {
            $this->page = $slug;

            $this->dispatch('navigate', url: route('dashboard', ['page' => $slug]));
        }
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
