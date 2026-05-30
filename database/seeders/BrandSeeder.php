<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Brand::query()->create([
            'fa_name' => 'متفرقه',
            'en_name' => 'Miscellaneous',
        ]);

        Brand::query()->create([
            'fa_name' => 'آریا',
            'en_name' => 'Arya',
        ]);

        Brand::query()->create([
            'fa_name' => 'کوییلو',
            'en_name' => 'Quilo',
        ]);
        Brand::query()->create([
            'fa_name' => 'فابرکاستل',
            'en_name' => 'Faber Castell',
        ]);
        Brand::query()->create([
            'fa_name' => 'کنکو',
            'en_name' => 'Canco',
        ]);
        Brand::query()->create([
            'fa_name' => 'پارسیکار',
            'en_name' => 'Parsikar',
        ]);
        Brand::query()->create([
            'fa_name' => 'فکتیس',
            'en_name' => 'Factis',
        ]);
        Brand::query()->create([
            'fa_name' => 'دلی',
            'en_name' => 'Deli',
        ]);
        Brand::query()->create([
            'fa_name' => 'استورم',
            'en_name' => 'Storm',
        ]);
    Brand::query()->create([
            'fa_name' => 'جانسون',
            'en_name' => 'Jonson',
        ]);
    Brand::query()->create([
            'fa_name' => 'رازی',
            'en_name' => 'Razi',
        ]);
    Brand::query()->create([
            'fa_name' => 'پنتر',
            'en_name' => 'Panter',
        ]);
    Brand::query()->create([
            'fa_name' => 'سی کلاس',
            'en_name' => 'Cclass',
        ]);
    Brand::query()->create([
            'fa_name' => 'کیان',
            'en_name' => 'Kian',
        ]);
    Brand::query()->create([
            'fa_name' => 'کرونا',
            'en_name' => 'Corona',
        ]);
    Brand::query()->create([
            'fa_name' => 'رولکس',
            'en_name' => 'Rolex',
        ]);
    Brand::query()->create([
            'fa_name' => 'پارس',
            'en_name' => 'Pars',
        ]);
    Brand::query()->create([
            'fa_name' => 'اسکول فنس',
            'en_name' => 'School Fans',
        ]);
    Brand::query()->create([
            'fa_name' => 'الیگیتور',
            'en_name' => 'Aligator',
        ]);
    Brand::query()->create([
            'fa_name' => 'ام کیو',
            'en_name' => 'MQ',
        ]);
    Brand::query()->create([
            'fa_name' => 'ووک',
            'en_name' => 'Woke',
        ]);
    Brand::query()->create([
            'fa_name' => 'اسکول فنس',
            'en_name' => 'Schoolfans',
        ]);
    Brand::query()->create([
            'fa_name' => 'اسکول فنس',
            'en_name' => 'Schoolfans',
        ]);
    }
}
