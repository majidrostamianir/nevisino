<?php

namespace App\Http\Controllers;

use App\Helpers\PriceHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TorobController extends Controller
{
    private const MAX_LEN = [
        'page_unique'      => 200,
        'page_url'         => 1500,
        'title'            => 500,
        'product_group_id' => 200,
        'category_name'    => 200,
        'short_desc'       => 500,
        'guarantee'        => 200,
        'image_link'       => 1000,
    ];

    private const GUARANTEE_TEXT = 'تصاویر اختصاصی محصول - ضمانت اصالت و بازگشت وجه';

    // =========================================================
    //  Entry point
    // =========================================================
    public function __invoke(Request $request)
    {
        // ---------- Auth ----------
        try {
            $this->verifyToken($request);
        } catch (Throwable $e) {
            return $this->jsonResponse([
                'error' => 'unauthorized: ' . $e->getMessage(),
            ], 403);
        }

        // ---------- Body ----------
        $body = $request->json()->all();

        // fallback: اگر json() به هر دلیلی بدنه را نخواند
        if (empty($body)) {
            $raw = $request->getContent();
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $body = $decoded;
                }
            }
        }

        if (empty($body)) {
            return $this->jsonResponse(['error' => 'empty body'], 400);
        }

        $hasPage    = array_key_exists('page', $body);
        $hasSort    = array_key_exists('sort', $body);
        $hasCursor  = array_key_exists('cursor', $body);
        $hasUniques = array_key_exists('page_uniques', $body);
        $hasUrls    = array_key_exists('page_urls', $body);

        // فقط یکی از ۴ حالت مجاز است
        $modes = (int)$hasUniques + (int)$hasUrls + (int)($hasPage || $hasCursor);
        if ($modes !== 1) {
            return $this->jsonResponse([
                'error' => 'exactly one of required: (page+sort), (cursor+sort), page_uniques, page_urls',
            ], 400);
        }

        // ---------- Pagination / Cursor ----------
        if ($hasPage || $hasCursor) {
            if (!$hasSort) {
                return $this->jsonResponse(['error' => 'sort parameter is not provided'], 400);
            }

            $sort = $body['sort'];
            if (!in_array($sort, ['date_added_desc', 'date_updated_desc', 'product_id_desc'], true)) {
                return $this->jsonResponse(['error' => 'invalid sort value'], 400);
            }

            if ($sort === 'product_id_desc') {
                if ($hasPage) {
                    return $this->jsonResponse([
                        'error' => 'page is not allowed with product_id_desc; use cursor',
                    ], 400);
                }
                return $this->handleCursorPagination($body);
            }

            if ($hasCursor) {
                return $this->jsonResponse([
                    'error' => 'cursor requires sort=product_id_desc',
                ], 400);
            }

            if (!is_int($body['page']) || $body['page'] < 1) {
                return $this->jsonResponse([
                    'error' => 'page must be an integer >= 1',
                ], 400);
            }

            return $this->handlePagination($body);
        }

        // ---------- By page_uniques ----------
        if ($hasUniques) {
            if (!is_array($body['page_uniques']) || empty($body['page_uniques'])) {
                return $this->jsonResponse([
                    'error' => 'page_uniques must be a non-empty array',
                ], 400);
            }
            return $this->handleByUniques($body['page_uniques']);
        }

        // ---------- By page_urls ----------
        if ($hasUrls) {
            if (!is_array($body['page_urls']) || empty($body['page_urls'])) {
                return $this->jsonResponse([
                    'error' => 'page_urls must be a non-empty array',
                ], 400);
            }
            return $this->handleByUrls($body['page_urls']);
        }

        return $this->jsonResponse(['error' => 'invalid parameters'], 400);
    }

    // =========================================================
    //  Page-based pagination
    // =========================================================
    private function handlePagination(array $body)
    {
        $page = (int)$body['page'];

        $query = Product::with(['variants', 'attributeValues.attribute', 'category']);

        if ($body['sort'] === 'date_added_desc') {
            $query->orderByDesc('created_at');
        } elseif ($body['sort'] === 'date_updated_desc') {
            $query->orderByDesc('updated_at');
        }

        $items = $this->flattenProducts($query->get());

        $total     = $items->count();
        $paginated = $items
            ->slice(($page - 1) * $this->perPage(), $this->perPage())
            ->values();

        return $this->pageResponse($paginated, $page, $total);
    }

    // =========================================================
    //  Cursor-based pagination
    // =========================================================
    private function handleCursorPagination(array $body)
    {
        $cursor = $body['cursor'] ?? null;

        $products = Product::with(['variants', 'attributeValues.attribute', 'category'])
            ->orderByDesc('id')
            ->get();

        $items = $this->flattenProducts($products);

        // sort: product_id desc, then variant_id desc
        $items = $items->sort(function ($a, $b) {
            $cmp = $b['product']->id <=> $a['product']->id;
            return $cmp !== 0
                ? $cmp
                : (($b['variant']?->id ?? 0) <=> ($a['variant']?->id ?? 0));
        })->values();

        // resume from cursor
        if ($cursor !== null && $cursor !== '') {
            $decoded = base64_decode($cursor, true);
            if ($decoded === false || substr_count($decoded, '_') !== 1) {
                return $this->jsonResponse(['error' => 'invalid cursor'], 400);
            }
            [$curPid, $curVid] = array_map('intval', explode('_', $decoded, 2));

            $items = $items->filter(function ($item) use ($curPid, $curVid) {
                $pid = $item['product']->id;
                $vid = $item['variant']?->id ?? 0;
                return $pid < $curPid || ($pid === $curPid && $vid < $curVid);
            })->values();
        }

        $pageItems = $items->take($this->perPage())->values();
        $hasMore   = $items->count() > $this->perPage();

        $nextCursor = null;
        if ($hasMore && $pageItems->isNotEmpty()) {
            $last       = $pageItems->last();
            $nextCursor = base64_encode($last['product']->id . '_' . ($last['variant']?->id ?? 0));
        }

        $data = [
            'api_version'  => 'torob_api_v3',
            'current_page' => null,
            'total'        => null,
            'max_pages'    => null,
            'next_cursor'  => $nextCursor,
            'products'     => $pageItems
                ->map(fn($i) => $this->transform($i['product'], $i['variant'], $i['stock']))
                ->all(),
        ];

        return $this->jsonResponse($data);
    }

    // =========================================================
    //  By page_uniques
    // =========================================================
    private function handleByUniques(array $uniques)
    {
        $uniques = array_map('strval', $uniques);

        $products = Product::with(['variants', 'attributeValues.attribute', 'category'])->get();

        $items = collect();
        foreach ($products as $product) {
            if ($product->variants->isEmpty()) {
                if (in_array((string)$product->id, $uniques, true)) {
                    $items->push([
                        'product' => $product,
                        'variant' => null,
                        'stock'   => (int)($product->stock ?? 0),
                    ]);
                }
            } else {
                foreach ($product->variants as $variant) {
                    if (in_array($product->id . '_' . $variant->id, $uniques, true)) {
                        $items->push([
                            'product' => $product,
                            'variant' => $variant,
                            'stock'   => (int)($variant->stock ?? 0),
                        ]);
                    }
                }
            }
        }

        return $this->pageResponse($items->values(), 1, $items->count());
    }

    // =========================================================
    //  By page_urls
    // =========================================================
    private function handleByUrls(array $urls)
    {
        $products = Product::with(['variants', 'attributeValues.attribute', 'category'])->get();

        $items = collect();
        foreach ($products as $product) {
            if ($product->variants->isEmpty()) {
                if (in_array($this->makeUrl($product, null), $urls, true)) {
                    $items->push([
                        'product' => $product,
                        'variant' => null,
                        'stock'   => (int)($product->stock ?? 0),
                    ]);
                }
            } else {
                foreach ($product->variants as $variant) {
                    if (in_array($this->makeUrl($product, $variant), $urls, true)) {
                        $items->push([
                            'product' => $product,
                            'variant' => $variant,
                            'stock'   => (int)($variant->stock ?? 0),
                        ]);
                    }
                }
            }
        }

        return $this->pageResponse($items->values(), 1, $items->count());
    }

    // =========================================================
    //  Helpers
    // =========================================================

    private function perPage(): int
    {
        return (int) config('torob.per_page', 100);
    }

    /** محصولات را به ردیف‌های (product, variant, stock) تبدیل می‌کند. */
    private function flattenProducts($products)
    {
        $items = collect();
        foreach ($products as $product) {
            if ($product->variants->isEmpty()) {
                $items->push([
                    'product' => $product,
                    'variant' => null,
                    'stock'   => (int)($product->stock ?? 0),
                ]);
            } else {
                foreach ($product->variants as $variant) {
                    $items->push([
                        'product' => $product,
                        'variant' => $variant,
                        'stock'   => (int)($variant->stock ?? 0),
                    ]);
                }
            }
        }
        return $items;
    }

    /** پاسخ استاندارد page-based. */
    private function pageResponse($items, int $page, int $total)
    {
        $data = [
            'api_version'  => 'torob_api_v3',
            'current_page' => $page,
            'total'        => $total,
            'max_pages'    => max(1, (int)ceil($total / $this->perPage())),
            'next_cursor'  => null,
            'products'     => $items
                ->map(fn($i) => $this->transform($i['product'], $i['variant'], $i['stock']))
                ->all(),
        ];

        return $this->jsonResponse($data);
    }

    /**
     * پاسخ JSON با UTF-8 امن.
     * JSON_INVALID_UTF8_SUBSTITUTE: هر بایت خراب را با \ufffd جایگزین می‌کند
     * تا json_encode هیچ‌وقت 500 ندهد.
     */
    private function jsonResponse(array $data, int $status = 200)
    {
        return response()->json(
            $data,
            $status,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    // =========================================================
    //  Transform
    // =========================================================
    private function transform($p, $v = null, int $stock = 0)
    {
        // ---------- تصاویر و عنوان ----------
        if ($v === null) {
            $path  = 'products/' . $p->id . '/large';
            $files = collect(Storage::disk('public')->files($path))
                ->sortBy(fn($f) => intval(pathinfo($f, PATHINFO_FILENAME)))
                ->values();
            $images = $files->map(fn($f) => asset('storage/' . $f))->values()->all();
            $title  = $p->title;
        } else {
            $images = [asset('storage/products/' . $p->id . '/large/' . $v->id . '.webp')];
            $title  = trim($p->title . ' - ' . ($p->variant ?? '') . ' ' . $v->name);
        }

        // ---------- spec ----------
        $spec = [];
        if ($p->variant && $v?->name) {
            $spec[(string)$p->variant] = (string)$v->name;
        }
        foreach ($p->attributeValues as $attrValue) {
            if ($attrValue->attribute && $attrValue->value !== null) {
                $spec[(string)$attrValue->attribute->name] = (string)$attrValue->value;
            }
        }
        if ($p->weight) {
            $spec['وزن'] = (ceil($p->weight / 5) * 5) . ' گرم';
        }

        // sanitize spec (هم کلید هم مقدار)
        $cleanSpec = [];
        foreach ($spec as $k => $val) {
            $k   = $this->sanitizeString((string)$k);
            $val = $this->sanitizeString((string)$val);
            if ($k !== '' && $val !== '' && $k !== null && $val !== null) {
                $cleanSpec[$k] = $val;
            }
        }

        // ---------- قیمت از PriceHelper ----------
        $currentPrice = PriceHelper::getProductPrice($p);
        $oldPrice     = PriceHelper::hasDiscount($p) ? PriceHelper::getBasePrice($p) : null;

        // ---------- خروجی ----------
        return [
            'page_unique'      => $this->sanitizeString(
                $this->truncate($this->makeUnique($p, $v), self::MAX_LEN['page_unique'])
            ),
            'page_url'         => $this->sanitizeString(
                $this->truncate($this->makeUrl($p, $v), self::MAX_LEN['page_url'])
            ),
            'product_group_id' => $this->sanitizeString(
                $this->truncate((string)$p->id, self::MAX_LEN['product_group_id'])
            ),
            'title'            => $this->sanitizeString(
                $this->truncate($title, self::MAX_LEN['title'])
            ),
            'subtitle'         => null,
            'current_price'    => (int)($currentPrice ?? 0),
            'old_price'        => $oldPrice !== null ? (int)$oldPrice : null,
            'availability'     => $stock > 0,
            'category_name'    => $this->sanitizeString(
                $this->truncate(
                    (string)($p->category?->title ?? ''),
                    self::MAX_LEN['category_name']
                )
            ) ?: null,
            'image_links'      => array_values(array_map(
                fn($u) => $this->sanitizeString(
                    $this->truncate($u, self::MAX_LEN['image_link'])
                ),
                $images
            )),
            'spec'             => $cleanSpec,
            'guarantee'        => self::GUARANTEE_TEXT,
            'short_desc'       => $this->makeShortDesc($p->description),
            'date_added'       => $this->formatDate($p->created_at),
            'date_updated'     => $this->formatDate($p->updated_at),
        ];
    }

    // =========================================================
    //  String helpers
    // =========================================================

    private function makeUnique($p, $v = null): string
    {
        return $v ? ($p->id . '_' . $v->id) : (string)$p->id;
    }

    private function makeUrl($p, $v = null): string
    {
        $base = url('/product/' . $p->id . '/' . $p->dashed_url);
        return $v ? ($base . '?nvi=' . $v->id) : $base;
    }

    private function formatDate($date): string
    {
        return Carbon::parse($date)->toIso8601String();
    }

    private function truncate(?string $s, int $len): string
    {
        $s = (string)$s;
        return mb_strlen($s) > $len ? mb_substr($s, 0, $len) : $s;
    }


    private function makeShortDesc(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return null;
        }

        // 1) UTF-8 را معتبر کن — این خط جلوی خطای "Malformed UTF-8" را می‌گیرد
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        }

        // 2) تگ‌های HTML را حذف کن
        $text = strip_tags($html);

        // 3) HTML entities را decode کن (&nbsp; &amp; &#x2026; …)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // 4) فاصله‌ها و خطوط جدید را به یک فاصله تبدیل کن
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        // 5) اگر بیش از ۵۰۰ کاراکتر بود، روی آخرین مرز کلمه ببر
        $max = self::MAX_LEN['short_desc']; // 500

        if (mb_strlen($text) > $max) {
            $cut = mb_substr($text, 0, $max);

            // تلاش کن روی فاصله ببر (اگر فاصله در ۲۰٪ آخر بود)
            $lastSpace = mb_strrpos($cut, ' ');
            if ($lastSpace !== false && $lastSpace > $max * 0.8) {
                $cut = mb_substr($cut, 0, $lastSpace);
            }

            // ← این خط مهم است: علائم پایانی را با regex (multibyte-safe) پاک کن
            //   نه با rtrim که byte-based است و وسط کاراکتر فارسی را می‌برد
            $cut = preg_replace('/[\s.,،؛;:!?]+$/u', '', $cut);

            $text = $cut . '…';
        }

        // 6) یک لایه sanitize نهایی — کاراکترهای کنترلی و بایت‌های خراب
        return $this->sanitizeString($text);
    }
    /**
     * رشته را از بایت‌های نامعتبر UTF-8 و کاراکترهای کنترلی پاک می‌کند.
     */
    private function sanitizeString(?string $s): ?string
    {
        if ($s === null || $s === '') {
            return $s;
        }

        if (!mb_check_encoding($s, 'UTF-8')) {
            $s = mb_convert_encoding($s, 'UTF-8', 'UTF-8');
        }

        // حذف کاراکترهای کنترلی به‌جز tab, LF, CR
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $s);

        return $s;
    }

    // =========================================================
    //  Security
    // =========================================================
    private function verifyToken(Request $request): void
    {
        $tokenHeader = config('torob.token_header', 'X-Torob-Token');
        $publicKey   = config('torob.public_key');
        $algo        = config('torob.jwt_algo', 'EdDSA');

        $token = $request->header($tokenHeader);
        if (!$token) {
            throw new \RuntimeException("missing {$tokenHeader} header");
        }
        if (!$publicKey) {
            throw new \RuntimeException('torob public key not configured (TOROB_PUBLIC_KEY)');
        }

        $seed = $this->extractEd25519Seed($publicKey);

        try {
            $decoded = JWT::decode($token, new Key($seed, $algo));
        } catch (Throwable $e) {
            throw new \RuntimeException('invalid token: ' . $e->getMessage(), 0, $e);
        }

        $expectedAud = config('torob.expected_audience') ?: $request->getHttpHost();
        if (($decoded->aud ?? null) !== $expectedAud) {
            throw new \RuntimeException("invalid audience: expected {$expectedAud}");
        }
    }


    private function extractEd25519Seed(string $publicKeyB64): string
    {
        $der = base64_decode($publicKeyB64, true);
        if ($der === false || strlen($der) < 32) {
            throw new \RuntimeException('invalid torob public key format');
        }
        return base64_encode(substr($der, -32));
    }
}