<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Product;
use App\Models\Url;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class SiteMap extends Component
{
    public function siteMap()
    {
        $categories = Url::all()->pluck('dashed_url');
        $products   = Product::all()->pluck('dashed_url');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // صفحه اصلی
        $sitemap .= '    <url>' . PHP_EOL;
        $sitemap .= '        <loc>' . url('/') . '</loc>' . PHP_EOL;
        $sitemap .= '        <priority>1.0</priority>' . PHP_EOL;
        $sitemap .= '        <changefreq>daily</changefreq>' . PHP_EOL;
        $sitemap .= '    </url>' . PHP_EOL;

        // صفحه shop
        $sitemap .= '    <url>' . PHP_EOL;
        $sitemap .= '        <loc>' . url('/shop') . '</loc>' . PHP_EOL;
        $sitemap .= '        <priority>0.9</priority>' . PHP_EOL; // می‌تونی مقدارش رو کم یا زیاد کنی
        $sitemap .= '        <changefreq>daily</changefreq>' . PHP_EOL;
        $sitemap .= '    </url>' . PHP_EOL;

        // دسته‌بندی‌ها
        foreach ($categories as $cat) {
            $sitemap .= '    <url>' . PHP_EOL;
            $sitemap .= '        <loc>' . url('/category/' . $cat) . '</loc>' . PHP_EOL;
            $sitemap .= '    </url>' . PHP_EOL;
        }

        // محصولات
        foreach ($products as $product) {
            $sitemap .= '    <url>' . PHP_EOL;
            $sitemap .= '        <loc>' . url('/product/' . $product) . '</loc>' . PHP_EOL;
            $sitemap .= '    </url>' . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        File::put(base_path('../public_html/sitemap.xml'), $sitemap);

        return 'Sitemap created successfully.';
    }


    public function render()
    {
        return view('livewire.admin.setting.site-map');
    }
}
