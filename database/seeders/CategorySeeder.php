<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {




        $mainCategory = Category::create([
            'title' => 'نوشت افزار',
            'dashed_url' => 'نوشت-افزار',
            'order' => 1
        ]);
        Category::create([
            'title' => 'مداد',
            'dashed_url' => 'مداد',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'خودکار و روان نویس',
            'dashed_url' => 'خودکار-روان-نویس',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'پاک کن',
            'dashed_url' => 'پاک-کن',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        Category::create([
            'title' => 'مداد تراش',
            'dashed_url' => 'مداد-تراش',
            'parent_id' => $mainCategory->id,
            'order' => 4
        ]);
        Category::create([
            'title' => 'اتود و مغزی اتود',
            'dashed_url' => 'اتود-مغزی',
            'parent_id' => $mainCategory->id,
            'order' => 5
        ]);
        Category::create([
            'title' => 'لاک غلط گیر',
            'dashed_url' => 'لاک-غلط-گیر',
            'parent_id' => $mainCategory->id,
            'order' => 6
        ]);
        Category::create([
            'title' => 'خط کش، گونیا، نقاله',
            'dashed_url' => 'خط-کش-گونیا-نقاله',
            'parent_id' => $mainCategory->id,
            'order' => 7
        ]);

        $mainCategory = Category::create([
            'title' => 'لوازم نقاشی و رنگ آمیزی',
            'dashed_url' => 'لوازم-نقاشی-رنگ آمیزی',
            'order' => 2
        ]);
        Category::create([
            'title' => 'مداد رنگی',
            'dashed_url' => 'مداد-رنگی',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'پاستل و مداد شمعی',
            'dashed_url' => 'پاستل-مداد-شمعی',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'گواش و آبرنگ',
            'dashed_url' => 'گواش-آبرنگ',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        Category::create([
            'title' => 'ماژیک',
            'dashed_url' => 'ماژیک',
            'parent_id' => $mainCategory->id,
            'order' => 4
        ]);

        $mainCategory = Category::create([
            'title' => 'لوازم اداری',
            'dashed_url' => 'لوازم-اداری',
            'order' => 3
        ]);
        Category::create([
            'title' => 'چسب',
            'dashed_url' => 'چسب',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'ماشین حساب',
            'dashed_url' => 'ماشین-حساب',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'منگنه',
            'dashed_url' => 'منگنه',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        Category::create([
            'title' => 'قیچی',
            'dashed_url' => 'قیچی',
            'parent_id' => $mainCategory->id,
            'order' => 4
        ]);

        $mainCategory = Category::create([
            'title' => 'دفتر و دفترچه',
            'dashed_url' => 'دفتر-دفترچه',
            'order' => 4
        ]);
        Category::create([
            'title' => 'دفتر مشق',
            'dashed_url' => 'دفتر-مشق',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'دفتر نقاشی',
            'dashed_url' => 'دفتر-نقاشی',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'دفتر یادداشت',
            'dashed_url' => 'دفتر-یادداشت',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        $mainCategory = Category::create([
            'title' => 'هدیه و سرگرمی',
            'dashed_url' => 'هدیه-سرگرمی',
            'order' => 5
        ]);
        Category::create([
            'title' => 'خمیر بازی',
            'dashed_url' => 'خمیر-بازی',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'دستمال مرطوب',
            'dashed_url' => 'دستمال-مرطوب',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
    }
}
