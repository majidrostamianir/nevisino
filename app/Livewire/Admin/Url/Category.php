<?php

namespace App\Livewire\Admin\Url;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Category extends Component
{
    #[Reactive]
    public Collection $categories;
    public string $title;
    public \App\Models\Category $category;

    public function mount(): void
    {
        $this->category = new \App\Models\Category();
        $this->title = '';
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|' . Rule::unique('categories', 'title')->ignore($this->category),
        ];
    }

    public function save(): void
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashedTitle = trim(preg_replace('/\s+/', '-', $this->title));
        $this->validate();
        $this->category->title = $this->title;
        $this->category->dashed_title = $dashedTitle;
        $this->category->price = $this->price;
        $this->category->save();
        $this->category = new \App\Models\Category();
        $this->title = '';
        $this->dispatch('added');
    }

    public function setCategory($id): void
    {
        $this->category = \App\Models\Category::query()->find($id);
        $this->title = $this->category->title;
    }

    public function render()
    {
        return view('livewire.admin.url.category')->layout('components.layouts.admin');
    }
}
