<?php

namespace App\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\Url;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Save extends Component
{
    public Product $product;
    public int $categoryId =0 , $length, $width, $height, $price, $weight ,$inventory;
    public string $title = '', $query;

    public array $urls = [];
    public array $selectedUrls = [];
    public bool $isFocused = false;


    public function focus()
    {
        $this->isFocused = true;
    }

    public function blur()
    {
        $this->isFocused = false;
    }


    public function selectUrl($key)
    {
        if (!in_array($key, $this->selectedUrls)) {
            $this->selectedUrls[$key] = Url::query()->find($key)->title;
        }
        $this->updateAvailableUrls();
        $this->query = '';
    }

    public function removeUrl($urlId)
    {
        unset($this->selectedUrls[$urlId]);
        $this->updateAvailableUrls();
    }

    public function mount($product = null): void
    {
        if ($product) {
            $this->product = $product;
            $this->title = $product->title;
            $this->selectedUrls = $product->urls->pluck('title', 'id')->toArray();
            $this->categoryId = $product->category_id;
            $this->length = $product->length;
            $this->width = $product->width;
            $this->height = $product->height;
            $this->price = $product->price;
            $this->weight = $product->weight;
            $this->inventory = $product->inventory;
//            $this->clearTemporaryFiles();
        } else {
            $this->product = new Product();
            $this->urls = [];
        }
    }
    public function updatedCategoryId(): void
    {
        $this->setUrls();
    }
    public function setUrls(): void
    {
        if ($this->categoryId) {

            $this->urls = array_diff(
                Url::where('category_id', $this->categoryId)
                    ->pluck('title', 'id')
                    ->toArray(),
                $this->selectedUrls
            );
        }
    }
    public function clearTemporaryFiles(): void
    {
        $path = storage_path('app/private/livewire-tmp');
        foreach (\File::files($path) as $file) {
            if (Carbon::createFromTimestamp($file->getMTime())->diffInMinutes(Carbon::now()) > 15) {
                \File::delete($file->getPathname());
            }
        }
    }


    public function updatedQuery()
    {
        $query = '%' . $this->query . '%';
        $this->urls = Url::where('category_id', $this->categoryId)
            ->where('title', 'like', $query)
            ->pluck('title', 'id')
            ->toArray();
    }

    public function search($query): array
    {
        return array_filter($this->urls, function ($item) use ($query) {
            return stripos($item, $query) !== false;
        });
    }

    protected function updateAvailableUrls()
    {
        $this->urls = array_diff(
            Url::where('category_id', $this->categoryId)->pluck('title', 'id')->toArray(),
            $this->selectedUrls
        );
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255|' . Rule::unique('products', 'title')->ignore($this->product->id),
            'selectedUrls' => 'required|array|min:1',
            'categoryId' => 'required',
            'length' => 'required|integer|min:0',
            'width' => 'required|integer|min:0',
            'height' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'inventory' => 'required|integer|min:0',
        ];
    }

    public function save()
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $dashed_title = trim(preg_replace('/\s+/', '-', $this->title));

        $this->validate();
        $this->product->title = $this->title;
        $this->product->dashed_title = $dashed_title;
        $this->product->category_id = $this->categoryId;
        $this->product->length = $this->length;
        $this->product->width = $this->width;
        $this->product->height = $this->height;
        $this->product->weight = $this->weight;
        $this->product->price = $this->price;
        $this->product->inventory = $this->inventory;
        $this->product->save();
        $this->product->urls()->sync(array_keys($this->selectedUrls));
        return $this->redirect(route('admin.product.save',  $this->product->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.product.save')->layout('components.layouts.admin');
    }
}
