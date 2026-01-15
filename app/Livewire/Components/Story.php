<?php

namespace App\Livewire\Components;

use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Story extends Component
{
    public $activeStory = null;
    public $currentIndex = 0;

    #[Computed]
    public function stories()
    {
        return Category::query()
            ->with(['products' => function($query) {
                $query->select('id', 'category_id', 'title', 'dashed_url')->take(10);
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
                        'image' => asset("storage/products/{$product->id}/small/1.webp"),
                        'link' => route('product-page', ['title' => $product->dashed_url]),
                        'product_name' => $product->title ?? 'محصول',
                    ])->values()->toArray(),
                ];
            })
            ->values()
            ->toArray();
    }
    public function openStory($storyId)
    {
        $this->activeStory = $storyId;
        $this->currentIndex = 0;
        $this->dispatch('story-index-changed');
    }

    public function nextItem()
    {
        $story = collect($this->stories)->firstWhere('id', $this->activeStory);

        if ($story && $this->currentIndex < count($story['items']) - 1) {
            $this->currentIndex++;
            $this->dispatch('story-index-changed');
        } else {
            $this->closeStory();
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
        $this->currentIndex = 0;
    }

    public function render()
    {
        return view('livewire.components.story');
    }
}
