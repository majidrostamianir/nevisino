<?php

namespace App\Livewire\Admin\Product;

use App\Models\Attribute;
use App\Models\AttributeValue;
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

    public string|null $variant = null, $code = null, $description = null;

    public array $urls = [];
    public array $attrs = [];
    public array $selectedUrls = [];
    public array $selectedAttrs = []; // [attribute_id => attribute_value_id]
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

            // تبدیل ساختار جدید به selectedAttrs
            $this->selectedAttrs = $product->attributes()
                ->withPivot('attribute_value_id')
                ->get()
                ->mapWithKeys(function ($attr) {
                    return [
                        $attr->id => [
                            'attribute_id' => $attr->id,
                            'attribute_name' => $attr->name,
                            'value_id' => $attr->pivot->attribute_value_id,
                            'value' => $attr->values->where('id', $attr->pivot->attribute_value_id)->first()->value ?? '',
                        ]
                    ];
                })->toArray();

            $this->categoryId = $product->category_id;
            $this->size = $product->size;
            $this->price = $product->price;
            $this->discounted_price = $product->discounted_price;
            $this->weight = $product->weight;
            $this->stock = $product->stock;

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

        // همگام سازی ویژگی‌های جدید
        $syncData = [];
        foreach ($this->selectedAttrs as $attributeId => $data) {
            $syncData[$attributeId] = ['attribute_value_id' => $data['value_id']];
        }
        $this->product->attributes()->sync($syncData);

        $keptIds = [];
        foreach ($this->variants as $variant) {
            $v = $this->product->variants()->updateOrCreate(
                ['id' => $variant['id'] ?? null],
                [
                    'name' => $variant['name'],
                    'stock' => $variant['stock'] ?? 0,
                ]
            );
            $keptIds[] = $v->id;
        }

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

    public function selectAttr($attributeId, $valueId, $attributeName, $value)
    {
        if (!array_key_exists($attributeId, $this->selectedAttrs)) {
            $this->selectedAttrs[$attributeId] = [
                'attribute_id' => $attributeId,
                'attribute_name' => $attributeName,
                'value_id' => $valueId,
                'value' => $value,
            ];
        }

        $this->updateAvailableAttrs();
        $this->queryAttr = '';
    }

    public function removeUrl($urlId)
    {
        unset($this->selectedUrls[$urlId]);
        $this->updateAvailableUrls();
    }

    public function removeAttr($attributeId)
    {
        unset($this->selectedAttrs[$attributeId]);
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
        $query = '%' . $this->queryAttr . '%';

        $allAttributes = Attribute::with(['values'])
            ->where('category_id', $this->categoryId)
            ->get();

        $availableAttrs = [];
        foreach ($allAttributes as $attribute) {
            foreach ($attribute->values as $value) {
                // بررسی نشدن در selectedAttrs
                if (!isset($this->selectedAttrs[$attribute->id])) {
                    // جستجو در نام ویژگی یا مقدار
                    $searchText = $this->queryAttr;
                    if (empty($searchText) ||
                        strpos(mb_strtolower($attribute->name), mb_strtolower($searchText)) !== false ||
                        strpos(mb_strtolower($value->value), mb_strtolower($searchText)) !== false) {

                        $availableAttrs[] = [
                            'attribute_id' => $attribute->id,
                            'attribute_name' => $attribute->name,
                            'value_id' => $value->id,
                            'value' => $value->value,
                        ];
                    }
                }
            }
        }

        $this->attrs = $availableAttrs;
    }

    public function render()
    {
        return view('livewire.admin.product.save')->layout('components.layouts.admin');
    }
}