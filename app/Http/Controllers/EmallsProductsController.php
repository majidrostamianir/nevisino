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
        // دریافت پارامترها
        $page = max(1, (int) $request->query('page', 1));
        $itemPerPage = (int) $request->query('item_per_page', 50);

        // اگر مقدار نامعتبر بود، پیش‌فرض 50
        if ($itemPerPage < 1) {
            $itemPerPage = 50;
        }

        // دریافت همه محصولات با واریانت‌ها
        $products = Product::with('variants')->get();

        // ساخت لیست آیتم‌ها
        $items = collect();

        foreach ($products as $product) {
            // محصول بدون واریانت
            if ($product->variants->isEmpty()) {
                $items->push([
                    'product' => $product,
                    'variant' => null,
                    'stock'   => $product->stock ?? 0,
                    'price'   => $product->discounted_price ?: $product->price,
                    'old_price' => ($product->discounted_price && $product->discounted_price < $product->price)
                        ? $product->price
                        : null,
                ]);
            } else {
                // محصول پایه با مجموع موجودی واریانت‌ها
                $totalStock = $product->variants->sum('stock');
                $minVariantPrice = $product->variants->min(fn($v) => $v->discounted_price ?: $v->price);
                $minVariantOldPrice = $product->variants
                    ->filter(fn($v) => $v->discounted_price && $v->discounted_price < $v->price)
                    ->min('price');

                $items->push([
                    'product'    => $product,
                    'variant'    => null,
                    'stock'      => $totalStock,
                    'price'      => $minVariantPrice ?: ($product->discounted_price ?: $product->price),
                    'old_price'  => $minVariantOldPrice ?: null,
                ]);

                // واریانت‌ها
                foreach ($product->variants as $variant) {
                    $items->push([
                        'product'    => $product,
                        'variant'    => $variant,
                        'stock'      => $variant->stock ?? 0,
                        'price'      => $variant->discounted_price ?: $variant->price,
                        'old_price'  => ($variant->discounted_price && $variant->discounted_price < $variant->price)
                            ? $variant->price
                            : null,
                    ]);
                }
            }
        }

        // صفحه‌بندی
        $total = $items->count();
        $totalPages = (int) ceil($total / $itemPerPage);

        $paginated = $items
            ->slice(($page - 1) * $itemPerPage, $itemPerPage)
            ->values();

        // فرمت پاسخ طبق مستندات ایمالز
        return response()->json([
            'success'       => true,
            'products'      => $paginated->map(fn($item) => $this->transformForEmalls($item)),
            'total_items'   => $total,
            'pages_count'   => $totalPages,
            'item_per_page' => $itemPerPage,
            'page_num'      => $page,
        ], 200, [
            'Content-Type' => 'application/json; charset=utf-8',
        ], JSON_UNESCAPED_UNICODE);
    }

    private function transformForEmalls($item)
    {
        $product = $item['product'];
        $variant = $item['variant'];
        $stock   = $item['stock'];
        $price   = $item['price'];
        $oldPrice = $item['old_price'];

        // عنوان محصول
        $title = $variant
            ? $product->title . ' - ' . $variant->name
            : $product->title;

        // تصویر محصول
        $image = $variant
            ? asset('storage/products/' . $product->id . '/large/' . $variant->id . '.webp')
            : $this->getMainImage($product);

        // لینک محصول
        $url = $variant
            ? url("/product/{$product->id}/{$product->dashed_url}?nvi={$variant->id}")
            : url("/product/{$product->id}/{$product->dashed_url}");

        // رنگ
        $color = ($variant && !empty($variant->color))
            ? $variant->color
            : ($product->color ?? null);

        // گارانتی
        $guarantee = ($variant && !empty($variant->guarantee))
            ? $variant->guarantee
            : ($product->guarantee ?? null);

        return [
            'id'           => $variant ? "{$product->id}_{$variant->id}" : (string) $product->id,
            'title'        => $title,
            'price'        => (int) $price,
            'old_price'    => $oldPrice ? (int) $oldPrice : null,
            'category'     => Category::find($product->category_id)?->title ?? 'دسته‌بندی نشده',
            'image'        => $image,
            'color'        => $color,
            'guarantee'    => $guarantee,
            'is_available' => $stock > 0,
            'url'          => $url,
        ];
    }

    private function getMainImage($product)
    {
        $path = 'products/' . $product->id . '/large';

        if (Storage::disk('public')->exists($path)) {
            $file = collect(Storage::disk('public')->files($path))
                ->sortBy(fn($f) => intval(pathinfo($f, PATHINFO_FILENAME)))
                ->first();

            if ($file) {
                return asset('storage/' . $file);
            }
        }

        return asset('storage/default-product.jpg');
    }
}