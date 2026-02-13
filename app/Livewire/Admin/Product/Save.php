<?php

namespace App\Livewire\Admin\Product;

use App\Models\Category;
use App\Models\Product;
use App\Models\Url;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Save extends Component
{
    public Product $product;
    public int|null $categoryId = null, $stock = null, $discounted_price = null;
    public int $size, $price, $weight;
    public string $title = '', $query = '', $queryAttr = '';

    public string|null $variant = null, $code = null  , $description = null;

    public array $urls = [];
    public array $attrs = [];
    public array $selectedUrls = [];
    public array $selectedAttrs = [];
    public bool $isFocused = false;
    public bool $isFocusedAttr = false;
    public array $variants = [];



    public function mount($product = null): void
    {
        if ($product) {
            $this->product = $product;
            $this->title = $product->title;
            $this->code = $product->code;
            $this->description = $product->description;
            $this->variant = $product->variant;
            $this->selectedUrls = $product->urls->pluck('title_tag', 'id')->toArray();
            $this->selectedAttrs = $product->attrs
                ->mapWithKeys(function ($attr) {
                    return [
                        $attr->id => [
                            'title' => $attr->title,
                            'value' => $attr->value,
                        ]
                    ];
                })
                ->toArray();
            $this->categoryId = $product->category_id;
            $this->size = $product->size;
            $this->price = $product->price;
            $this->discounted_price = $product->discounted_price;
            $this->weight = $product->weight;
            $this->stock = $product->stock;
//            $this->clearTemporaryFiles();


            $this->variants = $product->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->name,
                    'stock' => $v->stock,
                ];
            })->toArray();

            $this->updateAvailableAttrs();

        } else {
            $this->product = new Product();
            $this->urls = [];
            $this->attrs = [];
            $this->size = 0;
        }
    }
    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255|' . Rule::unique('products', 'title')->ignore($this->product->id),
            'variant' => ['nullable', 'string', 'min:2', 'max:255'],
            'variants' => ['nullable', 'array', 'required_with:variant', 'prohibited_if:variant,null|required_with:variant|array'],
            'variants.*.name' => ['required_with:variant', 'string', 'min:2', 'max:255'],
            'variants.*.stock' => ['required_with:variant', 'int', 'min:0'],
            'selectedUrls' => 'required|array|min:1',
            'selectedAttrs' => 'array',
            'categoryId' => 'required',
            'size' => 'nullable|integer|min:0',
            'weight' => 'required|integer|min:0',
            'price' => 'required|integer|min:0',
            'discounted_price' => 'nullable|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'code' => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:1000',
        ];
    }
    public function save()
    {
        $this->title = trim(preg_replace('/\s+/', ' ', $this->title));
        $this->variant = trim(preg_replace('/\s+/', ' ', $this->variant));
        $dashed_url = trim(preg_replace('/\s+/', '-', $this->title));

        if ($this->variant == null || $this->variant == '') {
            $this->variant = null;
        } else {
            $this->stock = null;
        }
        if ($this->discounted_price == null || $this->discounted_price == '') {
            $this->discounted_price = null;
        }


        $this->validate();
        $this->product->title = $this->title;
        $this->product->dashed_url = $dashed_url;
        $this->product->variant = $this->variant;
        $this->product->category_id = $this->categoryId;
        $this->product->size = $this->size;
        $this->product->weight = $this->weight;
        $this->product->price = $this->price;
        $this->product->discounted_price = $this->discounted_price;
        $this->product->stock = $this->stock;
        $this->product->code = $this->code;
        $this->product->description = $this->description;
        $this->product->save();
        $this->product->urls()->sync(array_keys($this->selectedUrls));
        $this->product->attrs()->sync(array_keys($this->selectedAttrs));

        $keptIds = [];
        foreach ($this->variants as $variant) {
            $v = $this->product->variants()->updateOrCreate(
                ['id' => $variant['id'] ?? null],
                [
                    'name' => $variant['name'],
                    'stock' => $variant['stock'] ?? 0,
                ]
            );
            $keptIds[] = $v->id; // آی‌دی‌هایی که باید نگه داشته شوند
        }

// حالا هر چیزی که جزو آی‌دی‌های جدید نیست حذف می‌کنیم
        $this->product->variants()
            ->whereNotIn('id', $keptIds)
            ->delete();

        return $this->redirect(route('admin.product.save', $this->product->id), navigate: true);
    }
    public function addVariant(): void
    {
        $this->variants[] = [
            'id' => null,
            'name' => null,
            'stock' => 0,
        ];
    }

    public function updatedVariant()
    {
        if ($this->variants == []) {
            $this->addVariant();
        }
    }

    public function removeVariant($index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function focus()
    {
        $this->isFocused = true;
    }

    public function blur()
    {
        $this->isFocused = false;
    }

    public function focusAttr()
    {
        $this->isFocusedAttr = true;
    }

    public function blurAttr()
    {
        $this->isFocusedAttr = false;
    }


    public function selectUrl($key)
    {
        if (!in_array($key, $this->selectedUrls)) {
            $this->selectedUrls[$key] = Url::query()->find($key)->title_tag;
        }
        $this->updateAvailableUrls();
        $this->query = '';
    }

    public function selectAttr($key)
    {
        if (!array_key_exists($key, $this->selectedAttrs)) {

            $attr = \App\Models\Attr::query()
                ->select('id', 'title', 'value')
                ->find($key);

            if ($attr) {
                $this->selectedAttrs[$key] = [
                    'title' => $attr->title,
                    'value' => $attr->value,
                ];
            }
        }

        $this->updateAvailableAttrs();
        $this->query = '';
    }


    public function removeUrl($urlId)
    {
        unset($this->selectedUrls[$urlId]);
        $this->updateAvailableUrls();
    }

    public function removeAttr($attrId)
    {
        unset($this->selectedAttrs[$attrId]);
        $this->updateAvailableAttrs();
    }
    public function updatedCategoryId(): void
    {
        $this->setUrls();
        $this->setAttrs();
    }

    public function setUrls(): void
    {
        if ($this->categoryId) {
            $this->updateAvailableUrls();
        }
    }

    public function setAttrs(): void
    {
        if ($this->categoryId) {
            $this->updateAvailableAttrs();
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
        $this->updateAvailableUrls();
    }

    public function updatedQueryAttr()
    {
        $this->updateAvailableAttrs();
    }

    public function search($query): array
    {
        return array_filter($this->urls, function ($item) use ($query) {
            return stripos($item, $query) !== false;
        });
    }

    protected function updateAvailableUrls()
    {
        $parentId = Category::query()->where('id', $this->categoryId)->pluck('parent_id');
        $query = '%' . $this->query . '%';

        $this->urls = array_diff(
            Url::query()
                ->whereIn('category_id', [$this->categoryId, $parentId])
                ->where('title_tag', 'like', $query)
                ->pluck('title_tag', 'id')
                ->toArray(),
            $this->selectedUrls
        );
    }

    protected function updateAvailableAttrs()
    {
        $query = '%' . $this->query . '%';

        $this->attrs = \App\Models\Attr::query()
            ->where('category_id', $this->categoryId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', $query)
                    ->orWhere('value', 'like', $query);
            })
            ->whereNotIn('id', array_keys($this->selectedAttrs))
            ->get(['id', 'title', 'value'])
            ->mapWithKeys(function ($item) {
                return [
                    $item->id => [
                        'title' => $item->title,
                        'value' => $item->value,
                    ]
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.product.save')->layout('components.layouts.admin');
    }
}
