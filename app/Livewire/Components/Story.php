<?php

namespace App\Livewire\Components;

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Story extends Component
{
    public $activeStory = null;
    public $activeData = null;

    public $currentIndex = 0;

    #[Computed]
    public function stories()
    {
        return cache()->remember('home_stories', 900, function () {
            return Category::query()->has('products')
                ->with(['products' => function($query) {
                    $query->select('id', 'category_id', 'title', 'dashed_url')
                        ->take(10);
                }])
                ->get()
                ->filter(fn($category) => $category->products->isNotEmpty())
                ->map(function ($category) {
                    $firstProduct = $category->products->first();
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                        'cover' => asset("storage/products/{$firstProduct->id}/small/1.webp"),
                        'items' => $category->products->map(fn($product) => [
                            'image' =>  $product->story_image,
                            'link' => route('product-page', ['title' => $product->dashed_url]),
                            'product_name' => $product->title ?? 'محصول',
                        ])->values()->toArray(),
                    ];
                })
                ->values()
                ->toArray();
        });
    }
    public function openStory($storyId)
    {
        $this->activeStory = $storyId;
        $this->currentIndex = 0;
        $this->activeData = collect($this->stories)
            ->firstWhere('id', $storyId);

        $this->dispatch('story-index-changed');
    }

    public function nextItem()
    {
        if ($this->activeData && $this->currentIndex < count($this->activeData['items']) - 1) {
            $this->currentIndex++;
            $this->dispatch('story-index-changed');
        } else {
            $this->goToNextStory();
        }
    }



    public function prevItem()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->dispatch('story-index-changed');
        }
    }

    public function closeStory()
    {
        $this->activeStory = null;
        $this->activeData = null;
        $this->currentIndex = 0;
    }
    public function goToNextStory()
    {
        $stories = $this->stories;

        $currentIndex = collect($stories)
            ->search(fn($story) => $story['id'] === $this->activeStory);

        if ($currentIndex !== false && isset($stories[$currentIndex + 1])) {
            $nextStory = $stories[$currentIndex + 1];

            $this->activeStory = $nextStory['id'];
            $this->activeData = $nextStory;
            $this->currentIndex = 0;

            $this->dispatch('story-index-changed');
        } else {
            $this->closeStory();
        }
    }


    public function render()
    {
        return view('livewire.components.story');
    }
}
