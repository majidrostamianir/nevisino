<?php

namespace App\Jobs;

use App\Models\BasalamProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Basalam\SyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncProductToBasalam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // safety net: retryUntil ملاک اصلی است
    public int $tries = 100;

    public array $backoff = [300, 600, 900, 1800, 3600];

    public function __construct(
        public Product $product,
        public bool $updateOnly = false,
        public bool $skipPhotos = false
    ) {}

    /**
     * تا ۱۲ ساعت تلاش کن. اگر بعد ۱۲ ساعت باز هم 429 بود، fail شو.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(12);
    }

    public function handle(SyncService $sync): void
    {
        // اگر cooldown فعاله، Job رو به صف برگردون بدون مصرف try
        $remaining = $sync->cooldownRemaining();
        if ($remaining > 0) {
            Log::info('Basalam cooldown active, releasing job', [
                'product_id' => $this->product->id,
                'release_in' => $remaining,
            ]);

            $this->release($remaining);
            return;
        }

        $product = $this->product->fresh();

        if (!$product) {
            Log::warning('SyncProductToBasalam: محصول یافت نشد', [
                'id' => $this->product->id,
            ]);

            return;
        }

        // relations را یک‌بار لود کن تا کوئری اضافه نزنیم
        $product->loadMissing('variants', 'category', 'brand');

        // اگر واریانت دارد، برای هر واریانت یک محصول جدا در باسلام
        if ($product->variants->isNotEmpty()) {
            foreach ($product->variants as $variant) {
                $this->syncOneVariant($sync, $product, $variant);
            }

            return;
        }

        // محصول بدون واریانت
        $this->syncOneProduct($sync, $product);
    }

    /**
     * بررسی خطای محدودیت درخواست باسلام
     */
    protected function isRateLimitError(Throwable $e): bool
    {
        return (int) $e->getCode() === 429;
    }

    /**
     * آیا این listing قبلاً با همین داده‌ها سینک شده؟
     * (صرفه‌جویی در API هنگام retry پس از 429)
     */
    protected function isAlreadyUpToDate(
        BasalamProduct $listing,
        Product $product,
        ?ProductVariant $variant = null
    ): bool {
        if (!$listing->basalam_product_id
            || $listing->status !== 'synced'
            || !$listing->synced_at) {
            return false;
        }

        $productChanged = $product->updated_at?->timestamp ?? 0;
        $variantChanged = $variant?->updated_at?->timestamp ?? 0;

        $lastChange = max($productChanged, $variantChanged);

        return $listing->synced_at->timestamp >= $lastChange;
    }

    /**
     * محصول بدون واریانت
     */
    protected function syncOneProduct(
        SyncService $sync,
        Product $product
    ): void {
        $listing = BasalamProduct::firstOrCreate(
            [
                'product_id' => $product->id,
                'product_variant_id' => null,
            ],
            ['status' => 'pending']
        );

        // اگر قبلاً سینک شده و تغییری نکرده، skip
        if ($this->isAlreadyUpToDate($listing, $product)) {
            return;
        }

        try {
            // اگر شناسه باسلام نداریم، ابتدا با نام جست‌وجو کن
            if (!$listing->basalam_product_id) {
                $existing = $sync->findProductByName($product->title);

                if ($existing && isset($existing['id'])) {
                    $listing->update([
                        'basalam_product_id' => (string) $existing['id'],
                        'basalam_sku' => $existing['sku'] ?? null,
                    ]);

                    $listing->refresh();
                }
            }

            // updateOnly: اگر محصول در باسلام نیست، کاری نکن
            if ($this->updateOnly && !$listing->basalam_product_id) {
                Log::info('SyncProductToBasalam: updateOnly و محصول در باسلام موجود نیست', [
                    'product_id' => $product->id,
                ]);

                return;
            }

            $photoIds = $this->skipPhotos
                ? []
                : $this->uploadProductPhotos($sync, $product);

            $data = $this->buildProductData($product);

            if ($listing->basalam_product_id) {
                $response = $sync->updateProduct(
                    $listing->basalam_product_id,
                    $data,
                    $photoIds
                );

                $listing->update([
                    'basalam_sku' => $data['sku'] ?? $listing->basalam_sku,
                    'payload' => $data,
                    'response' => $response,
                    'status' => 'synced',
                    'synced_at' => now(),
                    'last_error' => null,
                ]);
            } else {
                $response = $sync->createProduct($data, $photoIds);

                $basalamId = $response['id'] ?? null;

                if (!$basalamId) {
                    throw new \RuntimeException(
                        'باسلام شناسه محصول را برنگرداند: ' .
                        json_encode($response, JSON_UNESCAPED_UNICODE)
                    );
                }

                $listing->update([
                    'basalam_product_id' => (string) $basalamId,
                    'basalam_sku' => $data['sku'] ?? null,
                    'payload' => $data,
                    'response' => $response,
                    'status' => 'synced',
                    'synced_at' => now(),
                    'last_error' => null,
                ]);
            }

        } catch (Throwable $e) {
            if ($this->isRateLimitError($e)) {
                // 429 دیدیم — cooldown از قبل فعال شده.
                // Job رو به صف برگردون؛ دفعه بعد که صف اجرا کرد،
                // cooldown چک می‌شه و اگه تموم شده، کار ادامه پیدا می‌کنه.
                $releaseIn = max($sync->cooldownRemaining(), 60);

                Log::info('Basalam rate limit, releasing job (product)', [
                    'product_id' => $product->id,
                    'release_in' => $releaseIn,
                ]);

                $this->release($releaseIn);
                return;
            }

            $listing->markFailed($e->getMessage());

            Log::error('SyncProductToBasalam failed (product)', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * واریانت — هر واریانت = یک محصول جدا در باسلام
     */
    protected function syncOneVariant(
        SyncService $sync,
        Product $product,
        ProductVariant $variant
    ): void {
        $listing = BasalamProduct::firstOrCreate(
            [
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
            ],
            ['status' => 'pending']
        );

        // اگر قبلاً سینک شده و تغییری نکرده، skip
        if ($this->isAlreadyUpToDate($listing, $product, $variant)) {
            return;
        }

        try {
            // نام دقیق واریانت باید با نامی که در باسلام ثبت می‌شود یکی باشد.
            $data = $this->buildVariantProductData($product, $variant);

            // اگر شناسه باسلام نداریم، ابتدا با نام کامل جست‌وجو کن
            if (!$listing->basalam_product_id) {
                $existing = $sync->findProductByName($data['name']);

                if ($existing && isset($existing['id'])) {
                    $listing->update([
                        'basalam_product_id' => (string) $existing['id'],
                        'basalam_sku' => $existing['sku'] ?? null,
                    ]);

                    $listing->refresh();
                }
            }

            // updateOnly: اگر واریانت در باسلام نیست، کاری نکن
            if ($this->updateOnly && !$listing->basalam_product_id) {
                Log::info('SyncProductToBasalam: updateOnly و واریانت در باسلام موجود نیست', [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                ]);

                return;
            }

            $photoIds = $this->skipPhotos
                ? []
                : $this->uploadVariantPhotos($sync, $product, $variant);

            if ($listing->basalam_product_id) {
                $response = $sync->updateProduct(
                    $listing->basalam_product_id,
                    $data,
                    $photoIds
                );

                $listing->update([
                    'basalam_sku' => $data['sku'] ?? $listing->basalam_sku,
                    'payload' => $data,
                    'response' => $response,
                    'status' => 'synced',
                    'synced_at' => now(),
                    'last_error' => null,
                ]);
            } else {
                $response = $sync->createProduct($data, $photoIds);

                $basalamId = $response['id'] ?? null;

                if (!$basalamId) {
                    throw new \RuntimeException(
                        'باسلام شناسه واریانت را برنگرداند: ' .
                        json_encode($response, JSON_UNESCAPED_UNICODE)
                    );
                }

                $listing->update([
                    'basalam_product_id' => (string) $basalamId,
                    'basalam_sku' => $data['sku'] ?? null,
                    'payload' => $data,
                    'response' => $response,
                    'status' => 'synced',
                    'synced_at' => now(),
                    'last_error' => null,
                ]);
            }

        } catch (Throwable $e) {
            if ($this->isRateLimitError($e)) {
                // 429 دیدیم — cooldown از قبل فعال شده.
                $releaseIn = max($sync->cooldownRemaining(), 60);

                Log::info('Basalam rate limit, releasing job (variant)', [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'release_in' => $releaseIn,
                ]);

                $this->release($releaseIn);
                return;
            }

            $listing->markFailed($e->getMessage());

            Log::error('SyncProductToBasalam failed (variant)', [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // آپلود عکس‌ها
    // ─────────────────────────────────────────────

    protected function uploadProductPhotos(
        SyncService $sync,
        Product $product
    ): array {
        return $this->uploadFilesUnder1000($sync, $product);
    }

    protected function uploadVariantPhotos(
        SyncService $sync,
        Product $product,
        ProductVariant $variant
    ): array {
        $dir = storage_path("app/public/products/{$product->id}/large");

        if (!is_dir($dir)) {
            return [];
        }

        $photoIds = [];

        // عکس اصلی واریانت
        $variantPhoto = "{$dir}/{$variant->id}.webp";

        if (is_file($variantPhoto)) {
            $photoIds[] = $sync->uploadPhoto($variantPhoto);
        }

        // عکس‌های محصول، تا سقف ۵ عکس کلی
        foreach ($this->collectProductPhotos($dir) as $path) {
            if (count($photoIds) >= 5) {
                break;
            }

            $photoIds[] = $sync->uploadPhoto($path);
        }

        return $photoIds;
    }

    protected function uploadFilesUnder1000(
        SyncService $sync,
        Product $product
    ): array {
        $dir = storage_path("app/public/products/{$product->id}/large");

        if (!is_dir($dir)) {
            return [];
        }

        $photoIds = [];

        foreach ($this->collectProductPhotos($dir) as $path) {
            if (count($photoIds) >= 5) {
                break;
            }

            $photoIds[] = $sync->uploadPhoto($path);
        }

        return $photoIds;
    }

    protected function collectProductPhotos(string $dir): array
    {
        $files = [];

        foreach (\File::files($dir) as $file) {
            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            if (!is_numeric($name)) {
                continue;
            }

            $num = (int) $name;

            if ($num >= 1000) {
                continue;
            }

            $files[$num] = $file->getPathname();
        }

        ksort($files);

        return array_values($files);
    }

    // ─────────────────────────────────────────────
    // ساخت داده برای باسلام
    // ─────────────────────────────────────────────

    protected function buildProductData(Product $product): array
    {
        $product->loadMissing('category');

        $basalamCategoryId = $this->resolveCategoryId($product);

        return [
            'name' => $product->title,

            'brief' => mb_substr(
                strip_tags($product->description ?? ''),
                0,
                200
            ),

            'description' => trim(strip_tags(str_replace(
                ['</p>', '</div>', '<br>', '<br/>', '<br />'],
                "\n",
                $product->description ?? ''
            ))),

            'category_id' => (int) $basalamCategoryId,

            'status' => (int) config('basalam.default_status', 3790),

            'price' => $this->calculateBasalamPrice((int) $product->price),

            'stock' => \Cache::get('basalam_force_stock', 0)
                ?: max(0, (int) ($product->stock ?? 0)),

            'sku' => 'bslm_' . $product->id,

            'weight' => max(100, (int) ($product->weight ?? 500)),

            'package_weight' => max(200, (int) ($product->weight ?? 500) + 100),

            'unit_quantity' => 1,

            'unit_type' => (int) (
                $product->category?->basalam_unit_type ?? 6304
            ),

            'preparation_days' => 0,

            'product_attribute' => $product->brand?->name
                ? [[
                    'attribute_id' => (int) config('basalam.brand_attribute_id', 1751),
                    'value' => $product->brand->name,
                ]]
                : null,
        ];
    }

    protected function calculateBasalamPrice(int $price): int
    {
        $percent = (float) \Cache::get('basalam_profit_percent', 9);

        $withProfit = $price * (1 + $percent / 100) * 10;

        return (int) (ceil($withProfit / 10000) * 10000);
    }

    protected function buildVariantProductData(
        Product $product,
        ProductVariant $variant
    ): array {
        $product->loadMissing('category');

        $basalamCategoryId = $this->resolveCategoryId($product);

        $variantName = trim((string) $variant->name);
        $variantField = trim((string) $product->variant);

        $title = $variantName !== ''
            ? $product->title . ' - ' .
            ($variantField ? $variantField . ' ' : '') .
            $variantName
            : $product->title;

        return [
            'name' => $title,

            'brief' => mb_substr(
                strip_tags($product->description ?? ''),
                0,
                200
            ),

            'description' => trim(strip_tags(str_replace(
                ['</p>', '</div>', '<br>', '<br/>', '<br />'],
                "\n",
                $product->description ?? ''
            ))),

            'category_id' => (int) $basalamCategoryId,

            'status' => (int) config('basalam.default_status', 3790),

            'price' => $this->calculateBasalamPrice((int) $product->price),

            'stock' => \Cache::get('basalam_force_stock', 0)
                ?: max(0, (int) ($variant->stock ?? 0)),

            'sku' => 'bslm_' . $product->id . '_' . $variant->id,

            'weight' => max(100, (int) ($product->weight ?? 500)),

            'package_weight' => max(200, (int) ($product->weight ?? 500) + 100),

            'unit_quantity' => 1,

            'unit_type' => (int) (
                $product->category?->basalam_unit_type ?? 6304
            ),

            'preparation_days' => 0,

            'product_attribute' => $product->brand?->name
                ? [[
                    'attribute_id' => (int) config('basalam.brand_attribute_id', 1751),
                    'value' => $product->brand->name,
                ]]
                : null,
        ];
    }

    protected function resolveCategoryId(Product $product): int
    {
        $basalamCategoryId = $product->category?->basalam_category_id;

        if (!$basalamCategoryId) {
            throw new \RuntimeException(
                "دسته باسلام برای محصول «{$product->title}» تعیین نشده است. " .
                "لطفاً از پنل ادمین → دسته‌بندی‌ها (admin/category/basalam) " .
                "دسته «" .
                ($product->category?->title ?? 'بدون دسته') .
                '» را نگاشت کنید.'
            );
        }

        return (int) $basalamCategoryId;
    }
}