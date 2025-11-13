<?php

namespace App\Livewire\Admin\Url;

use App\Models\Category;
use App\Models\Url;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Index extends Component
{
    public \App\Models\Url $url;
    public string $title = '', $dashed_title = '';
    public null|string $description = '', $article = '';
    public int $categoryId = 0;

    public function mount(): void
    {
        $this->url = new \App\Models\Url();
    }

    public function toggleIndexing($id)
    {
        $url = Url::query()->find($id);
        $url->update([
            'indexing' => !$url->indexing,
        ]);
    }

    public function toggleFollowing($id)
    {
        $url = Url::query()->find($id);
        $url->update([
            'following' => !$url->following,
        ]);
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|' . Rule::unique('urls', 'title')->ignore($this->url),
            'dashed_title' => 'required|string|min:3|' . Rule::unique('urls', 'dashed_title')->ignore($this->url),
            'description' => 'nullable|string|min:3',
            'article' => 'nullable|string|min:3',
            'categoryId' => 'required|integer|exists:categories,id',
        ];
    }

    public function save(): void
    {
        $this->article = preg_replace('/rel="noopener noreferrer"/', '', $this->article);
        $this->article = preg_replace('/target="_blank"/', 'wire:navigate', $this->article);
        $this->article = preg_replace('/ql-align-justify/', 'text-justify', $this->article);

        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $this->dashed_title = preg_replace('/\s+/', '-', trim($this->dashed_title));
        $this->validate();
        $this->url->title = $this->title;
        $this->url->dashed_title = $this->dashed_title;
        $this->url->description = $this->description;
        $this->url->article = $this->article;
        $this->url->category_id = $this->categoryId;
        $this->url->save();
        $this->url = new \App\Models\Url();
        $this->title = '';
        $this->dashed_title = '';
        $this->description = '';
        $this->article = '';
        $this->categoryId = 0;
    }

    public function setUrl($id): void
    {
        $this->url = \App\Models\Url::query()->find($id);
        $this->title = $this->url->title;
        $this->dashed_title = $this->url->dashed_title;
        $this->description = $this->url->description;
        $this->article = $this->url->article;
        $this->categoryId = $this->url->category_id;
        $this->dispatch('articleUpdated', $this->article);

    }


    public function render()
    {
        $urls = \App\Models\Url::query()->orderBy('in_menu', 'desc')->get();
        $categories = \App\Models\Category::query()->whereNotNull('parent_id')->get();
        return view('livewire.admin.url.index', compact('urls', 'categories'))->layout('components.layouts.admin');
    }
}
