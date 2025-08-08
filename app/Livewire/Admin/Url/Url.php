<?php

namespace App\Livewire\Admin\Url;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Url extends Component
{
    #[Reactive]
    public Collection $urls, $categories;
    public string $title = '';
    public int $categoryId = 0;
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
            'categoryId' => 'required|integer|exists:categories,id',
        ];
    }


    public function save(): void
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashedTitle = trim(preg_replace('/\s+/', '-', $this->title));
        $this->validate();
        $this->url->title = $this->title;
        $this->url->dashed_title = $dashedTitle;
        $this->url->category_id = $this->categoryId;
        $this->url->save();
        $this->url = new \App\Models\Url();
        $this->title = '';
        $this->categoryId = 0;
        $this->dispatch('added');
    }

    public function setUrl($id): void
    {
        $this->url = \App\Models\Url::query()->find($id);
        $this->title = $this->url->title;
        $this->categoryId = $this->url->category_id;
        $this->dispatch('url-updated', $id);

    }

    public function render()
    {
        return view('livewire.admin.url.url')->layout('components.layouts.admin');
    }
}
