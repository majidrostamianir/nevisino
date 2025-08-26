<?php

namespace Database\Seeders;

use App\Models\Url;
use Illuminate\Database\Seeder;

class UrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Url::query()->create([
            'title' => 'نوشت افزار',
            'dashed_title' => 'نوشت-افزار',
            'category_id' => 1,
            'in_menu' => true,
        ]);
        Url::query()->create([
            'title' => 'لوازم نقاشی و رنگ آمیزی',
            'dashed_title' => 'لوازم-نقاشی-رنگ آمیزی',
            'category_id' => 9,
            'in_menu' => true,
        ]);
        Url::query()->create([
            'title' => 'لوازم اداری',
            'dashed_title' => 'لوازم-اداری',
            'category_id' => 14,
            'in_menu' => true,
        ]);
        Url::query()->create([
            'title' => 'دفتر و دفترچه',
            'dashed_title' => 'دفتر-دفترچه',
            'category_id' => 18,
            'in_menu' => true,
        ]);
        Url::query()->create([
            'title' => 'هدیه و فانتزی',
            'dashed_title' => 'هدیه-فانتزی',
            'category_id' => 22,
            'in_menu' => true,
        ]);


        Url::create([
            'title' => 'مداد',
            'dashed_title' => 'مداد',
            'category_id' => 2,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'خودکار و روان نویس',
            'dashed_title' => 'خودکار-روان-نویس',
            'category_id' => 3,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'پاک کن',
            'dashed_title' => 'پاک-کن',
            'category_id' => 4,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'مداد تراش',
            'dashed_title' => 'مداد-تراش',
            'category_id' => 5,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'اتود و مغزی اتود',
            'dashed_title' => 'اتود-مغزی',
            'category_id' => 6,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'لاک غلط گیر',
            'dashed_title' => 'لاک-غلط-گیر',
            'category_id' => 7,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'خط کش، گونیا، نقاله',
            'dashed_title' => 'خط-کش-گونیا-نقاله',
            'category_id' => 8,
            'in_menu' => true,
        ]);


        Url::create([
            'title' => 'مداد رنگی',
            'dashed_title' => 'مداد-رنگی',
            'category_id' => 10,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'پاستل و مداد شمعی',
            'dashed_title' => 'پاستل-مداد-شمعی',
            'category_id' => 11,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'گواش و آبرنگ',
            'dashed_title' => 'گواش-آبرنگ',
            'category_id' => 12,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'ماژیک',
            'dashed_title' => 'ماژیک',
            'category_id' => 13,
            'in_menu' => true,
        ]);


        Url::create([
            'title' => 'چسب',
            'dashed_title' => 'چسب',
            'category_id' => 15,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'ماشین حساب',
            'dashed_title' => 'ماشین-حساب',
            'category_id' => 16,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'منگنه',
            'dashed_title' => 'منگنه',
            'category_id' => 17,
            'in_menu' => true,
        ]);


        Url::create([
            'title' => 'دفتر مشق',
            'dashed_title' => 'دفتر-مشق',
            'category_id' => 19,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'دفتر نقاشی',
            'dashed_title' => 'دفتر-نقاشی',
            'category_id' => 20,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'دفتر یادداشت',
            'dashed_title' => 'دفتر-یادداشت',
            'category_id' => 21,
            'in_menu' => true,
        ]);
        Url::create([
            'title' => 'دستمال مرطوب',
            'dashed_title' => 'دستمال-مرطوب',
            'category_id' => 23,
            'in_menu' => true,
        ]);

    }
}
