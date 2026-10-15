<?php

namespace App\Services\Basalam;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SyncService
{
    protected string $baseUrl = 'https://openapi.basalam.com';
    protected string $token;
    protected int $vendorId;

    public function __construct()
    {
        $this->token    = (string) config('basalam.access_token');
        $this->vendorId = (int) config('basalam.vendor_id');

        if (empty($this->token)) {
            throw new RuntimeException('BASALAM_ACCESS_TOKEN در .env تنظیم نشده است.');
        }

        if (empty($this->vendorId)) {
            throw new RuntimeException('BASALAM_VENDOR_ID در .env تنظیم نشده است.');
        }
    }

    /**
     * هدرهای پایه برای همه درخواست‌ها
     */
    protected function headers(array $extra = []): array
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
        ], $extra);
    }

    /**
     * هندل کردن پاسخ و پرتاب استثنا در صورت خطا
     */
    protected function handle(Response $response): array
    {
        if ($response->failed()) {
            throw new RuntimeException(
                'Basalam API error [' . $response->status() . ']: ' . $response->body()
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
            Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/v1/users/me")
        );
    }

    /**
     * آپلود عکس محصول و برگرداندن شناسه فایل
     */
    public function uploadPhoto(string $filePath): string
    {
        if (!is_file($filePath)) {
            throw new RuntimeException("فایل یافت نشد: {$filePath}");
        }

        $response = Http::withHeaders($this->headers())
            ->attach('file', fopen($filePath, 'r'), basename($filePath))
            ->post("{$this->baseUrl}/v1/files", [
                'file_type' => 'product.photo',
            ]);

        $data = $this->handle($response);

        if (empty($data['id'])) {
            throw new RuntimeException('شناسه فایل از پاسخ باسلام دریافت نشد.');
        }

        return (string) $data['id'];
    }

    /**
     * ساخت محصول جدید در باسلام
     *
     * @param  array  $data      اطلاعات محصول (name, price, stock, ...)
     * @param  array  $photoIds  آرایه شناسه عکس‌ها؛ اولی عکس اصلی می‌شود
     */
    public function createProduct(array $data, array $photoIds = []): array
    {
        $payload = [
            'name'             => $data['name'],
            'photo'            => $photoIds[0] ?? null,
            'photos'           => array_slice($photoIds, 1),   // ← بقیه عکس‌ها توی photos
            'brief'            => $data['brief'] ?? '',
            'description'      => $data['description'] ?? '',
            'category_id'      => (int) $data['category_id'],
            'status'           => (int) $data['status'],
            'preparation_days' => $data['preparation_days'] ?? 0,
            'weight'           => $data['weight'] ?? 500,
            'package_weight'   => $data['package_weight'] ?? 600,
            'primary_price'    => $data['price'],
            'stock'            => $data['stock'] ?? 0,
            'sku'              => $data['sku'] ?? null,
            'is_wholesale'     => $data['is_wholesale'] ?? false,
            'unit_quantity'    => $data['unit_quantity'] ?? 1,
            'unit_type'        => $data['unit_type'] ?? 6304,
        ];

        $payload = array_filter($payload, fn ($v) => !is_null($v));
        // photos اگه خالی باشه نباید null بمونه
        if (empty($payload['photos'])) {
            unset($payload['photos']);
        }

        return $this->handle(
            Http::withHeaders($this->headers(['Content-Type' => 'application/json']))
                ->post("{$this->baseUrl}/v1/vendors/{$this->vendorId}/products", $payload)
        );
    }

    /**
     * دریافت لیست محصولات غرفه
     */
    public function getProducts(array $query = []): array
    {
        return $this->handle(
            Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/v1/vendors/{$this->vendorId}/products", $query)
        );
    }

    /**
     * دریافت یک محصول خاص
     */
    public function getProduct(int $productId): array
    {
        return $this->handle(
            Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/v1/products/{$productId}")  // ← عوض شد
        );
    }
    /**
     * به‌روزرسانی محصول موجود در باسلام
     */
    public function updateProduct(string $basalamProductId, array $data, array $photoIds = []): array
    {
        $payload = array_filter([
            'name'             => $data['name'] ?? null,
            'photo'            => $photoIds[0] ?? null,
            'photos'           => !empty($photoIds) ? array_slice($photoIds, 1) : null,
            'brief'            => $data['brief'] ?? null,
            'description'      => $data['description'] ?? null,
            'preparation_days' => $data['preparation_days'] ?? 0,
            'weight'           => $data['weight'] ?? null,
            'package_weight'   => $data['package_weight'] ?? null,
            'primary_price'    => $data['price'] ?? null,
            'stock'            => $data['stock'] ?? null,
            'sku'              => $data['sku'] ?? null,
            'unit_quantity'    => $data['unit_quantity'] ?? null,
            'unit_type'        => $data['unit_type'] ?? null,
        ], fn ($v) => !is_null($v));

        return $this->handle(
            Http::withHeaders($this->headers(['Content-Type' => 'application/json']))
                ->patch("{$this->baseUrl}/v1/products/{$basalamProductId}", $payload)  // ← عوض شد
        );
    }    /**
     * پیدا کردن محصول در باسلام بر اساس نام
     * همه صفحات رو می‌گرده تا پیدا کنه
     */
    public function findProductByName(string $name): ?array
    {
        $page = 1;

        do {
            $res = $this->handle(
                Http::withHeaders($this->headers())
                    ->get("{$this->baseUrl}/v1/vendors/{$this->vendorId}/products", [
                        'page'     => $page,
                        'per_page' => 50,   // ← از 100 به 50
                    ])
            );

            $items = $res['data'] ?? [];

            foreach ($items as $item) {
                if (($item['title'] ?? '') === $name) {
                    return $item;
                }
            }

            $page++;
        } while (count($items) === 50 && $page <= 20);  // ← سقف کمتر

        return null;
    }
}