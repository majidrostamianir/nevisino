<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Storage;

class TorobController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->verifyToken($request);

        $body = $request->all();

        if (empty($body)) {
            return response()->json(['error' => 'empty body'], 400);
        }

        if (isset($body['page']) && !isset($body['sort'])) {
            return response()->json(['error' => 'sort parameter is not provided'], 400);
        }

        if (isset($body['page'])) {
            return $this->handlePagination($body);
        }

        if (isset($body['page_uniques'])) {
            return $this->handleByUniques($body['page_uniques']);
        }

        if (isset($body['page_urls'])) {
            return $this->handleByUrls($body['page_urls']);
        }

        return response()->json(['error' => 'invalid parameters'], 400);
    }

    // =============================

    private function handlePagination($body)
    {
        $page = (int)$body['page'];
        $perPage = 100;

        $query = Product::with('variants');

        if ($body['sort'] === 'date_added_desc') {
            $query->orderByDesc('created_at');
        } elseif ($body['sort'] === 'date_updated_desc') {
            $query->orderByDesc('updated_at');
        } else {
            return response()->json(['error' => 'invalid sort'], 400);
        }

        $allProducts = $query->get();

        $items = collect();

        foreach ($allProducts as $product) {

            // بدون واریانت
            if (!$product->variant) {
                $items->push([
                    'product' => $product,
                    'variant' => null
                ]);
            } // با واریانت
            else {
                foreach ($product->variants as $variant) {
                    $items->push([
                        'product' => $product,
                        'variant' => $variant
                    ]);
                }
            }
        }

        $total = $items->count();

        $paginated = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return $this->response($paginated, $page, $total);
    }

    // =============================

    private function handleByUniques($uniques)
    {
        $products = Product::with('variants')->get();

        $items = collect();

        foreach ($products as $product) {

            if (!$product->variant) {

                $unique = $this->makeUnique($product, null);

                if (in_array($unique, $uniques)) {
                    $items->push([
                        'product' => $product,
                        'variant' => null
                    ]);
                }

            } else {

                foreach ($product->variants as $variant) {

                    $unique = $this->makeUnique($product, $variant);

                    if (in_array($unique, $uniques)) {
                        $items->push([
                            'product' => $product,
                            'variant' => $variant
                        ]);
                    }
                }
            }
        }

        return $this->response($items->values(), 1, $items->count());
    }

    // =============================

    private function handleByUrls($urls)
    {
        $products = Product::with('variants')->get();

        $items = collect();

        foreach ($products as $product) {

            if (!$product->variant) {

                $url = $this->makeUrl($product, null);

                if (in_array($url, $urls)) {
                    $items->push([
                        'product' => $product,
                        'variant' => null
                    ]);
                }

            } else {

                foreach ($product->variants as $variant) {

                    $url = $this->makeUrl($product, $variant);

                    if (in_array($url, $urls)) {
                        $items->push([
                            'product' => $product,
                            'variant' => $variant
                        ]);
                    }
                }
            }
        }

        return $this->response($items->values(), 1, $items->count());
    }

    // =============================

    private function response($items, $page, $total)
    {
        return response()->json([
            'api_version' => 'torob_api_v3',
            'current_page' => $page,
            'total' => $total,
            'max_pages' => max(1, ceil($total / 100)),
            'products' => $items->map(function ($item) {
                return $this->transform($item['product'], $item['variant']);
            })
        ]);
    }

    // =============================

    private function transform($p, $v = null)
    {
        if ($v === null) {
            $path = 'products/' . $p->id . '/large';
            $files = collect(Storage::disk('public')->files($path))
                ->sortBy(fn($file) => intval(pathinfo($file, PATHINFO_FILENAME)))
                ->values();

            $image_address = $files->map(function ($file) {
                return asset('storage/' . $file);
            })->values()->toArray();

            $title = $p->title;
        } else {
            $image_address = [asset('storage/products/' . $p->id . '/large/' . $v->id . '.webp')];
            $title = $p->title . ' - ' . $p->variant . ' ' . $v->name;
        }
        return [
            'page_unique' => $this->makeUnique($p, $v),

            'page_url' => $this->makeUrl($p, $v),

            'product_group_id' => (string)($p->id),

            'title' => $title,

            'subtitle' => null,

            'current_price' => (int)($p->discounted_price ?: $p->price),

            'old_price' => null,

            'availability' => $v
                ? ($v->stock > 0)
                : (($p->stock ?? 0) > 0),

            'category_name' => Category::query()->find($p->category_id)->title,

            'image_links' => $image_address,

            'spec' => array_filter(array_merge(
                $p->variant && $v?->name
                    ? [$p->variant => $v->name]
                    : [],

                $p->attrs->mapWithKeys(function ($attr) {
                    return [$attr->title => $attr->value];
                })->toArray(),

                [
                    'وزن' => ceil($p->weight / 5) * 5 . ' گرم',
                ]
            )),

            'guarantee' => 'تصاویر اختصاصی از محصول',

            'short_desc' => $p->description,

            'date_added' => $this->formatDate($p->created_at),

            'date_updated' => $this->formatDate($p->updated_at),
        ];
    }

    // =============================

    private function makeUnique($p, $v = null)
    {
        if ($v) {
            return $p->id . '_' . $v->id;
        }

        return (string)$p->id;
    }

    private function makeUrl($p, $v = null)
    {
        $base = url("/product/{$p->id}/{$p->dashed_url}");

        if ($v) {
            return $base . '?nvi=' . $v->id;
        }

        return $base;
    }

    private function formatDate($date)
    {
        return Carbon::parse($date)->toIso8601String();
    }

    // =============================

    private function verifyToken($request)
    {
        $token = $request->header('X-Torob-Token');

        if (!$token) {
            abort(403);
        }

        $publicKey = base64_encode(substr(base64_decode('MCowBQYDK2VwAyEAt6Mu4T0pBORY11W+QeM35UsmLO3vsf+6yKpFDEImFk0='), -32));

        $decoded = JWT::decode($token, new Key($publicKey, 'EdDSA'));

        if ($decoded->aud !== request()->getHost()) {
            abort(403, 'Invalid audience');
        }
    }
}
