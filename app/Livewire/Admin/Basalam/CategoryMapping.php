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
        // عددی و تکه‌ای
        6304 => 'عددی',
        6310 => 'تکه (اسلایس)',
        6319 => 'بسته',
        6321 => 'کارتن',
        6318 => 'جفت',
        6317 => 'جین',
        6320 => 'توپ',
        6316 => 'طاقه',
        6315 => 'قواره',
        6322 => 'تخته',
        6323 => 'بطری',

        // وزنی
        6305 => 'کیلوگرم',
        6306 => 'گرم',
        6314 => 'انس',
        6309 => 'مثقال',
        6466 => 'قیراط',

        // حجمی
        6311 => 'لیتر',
        6312 => 'میلی‌لیتر',
        6313 => 'سی‌سی',

        // طولی
        6307 => 'متر',
        6308 => 'سانتی‌متر',
        6374 => 'میلی‌متر',
        6375 => 'مترمربع',
        6331 => 'اینچ',
        6332 => 'فوت',

        // شمارشی خاص
        6324 => 'دست',
        6326 => 'شاخه',
        6325 => 'بوته',
        6327 => 'قالب',
        6328 => 'کلاف',
        6329 => 'اصله',
        6330 => 'سیر',
        6438 => 'سوت',
        6392 => 'رول',
        6373 => 'جلد',

        // بسته‌بندی خاص
        7062 => 'سرویس',
        7063 => 'دستگاه',
        7064 => 'بالک',
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