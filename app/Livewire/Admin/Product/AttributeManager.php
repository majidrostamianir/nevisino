<?php

namespace App\Livewire\Admin\Product;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Livewire\Component;
use Illuminate\Validation\Rule;

class AttributeManager extends Component
{
    public $categoryId = null;
    public $filterCategoryId = null;
    public $attributeName = '';
    public $attributeValue = '';
    public $editingAttributeId = null;
    public $editingValueId = null;
    public $selectedAttributeForValue = null;

    public function saveAttribute()
    {
        $this->validate([
            'categoryId' => 'required|exists:categories,id',
            'attributeName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes', 'name')
                    ->where('category_id', $this->categoryId)
                    ->ignore($this->editingAttributeId)
            ],
        ]);

        Attribute::updateOrCreate(
            ['id' => $this->editingAttributeId],
            [
                'category_id' => $this->categoryId,
                'name' => $this->attributeName,
            ]
        );

        $this->reset(['attributeName', 'editingAttributeId']);
        session()->flash('message', 'ویژگی ذخیره شد.');
    }

    public function saveValue()
    {
        $this->validate([
            'selectedAttributeForValue' => 'required|exists:attributes,id',
            'attributeValue' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attribute_values', 'value')
                    ->where('attribute_id', $this->selectedAttributeForValue)
                    ->ignore($this->editingValueId)
            ],
        ]);

        AttributeValue::updateOrCreate(
            ['id' => $this->editingValueId],
            [
                'attribute_id' => $this->selectedAttributeForValue,
                'value' => $this->attributeValue,
            ]
        );

        $this->reset(['attributeValue', 'editingValueId']);
        session()->flash('message', 'مقدار ذخیره شد.');
    }

    public function editAttribute($id)
    {
        $attribute = Attribute::find($id);
        $this->editingAttributeId = $attribute->id;
        $this->attributeName = $attribute->name;
        $this->categoryId = $attribute->category_id;
    }

    public function editValue($id)
    {
        $value = AttributeValue::find($id);
        $this->editingValueId = $value->id;
        $this->attributeValue = $value->value;
        $this->selectedAttributeForValue = $value->attribute_id;
    }

    public function cancelEditAttribute()
    {
        $this->reset(['attributeName', 'editingAttributeId', 'categoryId']);
    }

    public function cancelEditValue()
    {
        $this->reset(['attributeValue', 'editingValueId', 'selectedAttributeForValue']);
    }

    public function deleteAttribute($id)
    {
        // ابتدا usage_count همه مقادیر این ویژگی را صفر کن
        $attribute = Attribute::find($id);
        foreach ($attribute->values as $value) {
            $value->products()->detach();
            $value->usage_count = 0;
            $value->save();
        }
        $attribute->delete();
        session()->flash('message', 'ویژگی حذف شد.');
    }

    public function deleteValue($id)
    {
        $value = AttributeValue::find($id);
        // جدا کردن از همه محصولات
        $value->products()->detach();
        $value->delete();
        session()->flash('message', 'مقدار حذف شد.');
    }

    public function render()
    {
        $categories = Category::whereNotNull('parent_id')->orderBy('title')->get();

        $allAttributes = Attribute::with(['category', 'values'])
            ->orderBy('name')
            ->get();

        $attributes = Attribute::with(['category', 'values'])
            ->when($this->filterCategoryId, function($q) {
                $q->where('category_id', $this->filterCategoryId);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.product.attribute-manager', compact('categories', 'attributes', 'allAttributes'))
            ->layout('components.layouts.admin');
    }
}