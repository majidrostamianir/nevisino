<?php

namespace App\Livewire\Admin\Url;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Menu extends Component
{
    public Collection $menus;
    public string $title;
    public int $parent_id;
    public int $order;
    public \App\Models\Menu $menu;

    public function mount(): void
    {
        $this->menus = \App\Models\Menu::query()->whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
        $this->menu = new \App\Models\Menu();
        $this->title = '';
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|' . Rule::unique('menus', 'title')->ignore($this->menu),
            'parent_id' => 'int|nullable',
        ];
    }

    public function save(): void
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashedTitle = trim(preg_replace('/\s+/', '-', $this->title));
        $this->validate();
        $this->menu->title = $this->title;
        $this->menu->dashed_title = $dashedTitle;
        $this->menu->order = $this->order;
        if ($this->parent_id) {
            $this->menu->parent_id = $this->parent_id;
        }
        $this->menu->save();
        $this->menu = new \App\Models\Menu();
        $this->title = '';
        $this->parent_id = 0;
    }

    public function changeChild($id): void
    {
        $this->menu = \App\Models\Menu::query()->find($id);
        $this->title = $this->menu->title;
        $this->parent_id = $this->menu->parent_id;
        $this->order = $this->menu->order;

    }

    public function changeParent($id): void
    {
        $this->menu = \App\Models\Menu::query()->find($id);
        $this->title = $this->menu->title;
        $this->parent_id = 0;
        $this->order = $this->menu->order;
    }
    public function render()
    {
        return view('livewire.admin.url.menu')->layout('components.layouts.admin');
    }
}
