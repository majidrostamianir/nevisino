<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmallsProductsController extends Controller
{

    public function index(Request $request)
    {
        // دریافت پارامترها مثل ترب
        $page = (int)$request->query('page', 1);
        $itemPerPage = (int)$request->query('item_per_page', 50);

        // محدودیت حداکثر 100 عدد در صفحه (مثل ترب)
        $itemPerPage = min($itemPerPage, 100);

        // دریافت همه محصولات با واریانت‌ها (مثل روش ترب شما)
        $products = Product::with('variants')->get();

        // ساخت لیست آیتم‌ها (همون روش خلاقانه شما)
        $items = collect();

        foreach ($products as $product) {
            // محصول بدون واریانت
            if (is_null($product->variant)) {
                $items->push([
                    'product' => $product,
                    'variant' => null,
                    'stock' => $product->stock ?? 0,
                    'price' => $product->discounted_price ?: $product->price
                ]);
            } // محصول با واریانت
            else {
                // محصول پایه با مجموع موجودی واریانت‌ها
                $totalStock = $product->variants->sum('stock');

                $items->push([
                    'product' => $product,
                    'variant' => null,
                    'stock' => $totalStock,
                    'price' => $product->discounted_price ?: $product->price
                ]);

                // واریانت‌ها
                foreach ($product->variants as $variant) {
                    $items->push([
                        'product' => $product,
                        'variant' => $variant,
                        'stock' => $variant->stock ?? 0,
                        'price' => $variant->discounted_price ?: $variant->price
                    ]);
                }
            }
        }

        // صفحه‌بندی (مثل ترب)
        $total = $items->count();
        $totalPages = ceil($total / $itemPerPage);

        $paginated = $items
            ->slice(($page - 1) * $itemPerPage, $itemPerPage)
            ->values();

        // فرمت پاسخ طبق مستندات ایمالز
        return response()->json([
            'success' => true,
            'products' => $paginated->map(fn($item) => $this->transformForEmalls($item)),
            'total_items' => $total,
            'pages_count' => $totalPages,
            'item_per_page' => $itemPerPage,
            'page_num' => $page,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    private function transformForEmalls($item)
    {
        $product = $item['product'];
        $variant = $item['variant'];
        $stock = $item['stock'];
        $price = $item['price'];

        // قیمت قدیمی برای نمایش تخفیف
        $oldPrice = null;


        // عنوان محصول (مثل ترب)
        $title = $variant
            ? $product->title . ' - ' . ($product->variant . ' ' . $variant->name)
            : $product->title;

        // تصویر محصول (مثل ترب)
        $image = $variant
            ? asset('storage/products/' . $product->id . '/large/' . $variant->id . '.webp')
            : $this->getMainImage($product);

        // لینک محصول (مثل ترب)
        $url = $variant
            ? url("/product/{$product->id}/{$product->dashed_url}?nvi={$variant->id}")
            : url("/product/{$product->id}/{$product->dashed_url}");

        // رنگ (اگه واریانت رنگ داره)
        $color = $variant && isset($variant->color) ? $variant->color : ($product->color ?? null);

        // گارانتی
        $guarantee = $variant && $variant->guarantee
            ? $variant->guarantee
            : ($product->guarantee ?? 'تصاویر اختصاصی از محصول');

        return [
            'id' => $variant ? "{$product->id}_{$variant->id}" : (string)$product->id,
            'title' => $title,
            'price' => (int)$price,
            'price_old' => $oldPrice ? (int)$oldPrice : null,
            'category' => Category::find($product->category_id)?->title ?? 'دسته‌بندی نشده',
            'image' => $image,
            'color' => $color,
            'guarantee' => $guarantee,
            'available_is' => $stock > 0,
            'url' => $url,
        ];
    }

    private function getMainImage($product)
    {
        // مشابه روش ترب برای گرفتن تصویر اصلی
        $path = 'products/' . $product->id . '/large';

        if (Storage::disk('public')->exists($path)) {
            $files = collect(Storage::disk('public')->files($path))
                ->sortBy(fn($file) => intval(pathinfo($file, PATHINFO_FILENAME)))
                ->first();

            if ($files) {
                return asset('storage/' . $files);
            }
        }

        // تصویر پیش‌فرض
        return asset('storage/default-product.jpg');
    }



}
