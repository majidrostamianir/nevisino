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
    public string $title_tag = '', $dashed_url = '';
    public null|string $meta_description = '', $article = '' , $title_h1 = '' , $mini_article = '';
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
            'title_tag' => 'required|string|min:3|' . Rule::unique('urls', 'title_tag')->ignore($this->url),
            'dashed_url' => 'required|string|min:3|' . Rule::unique('urls', 'dashed_url')->ignore($this->url),
            'title_h1' => 'nullable|string|min:3|',
            'meta_description' => 'nullable|string|min:3',
            'article' => 'nullable|string|min:3',
            'mini_article' => 'nullable|string|min:3',
            'categoryId' => 'required|integer|exists:categories,id',
        ];
    }

    public function save(): void
    {
        $this->article = preg_replace('/rel="noopener noreferrer"/', '', $this->article);
        $this->article = preg_replace('/target="_blank"/', 'wire:navigate', $this->article);
        $this->article = preg_replace('/ql-align-justify/', 'text-justify', $this->article);

        $this->mini_article = preg_replace('/rel="noopener noreferrer"/', '', $this->mini_article);
        $this->mini_article = preg_replace('/target="_blank"/', 'wire:navigate', $this->mini_article);
        $this->mini_article = preg_replace('/ql-align-justify/', 'text-justify', $this->mini_article);

        $this->title_tag = trim(preg_replace('/\s+/', ' ', $this->title_tag));
        $this->dashed_url = preg_replace('/\s+/', '-', trim($this->dashed_url));
        $this->title_h1 = trim(preg_replace('/\s+/', ' ', $this->title_h1));
        $this->validate();
        $this->url->title_tag = $this->title_tag;
        $this->url->dashed_url = $this->dashed_url;
        $this->url->title_h1 = $this->title_h1;
        $this->url->meta_description = $this->meta_description;
        $this->url->article = $this->article;
        $this->url->mini_article = $this->mini_article;
        $this->url->category_id = $this->categoryId;
        $this->url->save();
        $this->url = new \App\Models\Url();
        $this->title_tag = '';
        $this->dashed_url = '';
        $this->title_h1 = '';
        $this->meta_description = '';
        $this->article = '';
        $this->mini_article = '';
        $this->categoryId = 0;
    }

    public function setUrl($id): void
    {
        $this->url = \App\Models\Url::query()->find($id);
        $this->title_tag = $this->url->title_tag;
        $this->dashed_url = $this->url->dashed_url;
        $this->title_h1 = $this->url->title_h1;
        $this->meta_description = $this->url->meta_description;
        $this->article = $this->url->article;
        $this->mini_article = $this->url->mini_article;
        $this->categoryId = $this->url->category_id;
        $this->dispatch('articleUpdated', $this->article);
        $this->dispatch('miniArticleUpdated', $this->mini_article);

    }


    public function render()
    {
        $urls = \App\Models\Url::query()->orderBy('in_menu', 'desc')->get();
        $categories = \App\Models\Category::query()->whereNotNull('parent_id')->get();
        return view('livewire.admin.url.index', compact('urls', 'categories'))->layout('components.layouts.admin');
    }
}
