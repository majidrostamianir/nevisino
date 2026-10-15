<?php

namespace App\Livewire\Admin\Basalam;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class CategoryMapping extends Component
{
    /** @var array<int, int|string|null> */
    public array $mappings = [];

    /** @var array<int, int|string|null> */
    public array $unitMappings = [];

    /** @var array<int, string> */
    public array $rowSearch = [];

    public array $unitTypes = [
        6304 => 'عددی',
        6310 => 'عدد',
        6305 => 'کیلوگرم',
        6306 => 'گرم',
        6307 => 'متر',
        6308 => 'سانتی‌متر',
        6311 => 'لیتر',
        6312 => 'میلی‌لیتر',
        6321 => 'کارتن',
        6318 => 'جفت',
        6319 => 'بسته',
        6322 => 'تخته',
        6323 => 'بطری',
        6324 => 'دست',
        6326 => 'شاخه',
        6331 => 'اینچ',
        6332 => 'فوت',
        6392 => 'رول',
    ];

    /** @var array<int, string> */
    public array $basalamCategories = [];

    public function mount(): void
    {
        $cached = Cache::get('basalam_categories_flat');
        $this->basalamCategories = is_array($cached) ? $cached : [];

        if (empty($this->basalamCategories)) {
            session()->flash(
                'warning',
                'لیست دسته‌های باسلام خالی است. دکمه «بروزرسانی» را بزنید.'
            );
        }

        foreach (Category::all() as $category) {
            $this->mappings[$category->id]     = $category->basalam_category_id;
            $this->unitMappings[$category->id] = $category->basalam_unit_type;
        }
    }

    public function save(): void
    {
        foreach ($this->mappings as $categoryId => $basalamId) {
            Category::where('id', $categoryId)->update([
                'basalam_category_id' => $basalamId ?: null,
                'basalam_unit_type'   => $this->unitMappings[$categoryId] ?? null,
            ]);
        }

        session()->flash('success', ' دسته‌بندی‌ها با موفقیت ذخیره شد.');
    }

    public function clearMapping(int $categoryId): void
    {
        $this->mappings[$categoryId] = null;
    }

    public function refreshCategories(): void
    {
        \Artisan::call('basalam:cache-categories');
        $this->basalamCategories = Cache::get('basalam_categories_flat', []);
        session()->flash('success', 'لیست دسته‌های باسلام بروزرسانی شد.');
    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('order')
            ->get();

        return view('livewire.admin.basalam.category-mapping', [
            'categories' => $categories,
        ])->layout('components.layouts.admin');
    }
}