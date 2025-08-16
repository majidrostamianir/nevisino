<?php

namespace App\Livewire\Admin\Url;

use App\Models\Category;
use App\Models\Url;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $categories;
//    public Collection $urls;
    public Url $url;

    protected $listeners = ['added' => 'added'];


    public function mount(): void
    {
        $this->added();
    }

    public function added(): void
    {
        $this->categories = \App\Models\Category::query()->whereNotNull('parent_id')->get();
    }

    public function render()
    {
        return view('livewire.admin.url.index')->layout('components.layouts.admin');
    }
}
