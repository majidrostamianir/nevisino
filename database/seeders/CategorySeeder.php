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
            'dashed_title' => 'نوشت-افزار',
            'order' => 1
        ]);
        Category::create([
            'title' => 'مداد',
            'dashed_title' => 'مداد',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'خودکار و روان نویس',
            'dashed_title' => 'خودکار-روان-نویس',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'پاک کن',
            'dashed_title' => 'پاک-کن',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        Category::create([
            'title' => 'مداد تراش',
            'dashed_title' => 'مداد-تراش',
            'parent_id' => $mainCategory->id,
            'order' => 4
        ]);
        Category::create([
            'title' => 'اتود و مغزی اتود',
            'dashed_title' => 'اتود-مغزی',
            'parent_id' => $mainCategory->id,
            'order' => 5
        ]);
        Category::create([
            'title' => 'لاک غلط گیر',
            'dashed_title' => 'لاک-غلط-گیر',
            'parent_id' => $mainCategory->id,
            'order' => 6
        ]);
        Category::create([
            'title' => 'خط کش، گونیا، نقاله',
            'dashed_title' => 'خط-کش-گونیا-نقاله',
            'parent_id' => $mainCategory->id,
            'order' => 7
        ]);

        $mainCategory = Category::create([
            'title' => 'لوازم نقاشی و رنگ آمیزی',
            'dashed_title' => 'لوازم-نقاشی-رنگ آمیزی',
            'order' => 2
        ]);
        Category::create([
            'title' => 'مداد رنگی',
            'dashed_title' => 'مداد-رنگی',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'پاستل و مداد شمعی',
            'dashed_title' => 'پاستل-مداد-شمعی',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'گواش و آبرنگ',
            'dashed_title' => 'گواش-آبرنگ',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
        Category::create([
            'title' => 'ماژیک',
            'dashed_title' => 'ماژیک',
            'parent_id' => $mainCategory->id,
            'order' => 4
        ]);

        $mainCategory = Category::create([
            'title' => 'لوازم اداری',
            'dashed_title' => 'لوازم-اداری',
            'order' => 3
        ]);
        Category::create([
            'title' => 'چسب',
            'dashed_title' => 'چسب',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'ماشین حساب',
            'dashed_title' => 'ماشین-حساب',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'منگنه',
            'dashed_title' => 'منگنه',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);

        $mainCategory = Category::create([
            'title' => 'دفتر و دفترچه',
            'dashed_title' => 'دفتر-دفترچه',
            'order' => 4
        ]);
        Category::create([
            'title' => 'دفتر مشق',
            'dashed_title' => 'دفتر-مشق',
            'parent_id' => $mainCategory->id,
            'order' => 1
        ]);
        Category::create([
            'title' => 'دفتر نقاشی',
            'dashed_title' => 'دفتر-نقاشی',
            'parent_id' => $mainCategory->id,
            'order' => 2
        ]);
        Category::create([
            'title' => 'دفتر یادداشت',
            'dashed_title' => 'دفتر-یادداشت',
            'parent_id' => $mainCategory->id,
            'order' => 3
        ]);
    }
}
