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

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public Product $product,
        public bool    $updateOnly = false,
        public bool    $skipPhotos = false

    ) {}

    public function handle(SyncService $sync): void
    {
        $product = $this->product->fresh();

        if (!$product) {
            Log::warning("SyncProductToBasalam: محصول یافت نشد", ['id' => $this->product->id]);
            return;
        }

        // اگر واریانت دارد → برای هر واریانت یک محصول جدا در باسلام
        if ($product->variants()->exists()) {
            foreach ($product->variants as $variant) {
                $this->syncOneVariant($sync, $product, $variant);
            }
            return;
        }

        // محصول بدون واریانت
        $this->syncOneProduct($sync, $product);
    }

    // ─────────────────────────────────────────────
    //  محصول بدون واریانت
    // ─────────────────────────────────────────────

    protected function syncOneProduct(SyncService $sync, Product $product): void
    {
        $listing = BasalamProduct::firstOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => null],
            ['status' => 'pending']
        );
// اگه basalam_product_id نداریم، توی باسلام با نام بگرد
        if (!$listing->basalam_product_id) {
            $existing = $sync->findProductByName($product->title);
            if ($existing && isset($existing['id'])) {
                $listing->update([
                    'basalam_product_id' => (string) $existing['id'],
                    'basalam_sku'        => $existing['sku'] ?? null,
                ]);
            }
        }
        try {
            $photoIds = $this->skipPhotos ? [] : $this->uploadProductPhotos($sync, $product);
            $data     = $this->buildProductData($product);

            if ($listing->basalam_product_id) {
                $response = $sync->updateProduct($listing->basalam_product_id, $data, $photoIds);
                $listing->update(['payload' => $data, 'response' => $response]);
            } else {
                $response  = $sync->createProduct($data, $photoIds);
                $basalamId = $response['id'] ?? null;

                if (!$basalamId) {
                    throw new \RuntimeException('باسلام شناسه محصول را برنگرداند: ' . json_encode($response));
                }

                $listing->update([
                    'basalam_product_id' => (string) $basalamId,
                    'basalam_sku'        => $data['sku'] ?? null,
                    'payload'            => $data,
                    'response'           => $response,
                ]);
            }

            $listing->markSynced($listing->basalam_product_id);
        } catch (Throwable $e) {
            $listing->markFailed($e->getMessage());
            Log::error("SyncProductToBasalam failed (product)", [
                'product_id' => $product->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    //  واریانت (هر واریانت = یک محصول جدا در باسلام)
    // ─────────────────────────────────────────────

    protected function syncOneVariant(SyncService $sync, Product $product, ProductVariant $variant): void
    {
        $listing = BasalamProduct::firstOrCreate(
            ['product_id' => $product->id, 'product_variant_id' => $variant->id],
            ['status' => 'pending']
        );
// اگه basalam_product_id نداریم، توی باسلام با نام کامل بگرد
        if (!$listing->basalam_product_id) {
            $variantName = trim((string) $variant->name);
            $fullName    = $variantName !== ''
                ? $product->title . ' - ' . $variantName
                : $product->title;

            $existing = $sync->findProductByName($fullName);
            if ($existing && isset($existing['id'])) {
                $listing->update([
                    'basalam_product_id' => (string) $existing['id'],
                    'basalam_sku'        => $existing['sku'] ?? null,
                ]);
            }
        }
        try {
            $photoIds = $this->skipPhotos ? [] : $this->uploadVariantPhotos($sync, $product, $variant);
            $data     = $this->buildVariantProductData($product, $variant);

            if ($listing->basalam_product_id ) {
                $response = $sync->updateProduct($listing->basalam_product_id, $data, $photoIds);
                $listing->update(['payload' => $data, 'response' => $response]);
            } else {
                $response  = $sync->createProduct($data, $photoIds);
                $basalamId = $response['id'] ?? null;

                if (!$basalamId) {
                    throw new \RuntimeException('باسلام شناسه واریانت را برنگرداند: ' . json_encode($response));
                }

                $listing->update([
                    'basalam_product_id' => (string) $basalamId,
                    'basalam_sku'        => $data['sku'] ?? null,
                    'payload'            => $data,
                    'response'           => $response,
                ]);
            }

            $listing->markSynced($listing->basalam_product_id);
        } catch (Throwable $e) {
            $listing->markFailed($e->getMessage());
            Log::error("SyncProductToBasalam failed (variant)", [
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────
    //  آپلود عکس‌ها
    // ─────────────────────────────────────────────

    /**
     * عکس‌های محصول بدون واریانت: 1.webp, 2.webp, ...
     */
    protected function uploadProductPhotos(SyncService $sync, Product $product): array
    {
        return $this->uploadFilesUnder1000($sync, $product);
    }

    /**
     * عکس‌های واریانت:
     *   1. عکس خود واریانت: {variant->id}.webp (عکس اصلی)
     *   2. عکس‌های محصول: 1.webp, 2.webp, ... (آلبوم)
     */
    protected function uploadVariantPhotos(SyncService $sync, Product $product, ProductVariant $variant): array
    {
        $dir = storage_path("app/public/products/{$product->id}/large");

        if (!is_dir($dir)) {
            return [];
        }

        $photoIds = [];

        // ۱. عکس اصلی واریانت
        $variantPhoto = "{$dir}/{$variant->id}.webp";
        if (is_file($variantPhoto)) {
            $photoIds[] = $sync->uploadPhoto($variantPhoto);
        }

        // ۲. عکس‌های محصول (تا سقف ۵ عکس کلی)
        $productPhotos = $this->collectProductPhotos($dir);
        foreach ($productPhotos as $path) {
            if (count($photoIds) >= 5) break;
            $photoIds[] = $sync->uploadPhoto($path);
        }

        return $photoIds;
    }

    protected function uploadFilesUnder1000(SyncService $sync, Product $product): array
    {
        $dir = storage_path("app/public/products/{$product->id}/large");

        if (!is_dir($dir)) {
            return [];
        }

        $photoIds = [];
        foreach ($this->collectProductPhotos($dir) as $path) {
            if (count($photoIds) >= 5) break;
            $photoIds[] = $sync->uploadPhoto($path);
        }

        return $photoIds;
    }

    /**
     * جمع‌آوری عکس‌های محصول (فایل‌های عددی < 1000)، مرتب‌شده بر اساس شماره.
     *
     * @return array<string> آرایه‌ی مسیر فایل‌ها
     */
    protected function collectProductPhotos(string $dir): array
    {
        $files = [];

        foreach (\File::files($dir) as $file) {
            $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            if (!is_numeric($name)) continue;

            $num = (int) $name;
            if ($num >= 1000) continue;

            $files[$num] = $file->getPathname();
        }

        ksort($files);
        return array_values($files);
    }

    // ─────────────────────────────────────────────
    //  ساخت داده برای باسلام
    // ─────────────────────────────────────────────

    /**
     * داده محصول بدون واریانت
     */
    protected function buildProductData(Product $product): array
    {
        $product->loadMissing('category');
        $basalamCategoryId = $this->resolveCategoryId($product);

        return [
            'name'           => $product->title,
            'brief'          => mb_substr(strip_tags($product->description ?? ''), 0, 200),
            'description' => trim(strip_tags(str_replace(
                ['</p>', '</div>', '<br>', '<br/>', '<br />'],
                "\n",
                $product->description ?? ''
            ))),
            'category_id'    => (int) $basalamCategoryId,
            'status'         => (int) config('basalam.default_status', 3790),
            'price' => $this->calculateBasalamPrice((int) $product->price),
            'stock' => \Cache::get('basalam_force_stock_one', false)
                ? 1
                : max(0, (int) ($product->stock ?? 0)),
            'sku'            => 'bslm_' . $product->id,
            'weight'         => max(100, (int) ($product->weight ?? 500)),
            'package_weight' => max(200, (int) ($product->weight ?? 500) + 100),
            'unit_quantity' => 1,
            'unit_type' => (int) ($product->category?->basalam_unit_type ?? 6304),
            'preparation_days' => 0,
        ];
    }
    /**
     * قیمت نهایی برای باسلام:
     * قیمت + 9% → گرد شده به مضرب ۱۰۰۰ (رو به بالا)
     */
    protected function calculateBasalamPrice(int $price): int
    {
        $percent = (float) \Cache::get('basalam_profit_percent', 9);
        $withProfit = $price * (1 + $percent / 100) * 10;
        return (int) (ceil($withProfit / 10000) * 10000);
    }
    /**
     * داده محصول واریانت‌دار (هر واریانت = یک محصول)
     */
    protected function buildVariantProductData(Product $product, ProductVariant $variant): array
    {
        $product->loadMissing('category');
        $basalamCategoryId = $this->resolveCategoryId($product);

        $variantName   = trim((string) $variant->name);
        $variantField  = trim((string) $product->variant);   // ← "تناژ پوست"

        $title = $variantName !== ''
            ? $product->title . ' - ' . ($variantField ? $variantField . ' ' : '') . $variantName
            : $product->title;


        return [
            'name'           => $title,
            'brief'          => mb_substr(strip_tags($product->description ?? ''), 0, 200),
            'description' => trim(strip_tags(str_replace(
                ['</p>', '</div>', '<br>', '<br/>', '<br />'],
                "\n",
                $product->description ?? ''
            ))),
            'category_id'    => (int) $basalamCategoryId,
            'status'         => (int) config('basalam.default_status', 3790),
            'price' => $this->calculateBasalamPrice((int) $product->price),
            'stock' => \Cache::get('basalam_force_stock_one', false)
                ? 1
                : max(0, (int) ($variant->stock ?? 0)),
            'sku'            => 'bslm_' . $product->id . '_' . $variant->id,
            'weight'         => max(100, (int) ($product->weight ?? 500)),
            'package_weight' => max(200, (int) ($product->weight ?? 500) + 100),
            'unit_quantity' => 1,
            'unit_type' => (int) ($product->category?->basalam_unit_type ?? 6304),
            'preparation_days' => 0,
        ];
    }

    /**
     * پیدا کردن دسته باسلام برای محصول
     */
    protected function resolveCategoryId(Product $product): int
    {
        $basalamCategoryId = $product->category?->basalam_category_id;

        if (!$basalamCategoryId) {
            throw new \RuntimeException(
                "دسته باسلام برای محصول «{$product->title}» تعیین نشده است. " .
                "لطفاً از پنل ادمین →  دسته‌بندی‌ها (admin/category/basalam) " .
                "دسته «" . ($product->category?->title ?? 'بدون دسته') . "» را نگاشت کنید."
            );
        }

        return (int) $basalamCategoryId;
    }
}