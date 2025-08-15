<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainMenu = Menu::create([
            'title' => 'نوشت افزار',
            'dashed_title'  => 'نوشت-افزار',
            'order' => 1
        ]);
        Menu::create([
            'title' => 'مداد',
            'dashed_title'  => 'مداد',
            'parent_id' => $mainMenu->id,
            'order' => 1
        ]);
        Menu::create([
            'title' => 'خودکار و روان نویس',
            'dashed_title'  => 'خودکار-روان-نویس',
            'parent_id' => $mainMenu->id,
            'order' => 2
        ]);
        Menu::create([
            'title' => 'پاک کن',
            'dashed_title'  => 'پاک-کن',
            'parent_id' => $mainMenu->id,
            'order' => 3
        ]);
        Menu::create([
            'title' => 'تراش',
            'dashed_title'  => 'تراش',
            'parent_id' => $mainMenu->id,
            'order' => 4
        ]);
        Menu::create([
            'title' => 'اتود و مغزی اتود',
            'dashed_title'  => 'اتود-مغزی',
            'parent_id' => $mainMenu->id,
            'order' => 5
        ]);
        Menu::create([
            'title' => 'لاک غلط گیر',
            'dashed_title'  => 'لاک-غلط-گیر',
            'parent_id' => $mainMenu->id,
            'order' => 6
        ]);

        $mainMenu = Menu::create([
            'title' => 'لوازم نقاشی',
            'dashed_title'  => 'لوازم-نقاشی',
            'order' => 2
        ]);
        Menu::create([
            'title' => 'مداد رنگی',
            'dashed_title'  => 'مداد-رنگی',
            'parent_id' => $mainMenu->id,
            'order' => 1
        ]);
        Menu::create([
            'title' => 'پاستل و مداد شمعی',
            'dashed_title'  => 'پاستل-مداد-شمعی',
            'parent_id' => $mainMenu->id,
            'order' => 2
        ]);
        Menu::create([
            'title' => 'گواش و آبرنگ',
            'dashed_title'  => 'گواش-آبرنگ',
            'parent_id' => $mainMenu->id,
            'order' => 1
        ]);
    }
}
