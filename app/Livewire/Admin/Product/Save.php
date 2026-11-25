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
use Illuminate\Support\Facades\Session;

class Save extends Component
{
    public Product $product;
    public int $size, $weight;
    public int|null $categoryId = null, $stock = null, $brandId = null;

    public array $prices = [
        'price' => null,
        'price_previous' => null,
        'bulk_price' => null,
        'bulk_price_previous' => null,
        'installment_price' => null,
        'installment_price_previous' => null,
        'discounted_price' => null,
        'discounted_price_previous' => null,
        'discounted_installment_price' => null,
        'discounted_installment_price_previous' => null,
    ];
    public $profitPercent = 20;
    public $gatewayFeePercent = 13;

    public $price_for_show = 0;
    public $installment_price_for_show = 0;

    public array $price_updates = [
        'price_updated_at' => null,
        'bulk_price_updated_at' => null,
        'installment_price_updated_at' => null,
        'discounted_price_updated_at' => null,
        'discounted_installment_price_updated_at' => null,
    ];

    public string $title = '', $query = '', $queryAttr = '';
    public string|null $variant = null, $code = null, $description = null, $torob_url = null;

    public array $urls = [], $attrs = [], $selectedUrls = [], $selectedAttrs = [], $variants = [];
    public bool $isFocused = false, $isFocusedAttr = false;

