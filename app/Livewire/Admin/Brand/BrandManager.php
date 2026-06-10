<?php

namespace App\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;
use Illuminate\Support\Str;

class BrandManager extends Component
{
    public $brands;
    public $name, $slug, $logo, $website, $description, $order, $status = true;
    public $editingId = null;
    public $searchQuery = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $this->editingId,
            'slug' => 'required|string|max:255|unique:brands,slug,' . $this->editingId,
            'logo' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'boolean',
        ];
    }

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function save()
    {
        $this->validate();

        Brand::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'logo' => $this->logo,
                'website' => $this->website,
                'description' => $this->description,
                'order' => $this->order ?? 0,
                'status' => $this->status,
            ]
        );

        $this->reset(['name', 'slug', 'logo', 'website', 'description', 'order', 'status', 'editingId']);
        session()->flash('message', 'برند ذخیره شد.');
        $this->loadBrands();
    }

    public function edit($id)
    {
        $brand = Brand::find($id);
        $this->editingId = $brand->id;
        $this->name = $brand->name;
        $this->slug = $brand->slug;
        $this->logo = $brand->logo;
        $this->website = $brand->website;
        $this->description = $brand->description;
        $this->order = $brand->order;
        $this->status = $brand->status;
    }

    public function delete($id)
    {
        Brand::find($id)->delete();
        session()->flash('message', 'برند حذف شد.');
        $this->loadBrands();
    }

    public function toggleStatus($id)
    {
        $brand = Brand::find($id);
        $brand->status = !$brand->status;
        $brand->save();
        $this->loadBrands();
    }

    public function loadBrands()
    {
        $this->brands = Brand::when($this->searchQuery, function($q) {
            $q->where('name', 'like', '%' . $this->searchQuery . '%');
        })->orderBy('order')->get();
    }

    public function mount()
    {
        $this->loadBrands();
    }

    public function render()
    {
        return view('livewire.admin.brand.brand-manager')->layout('components.layouts.admin');
    }
}