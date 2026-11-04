<?php

namespace App\Livewire\Admin\Url;

use App\Models\Category;
use App\Models\Url;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UrlArticle extends Component
{
    public Url $url;

    public string $title_tag = '';
    public string $dashed_url = '';
    public ?string $meta_description = '';
    public ?string $article = '';
    public ?string $title_h1 = '';
    public ?string $mini_article = '';

    public int $categoryId = 0;


    public function mount(Url $url): void
    {
        $this->url = $url;

        $this->title_tag = $url->title_tag ?? '';
        $this->dashed_url = $url->dashed_url ?? '';
        $this->title_h1 = $url->title_h1 ?? '';
        $this->meta_description = $url->meta_description ?? '';
        $this->article = $url->article ?? '';
        $this->mini_article = $url->mini_article ?? '';
        $this->categoryId = (int) ($url->category_id ?? 0);
    }


    protected function rules(): array
    {
        return [

            'title_tag' => [
                'required',
                'string',
                'min:3',
                Rule::unique('urls', 'title_tag')
                    ->ignore($this->url->id),
            ],

            'dashed_url' => [
                'required',
                'string',
                'min:3',
                Rule::unique('urls', 'dashed_url')
                    ->ignore($this->url->id),
            ],

            'title_h1' => [
                'nullable',
                'string',
                'min:3',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'min:3',
            ],

            'article' => [
                'nullable',
                'string',
            ],

            'mini_article' => [
                'nullable',
                'string',
            ],

            'categoryId' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
        ];
    }


    public function save(): void
    {
        $this->title_tag = trim(
            preg_replace('/\s+/', ' ', $this->title_tag)
        );

        $this->dashed_url = trim(
            preg_replace('/\s+/', '-', $this->dashed_url)
        );

        $this->title_h1 = trim(
            preg_replace('/\s+/', ' ', $this->title_h1 ?? '')
        );

        $this->validate();


        $this->url->title_tag = $this->title_tag;
        $this->url->dashed_url = $this->dashed_url;
        $this->url->title_h1 = $this->title_h1;
        $this->url->meta_description = $this->meta_description;
        $this->url->article = $this->article;
        $this->url->mini_article = $this->mini_article;
        $this->url->category_id = $this->categoryId;

        $this->url->save();


        session()->flash(
            'success',
            'اطلاعات مقاله با موفقیت ذخیره شد.'
        );
    }


    public function render()
    {
        $categories = Category::query()
            ->whereNotNull('parent_id')
            ->orderBy('title')
            ->get();

        return view(
            'livewire.admin.url.url-article',
            compact('categories')
        )->layout('components.layouts.admin');
    }
}