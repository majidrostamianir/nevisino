<?php

namespace App\Services\Basalam;

use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SyncService
{
    protected string $baseUrl = 'https://openapi.basalam.com';
    protected string $token;
    protected int $vendorId;

    // حداقل فاصله بین درخواست‌های باسلام (قابل تنظیم از .env)
    protected int $requestInterval;

    // کلیدهای مشترک بین تمام Jobها
    protected string $requestLockKey = 'basalam-api-request-lock';
    protected string $lastRequestKey = 'basalam_last_request_at';
    protected string $cooldownKey = 'basalam_api_cooldown_until';

    // TTL قفل باید بیشتر از حداکثر زمان یک درخواست (آپلود عکس بزرگ) باشد
    protected int $lockTtl = 300;

    // حداکثر انتظار برای گرفتن قفل
    protected int $lockWait = 90;

    public function __construct()
    {
        $this->token = (string) config('basalam.access_token');
        $this->vendorId = (int) config('basalam.vendor_id');
        $this->requestInterval = max(2, (int) config('basalam.request_interval', 5));

        if (empty($this->token)) {
            throw new RuntimeException('BASALAM_ACCESS_TOKEN در .env تنظیم نشده است.');
        }

        if (empty($this->vendorId)) {
            throw new RuntimeException('BASALAM_VENDOR_ID در .env تنظیم نشده است.');
        }
    }

    /**
     * چند ثانیه مونده تا cooldown تموم بشه؟ (۰ یعنی فعال نیست)
     */
    public function cooldownRemaining(): int
    {
        $cooldownUntil = (int) Cache::get($this->cooldownKey, 0);

        return max(0, $cooldownUntil - time());
    }

    /**
     * هدرهای پایه
     */
    protected function headers(array $extra = []): array
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ], $extra);
    }

    /**
     * اجرای درخواست با قفل مشترک و کنترل محدودیت API
     */
    protected function sendRequest(callable $callback): Response
    {
        // مسیر سریع: cooldown رو بدون گرفتن قفل چک کن
        // (تا صدها Job بی‌دلیل منتظر قفل نمونه)
        $remaining = $this->cooldownRemaining();
        if ($remaining > 0) {
            throw new RuntimeException(
                "محدودیت درخواست باسلام فعال است. تلاش مجدد پس از {$remaining} ثانیه.",
                429
            );
        }

        try {
            return Cache::lock($this->requestLockKey, $this->lockTtl)
                ->block($this->lockWait, function () use ($callback) {
                    // دوباره چک کن — شاید توی همین فاصله یکی دیگه cooldown فعال کرده
                    $remaining = $this->cooldownRemaining();
                    if ($remaining > 0) {
                        throw new RuntimeException(
                            "محدودیت درخواست باسلام فعال است. تلاش مجدد پس از {$remaining} ثانیه.",
                            429
                        );
                    }

                    // فاصله زمانی مشترک بین درخواست‌ها
                    $lastRequest = (float) Cache::get($this->lastRequestKey, 0);
                    $elapsed = microtime(true) - $lastRequest;
                    $remaining = $this->requestInterval - $elapsed;

                    if ($remaining > 0) {
                        usleep((int) ceil($remaining * 1_000_000));
                    }

                    try {
                        $response = $callback();

                        // در صورت دریافت 429، تمام درخواست‌های بعدی
                        // را برای مدت مشخص متوقف کن.
                        if ($response instanceof Response && $response->status() === 429) {
                            $this->activateCooldown($response);
                        }

                        return $response;
                    } finally {
                        Cache::put($this->lastRequestKey, microtime(true), 120);
                    }
                });
        } catch (LockTimeoutException $e) {
            // قفل در زمان معقول آزاد نشد → سیستم شلوغ است → مثل 429 رفتار کن
            throw new RuntimeException(
                'سیستم سینک باسلام شلوغ است. تلاش مجدد.',
                429,
                $e
            );
        }
    }

    /**
     * فعال کردن توقف موقت درخواست‌ها پس از خطای 429
     */
    protected function activateCooldown(Response $response): void
    {
        $retryAfter = $response->header('Retry-After');

        if ($retryAfter !== null && is_numeric($retryAfter)) {
            $delay = (int) $retryAfter;
        } elseif ($retryAfter !== null && strtotime($retryAfter) !== false) {
            $delay = strtotime($retryAfter) - time();
        } else {
            // اگر باسلام زمان مشخص نکرد، ۵ دقیقه صبر کن
            $delay = 300;
        }

        $delay = max(60, min(1800, $delay));

        $cooldownUntil = time() + $delay;
        $currentCooldown = (int) Cache::get($this->cooldownKey, 0);

        // اگر توقف قبلی طولانی‌تر است، آن را کوتاه نکن
        $cooldownUntil = max($cooldownUntil, $currentCooldown);

        Cache::put(
            $this->cooldownKey,
            $cooldownUntil,
            max(60, $cooldownUntil - time() + 60)
        );
    }

    /**
     * هندل کردن پاسخ
     */
    protected function handle(Response $response): array
    {
        if ($response->failed()) {
            $status = $response->status();

            // برای 429 پیام کوتاه تا لاگ‌ها سنگین نشوند
            if ($status === 429) {
                throw new RuntimeException('Basalam API rate limit (429)', 429);
            }

            throw new RuntimeException(
                'Basalam API error [' . $status . ']: ' . $response->body(),
                $status
            );
        }

        return $response->json() ?? [];
    }

    /**
     * دریافت اطلاعات کاربر جاری
     */
    public function getMe(): array
    {
        return $this->handle(
            $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers())
                ->get("{$this->baseUrl}/v1/users/me")
            )
        );
    }

    /**
     * آپلود عکس محصول
     */
    public function uploadPhoto(string $filePath): string
    {
        if (!is_file($filePath)) {
            throw new RuntimeException("فایل یافت نشد: {$filePath}");
        }

        $file = fopen($filePath, 'r');

        if ($file === false) {
            throw new RuntimeException("امکان باز کردن فایل وجود ندارد: {$filePath}");
        }

        try {
            $response = $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers())
                ->attach('file', $file, basename($filePath))
                ->post("{$this->baseUrl}/v1/files", [
                    'file_type' => 'product.photo',
                ])
            );
        } finally {
            fclose($file);
        }

        $data = $this->handle($response);

        if (empty($data['id'])) {
            throw new RuntimeException('شناسه فایل از پاسخ باسلام دریافت نشد.');
        }

        return (string) $data['id'];
    }

    /**
     * ساخت محصول جدید
     */
    public function createProduct(array $data, array $photoIds = []): array
    {
        $payload = [
            'name' => $data['name'],
            'photo' => $photoIds[0] ?? null,
            'photos' => array_slice($photoIds, 1),
            'brief' => $data['brief'] ?? '',
            'description' => $data['description'] ?? '',
            'category_id' => (int) $data['category_id'],
            'status' => (int) $data['status'],
            'preparation_days' => $data['preparation_days'] ?? 0,
            'weight' => $data['weight'] ?? 500,
            'package_weight' => $data['package_weight'] ?? 600,
            'primary_price' => $data['price'],
            'stock' => $data['stock'] ?? 0,
            'sku' => $data['sku'] ?? null,
            'is_wholesale' => $data['is_wholesale'] ?? false,
            'unit_quantity' => $data['unit_quantity'] ?? 1,
            'unit_type' => $data['unit_type'] ?? 6304,
            'product_attribute' => $data['product_attribute'] ?? null,
        ];

        $payload = array_filter($payload, fn ($v) => !is_null($v));

        if (empty($payload['photos'])) {
            unset($payload['photos']);
        }

        $response = $this->handle(
            $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers([
                    'Content-Type' => 'application/json',
                ]))
                ->post(
                    "{$this->baseUrl}/v1/vendors/{$this->vendorId}/products",
                    $payload
                )
            )
        );

        // محصول تازه‌ساخته‌شده را به کش کاتالوگ اضافه کن
        // تا در تلاش مجدد اشتباهاً دوباره ساخته نشود.
        if (!empty($response['id'])) {
            $this->appendToCatalogCache([
                'id' => $response['id'],
                'title' => $response['title'] ?? ($response['name'] ?? $data['name']),
                'sku' => $response['sku'] ?? ($data['sku'] ?? null),
            ]);
        }

        return $response;
    }

    /**
     * دریافت لیست محصولات غرفه
     */
    public function getProducts(array $query = []): array
    {
        return $this->handle(
            $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers())
                ->get(
                    "{$this->baseUrl}/v1/vendors/{$this->vendorId}/products",
                    $query
                )
            )
        );
    }

    /**
     * دریافت یک محصول خاص
     */
    public function getProduct(int $productId): array
    {
        return $this->handle(
            $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers())
                ->get("{$this->baseUrl}/v1/products/{$productId}")
            )
        );
    }

    /**
     * به‌روزرسانی محصول
     */
    public function updateProduct(
        string $basalamProductId,
        array $data,
        array $photoIds = []
    ): array {
        $payload = array_filter([
            'name' => $data['name'] ?? null,
            'photo' => $photoIds[0] ?? null,
            'photos' => !empty($photoIds) ? array_slice($photoIds, 1) : null,
            'brief' => $data['brief'] ?? null,
            'description' => $data['description'] ?? null,
            'preparation_days' => $data['preparation_days'] ?? 0,
            'weight' => $data['weight'] ?? null,
            'package_weight' => $data['package_weight'] ?? null,
            'primary_price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'sku' => $data['sku'] ?? null,
            'unit_quantity' => $data['unit_quantity'] ?? null,
            'unit_type' => $data['unit_type'] ?? null,
            'product_attribute' => $data['product_attribute'] ?? null,
        ], fn ($v) => !is_null($v));

        $response = $this->handle(
            $this->sendRequest(fn () =>
            Http::timeout(30)
                ->withHeaders($this->headers([
                    'Content-Type' => 'application/json',
                ]))
                ->patch(
                    "{$this->baseUrl}/v1/products/{$basalamProductId}",
                    $payload
                )
            )
        );

        // به‌روزرسانی کش کاتالوگ
        $this->appendToCatalogCache([
            'id' => (int) $basalamProductId,
            'title' => $response['title'] ?? ($response['name'] ?? ($data['name'] ?? '')),
            'sku' => $response['sku'] ?? ($data['sku'] ?? null),
        ]);

        return $response;
    }

    // ─────────────────────────────────────────────
    // کش کاتالوگ (map از نام → محصول)
    // ─────────────────────────────────────────────

    protected function catalogCacheKey(): string
    {
        return 'basalam_vendor_catalog_' . $this->vendorId;
    }

    /**
     * افزودن/به‌روزرسانی یک آیتم در کش کاتالوگ
     * (فقط اگر کش از قبل موجود باشد)
     */
    protected function appendToCatalogCache(array $item): void
    {
        $name = trim((string) ($item['title'] ?? ''));

        if ($name === '') {
            return;
        }

        $cache = Cache::get($this->catalogCacheKey());

        if (!is_array($cache)) {
            // کش از قبل نیست؛ دفعه بعد از API ساخته می‌شود
            return;
        }

        $cache[$name] = [
            'id' => $item['id'],
            'title' => $name,
            'sku' => $item['sku'] ?? null,
        ];

        Cache::put($this->catalogCacheKey(), $cache, 600);
    }

    /**
     * پیدا کردن محصول بر اساس نام
     *
     * نتیجه به‌صورت map (نام → محصول) در Cache ذخیره می‌شود
     * تا برای Jobهای بعدی دوباره تمام صفحات بررسی نشوند.
     */
    public function findProductByName(string $name): ?array
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        /** @var array<string, array> $map */
        $map = Cache::remember(
            $this->catalogCacheKey(),
            600,
            function () {
                $map = [];
                $page = 1;

                // سقف ۵۰ صفحه × ۵۰ = ۲۵۰۰ محصول؛ در صورت نیاز افزایش بده
                do {
                    $response = $this->handle(
                        $this->sendRequest(fn () =>
                        Http::timeout(30)
                            ->withHeaders($this->headers())
                            ->get(
                                "{$this->baseUrl}/v1/vendors/{$this->vendorId}/products",
                                [
                                    'page' => $page,
                                    'per_page' => 50,
                                ]
                            )
                        )
                    );

                    $items = $response['data'] ?? [];

                    foreach ($items as $item) {
                        $key = trim((string) ($item['title'] ?? ''));

                        if ($key !== '') {
                            $map[$key] = $item;
                        }
                    }

                    $page++;

                } while (count($items) === 50 && $page <= 50);

                return $map;
            }
        );

        return $map[$name] ?? null;
    }
}