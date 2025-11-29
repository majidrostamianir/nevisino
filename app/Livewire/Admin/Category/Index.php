<?php

namespace App\Livewire\Admin\Category;

use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public \App\Models\Category $category;
    public string $title = '';
    public int|null $parent_id = null;
    public int|null $order = null;

    public function mount(): void
    {
        $this->category = new \App\Models\Category();
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|' . Rule::unique('categories', 'title')->ignore($this->category),
            'parent_id' => 'int|nullable',
            'order' => 'required|int',
        ];
    }

    public function save(): void
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashedTitle = trim(preg_replace('/\s+/', '-', $this->title));
        $this->validate();

        $this->category->title = $this->title;
        $this->category->dashed_url = $dashedTitle;
        $this->category->order = $this->order;
        $this->category->parent_id = $this->parent_id ?: null;

        $this->category->save();
        $this->category = new \App\Models\Category();
        $this->title = '';
        $this->parent_id = null;
        $this->order = null;

        $this->dispatch('added');

    }

    public function changeCategory($id): void
    {
        $this->category = \App\Models\Category::query()->find($id);
        $this->title = $this->category->title;
        $this->parent_id = $this->category->parent_id;
        $this->order = $this->category->order;
    }
    public function render()
    {
        $categories = \App\Models\Category::query()->whereNull('parent_id')
            ->with('children')
            ->orderBy('order')
            ->get();
        return view('livewire.admin.category.index' ,compact('categories'))->layout('components.layouts.admin');
    }
}