    public function mount($product = null): void
    {
        $this->profitPercent = Session::get('profit_percent', 20);
        $this->gatewayFeePercent = Session::get('gateway_fee_percent', 13);

        if ($product) {
            $this->brandId = $product->brand_id;
            $this->product = $product;
            $this->title = $product->title;
            $this->code = $product->code;
            $this->description = $product->description;
            $this->variant = $product->variant;
            $this->torob_url = $product->torob_url ?? null;
            $this->selectedUrls = $product->urls->pluck('title_tag', 'id')->toArray();

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
            $this->weight = $product->weight;
            $this->stock = $product->stock;

            $this->fillPricesFromModel();
            $this->calculatePriceFromBulk();
            $this->variants = $product->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'name' => $v->name,
                    'stock' => $v->stock,
                    'torob_url' => $v->torob_url,
                ];
            })->toArray();

            $this->updateAvailableAttrs();

        } else {
            $this->product = new Product();
            $this->urls = [];
            $this->attrs = [];
            $this->size = 0;
            $this->weight = 0;

            $this->prices = array_fill_keys(array_keys($this->prices), null);
            $this->price_updates = array_fill_keys(array_keys($this->price_updates), null);
        }
    }

    private function fillPricesFromModel(): void
    {
        $priceFields = [
            'price', 'price_previous', 'bulk_price', 'bulk_price_previous',
            'installment_price', 'installment_price_previous',
            'discounted_price', 'discounted_price_previous',
            'discounted_installment_price', 'discounted_installment_price_previous'
        ];

        foreach ($priceFields as $field) {
            $this->prices[$field] = $this->product->{$field};
        }

        $updateFields = [
            'price_updated_at', 'bulk_price_updated_at', 'installment_price_updated_at',
            'discounted_price_updated_at', 'discounted_installment_price_updated_at'
        ];

        foreach ($updateFields as $field) {
            $this->price_updates[$field] = $this->product->{$field};
        }
    }

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|min:3|max:255|' . Rule::unique('products', 'title')->ignore($this->product->id),
            'variant' => ['nullable', 'string', 'min:2', 'max:255'],
            'variants' => ['nullable', 'array', 'required_with:variant', 'prohibited_if:variant,null|required_with:variant|array'],
            'variants.*.name' => ['required_with:variant', 'string', 'min:2', 'max:255'],
            'variants.*.stock' => ['required_with:variant', 'int', 'min:0'],
            'variants.*.torob_url' => ['nullable', 'url', 'max:2000'],
            'selectedUrls' => 'required|array|min:1',
            'selectedAttrs' => 'array',
            'categoryId' => 'required',
            'size' => 'nullable|integer|min:0',
            'weight' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'code' => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:5000',
            'brandId' => 'required|exists:brands,id',
            'torob_url' => 'nullable|url|max:255',
        ];

        $priceFields = [
            'prices.price', 'prices.price_previous', 'prices.bulk_price',
            'prices.bulk_price_previous', 'prices.installment_price',
            'prices.installment_price_previous', 'prices.discounted_price',
            'prices.discounted_price_previous', 'prices.discounted_installment_price',
            'prices.discounted_installment_price_previous'
        ];

        foreach ($priceFields as $field) {
            $rules[$field] = 'nullable|integer|min:0';
        }

        $updateFields = [
            'price_updates.price_updated_at', 'price_updates.bulk_price_updated_at',
            'price_updates.installment_price_updated_at', 'price_updates.discounted_price_updated_at',
            'price_updates.discounted_installment_price_updated_at'
        ];

        foreach ($updateFields as $field) {
            $rules[$field] = 'nullable|date';
        }

        return $rules;
    }

    public function updatedPrices($value, $key): void
    {
        if ($key === 'bulk_price') {
            $this->calculatePriceFromBulk();
        }
    }

    public function updatedProfitPercent($value): void
    {
        Session::put('profit_percent', (int) $value);
        $this->profitPercent = Session::get('profit_percent', 20);

        $this->calculatePriceFromBulk();
    }

    public function updatedGatewayFeePercent($value): void
    {
        Session::put('gateway_fee_percent', (float) $value);
        $this->gatewayFeePercent = Session::get('gateway_fee_percent', 13);

        $this->calculatePriceFromBulk();
    }

    public function calculatePriceFromBulk(): void
    {
        $bulk = $this->prices['bulk_price'] ?? 0;

        $bulk = (int) str_replace(
            [',', '،', ' ', '_'],
            '',
            $bulk
        );

        if ($bulk <= 0) {
            $this->price_for_show = 0;
            $this->installment_price_for_show = 0;
            return;
        }

        $cashPrice = $bulk * (1 + ($this->profitPercent / 100));

        $this->price_for_show = (int) $cashPrice;

        if ($this->gatewayFeePercent >= 100 || $this->gatewayFeePercent <= 0) {
            $this->installment_price_for_show = 0;
        } else {
            $this->installment_price_for_show = (int) round(
                $cashPrice / (1 - ($this->gatewayFeePercent / 100))
            );
        }
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

        $this->updatePriceHistory();

        $this->validate();

        $this->fillModelFromPrices();

        $this->product->title = $this->title;
        $this->product->dashed_url = $dashed_url;
        $this->product->variant = $this->variant;
        $this->product->category_id = $this->categoryId;
        $this->product->brand_id = $this->brandId;
        $this->product->size = $this->size;
        $this->product->weight = $this->weight;
        $this->product->stock = $this->stock;
        $this->product->code = $this->code;
        $this->product->description = $this->description;
        $this->product->torob_url = $this->torob_url;
        $this->product->save();

        $this->product->urls()->sync(array_keys($this->selectedUrls));

        $oldValueIds = $this->product->attributeValues()->pluck('attribute_value_id')->toArray();

        $syncData = [];
        $newValueIds = [];
        foreach ($this->selectedAttrs as $attributeId => $data) {
            $syncData[$attributeId] = ['attribute_value_id' => $data['value_id']];
            $newValueIds[] = $data['value_id'];
        }
        $this->product->attributes()->sync($syncData);

        $removedValueIds = array_diff($oldValueIds, $newValueIds);
        foreach ($removedValueIds as $valueId) {
            AttributeValue::find($valueId)?->decrement('usage_count');
        }

        $addedValueIds = array_diff($newValueIds, $oldValueIds);
        foreach ($addedValueIds as $valueId) {
            AttributeValue::find($valueId)?->increment('usage_count');
        }

        $keptIds = [];
        foreach ($this->variants as $variant) {
            // ⬇️ تغییر: اضافه شد torob_url
            $v = $this->product->variants()->updateOrCreate(
                ['id' => $variant['id'] ?? null],
                [
                    'name' => $variant['name'],
                    'stock' => $variant['stock'] ?? 0,
                    'torob_url' => $variant['torob_url'] ?? null,
                ]
            );
            $keptIds[] = $v->id;
        }

        $this->product->variants()
            ->whereNotIn('id', $keptIds)
            ->delete();

        app(\App\Services\ProductService::class)->syncToBasalam($this->product->fresh());

        return $this->redirect(route('admin.product.save', $this->product->id), navigate: true);
    }

    private function updatePriceHistory(): void
    {
        $priceMappings = [
            'price' => 'price_updated_at',
            'bulk_price' => 'bulk_price_updated_at',
            'installment_price' => 'installment_price_updated_at',
            'discounted_price' => 'discounted_price_updated_at',
            'discounted_installment_price' => 'discounted_installment_price_updated_at',
        ];

        foreach ($priceMappings as $priceField => $updateField) {
            $currentValue = $this->product->{$priceField} ?? null;
            $newValue = $this->prices[$priceField] ?? null;

            if ($newValue != $currentValue) {
                $previousField = $priceField . '_previous';
                if (isset($this->prices[$previousField])) {
                    $this->prices[$previousField] = $currentValue;
                }

                $this->price_updates[$updateField] = now();
            }
        }
    }

    private function fillModelFromPrices(): void
    {
        foreach ($this->prices as $field => $value) {
            $this->product->{$field} = $value;
        }

        foreach ($this->price_updates as $field => $value) {
            $this->product->{$field} = $value;
        }
    }

    public function addVariant(): void
    {
        // ⬇️ تغییر: اضافه شد torob_url
        $this->variants[] = [
            'id' => null,
            'name' => null,
            'stock' => 0,
            'torob_url' => null,
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
                if (!isset($this->selectedAttrs[$attribute->id])) {
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