<?php

namespace App\Livewire\Admin\Url;

use App\Models\Url;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $menus ;
    public Collection $urls ;
    public Url $url ;


    public function mount(): void
    {
        $this->menus = \App\Models\Menu::query()->whereNotNull('parent_id')->get();
        $this->urls = Url::all();
    }

    public function render()
    {
        return view('livewire.admin.url.index')->layout('components.layouts.admin');
    }
}
