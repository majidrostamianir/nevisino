<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BasalamCacheCategories extends Command
{
    protected $signature   = 'basalam:cache-categories';
    protected $description = 'دریافت و کش کردن دسته‌بندی‌های باسلام (فقط leafها)';

    public function handle(): int
    {
        $this->info('⏳ دریافت دسته‌بندی‌ها از باسلام...');

        $response = Http::withToken(config('basalam.access_token'))
            ->acceptJson()
            ->get('https://openapi.basalam.com/v1/categories');

        if ($response->failed()) {
            $this->error('❌ خطا در دریافت: ' . $response->body());
            return self::FAILURE;
        }

        $tree = $response->json()['data'] ?? [];

        if (empty($tree)) {
            $this->error('❌ لیست دسته‌ها خالی برگشت.');
            return self::FAILURE;
        }

        $flat = [];
        $this->flatten($tree, $flat);

        Cache::forever('basalam_categories_flat', $flat);

        $this->newLine();
        $this->info('✅ تعداد ' . count($flat) . ' دسته (leaf) در کش ذخیره شد.');
        $this->info('کلید کش: basalam_categories_flat');

        return self::SUCCESS;
    }

    /**
     * بازگشتی: از درخت باسلام، لیست مسطح leafها را می‌سازد
     *
     * @param  array  $nodes  گره‌های درخت
     * @param  array  $flat   آرایه خروجی [id => "مسیر کامل"]
     * @param  string $prefix پیشوند مسیر (والدها)
     */
    protected function flatten(array $nodes, array &$flat, string $prefix = ''): void
    {
        foreach ($nodes as $node) {
            $id       = $node['id'] ?? null;
            $title    = $node['title'] ?? '';
            $children = $node['children'] ?? [];

            if (!$id) {
                continue;
            }

            $fullTitle = $prefix === '' ? $title : $prefix . ' › ' . $title;

            if (empty($children)) {
                // leaf node
                $flat[$id] = $fullTitle;
            } else {
                $this->flatten($children, $flat, $fullTitle);
            }
        }
    }
}