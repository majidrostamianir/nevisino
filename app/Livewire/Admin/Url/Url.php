<?php

namespace App\Livewire\Admin\Url;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Url extends Component
{
    #[Reactive]
    public Collection $urls, $menus;
    public string $title = '';
    public int $menuId = 0;
    public \App\Models\Url $url;

    public function mount(): void
    {
        $this->url = new \App\Models\Url();
        $this->title = '';
    }


    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|' . Rule::unique('urls', 'title')->ignore($this->url),
            'menuId' => 'required|integer|exists:menus,id',
        ];
    }


    public function save(): void
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashedTitle = trim(preg_replace('/\s+/', '-', $this->title));
        $this->validate();
        $this->url->title = $this->title;
        $this->url->dashed_title = $dashedTitle;
        $this->url->menu_id = $this->menuId;
        $this->url->save();
        $this->url = new \App\Models\Url();
        $this->title = '';
        $this->menuId = 0;
        $this->dispatch('added');
    }

    public function setUrl($id): void
    {
        $this->url = \App\Models\Url::query()->find($id);
        $this->title = $this->url->title;
        $this->menuId = $this->url->menu_id;
        $this->dispatch('url-updated', $id);

    }

    public function render()
    {
        return view('livewire.admin.url.url')->layout('components.layouts.admin');
    }
}
