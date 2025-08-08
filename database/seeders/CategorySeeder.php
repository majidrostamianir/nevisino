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
        Category::create(['title' => 'مداد', 'parent_id' => 1 , 'dashed_title' => 'مداد']);
        Category::create(['title' => 'خودکار', 'parent_id' => 1 , 'dashed_title' => 'خودکار']);
        Category::create(['title' => 'روان نویس', 'parent_id' => 1 , 'dashed_title' => 'روان-نویس']);
        Category::create(['title' => 'پاک کن', 'parent_id' => 1 , 'dashed_title' => 'پاک-کن']);
        Category::create(['title' => 'تراش', 'parent_id' => 1 , 'dashed_title' => 'تراش']);

        Category::create(['title' => 'مداد رنگی', 'parent_id' => 2 , 'dashed_title' => 'مداد-رنگی']);
        Category::create(['title' => 'مداد شمعی', 'parent_id' => 2 , 'dashed_title' => 'مداد-شمعی']);
        Category::create(['title' => 'گواش آبرنگ', 'parent_id' => 2 , 'dashed_title' => 'گواش-آبرنگ']);

        Category::create(['title' => 'چسب', 'parent_id' => 3 , 'dashed_title' => 'چسب']);
        Category::create(['title' => 'منگنه', 'parent_id' => 3 , 'dashed_title' => 'منگنه']);
        Category::create(['title' => 'ماشین حساب', 'parent_id' => 3 , 'dashed_title' => 'ماشین-حساب']);

        Category::create(['title' => 'کیف و کوله', 'parent_id' => 4 , 'dashed_title' => 'کیف-کوله']);

    }
}
