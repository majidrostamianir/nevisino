<?php

namespace App\Services\Digipay;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class DigipayClient
{
    public function __construct(
        protected AuthService $auth
    ) {}

    /**
     * ارسال درخواست به دیجی‌پی با هدرهای استاندارد
     */
    public function request(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization'   => 'Bearer ' . $this->auth->getAccessToken(),
            'Agent'           => config('services.digipay.agent', 'WEB'),
            'Digipay-Version' => config('services.digipay.version', '2022-02-02'),
            'Content-Type'    => 'application/json',
            'Accept'          => 'application/json',
        ])
            ->baseUrl(config('services.digipay.base_url'))
            ->timeout(30)
            ->retry(2, 500, throw: false);
    }

    /**
     * POST request
     */
    public function post(string $url, array $data = []): Response
    {
        Log::info('Digipay POST', ['url' => $url, 'data' => $data]);

        $response = $this->request()->post($url, $data);

        Log::info('Digipay Response', [
            'url'    => $url,
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        return $response;
    }

    /**
     * GET request
     */
    public function get(string $url, array $query = []): Response
    {
        Log::info('Digipay GET', ['url' => $url, 'query' => $query]);

        $response = $this->request()->get($url, $query);

        Log::info('Digipay Response', [
            'url'    => $url,
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        return $response;
    }

    /**
     * بررسی موفق بودن پاسخ و برگرداندن دیتا
     */
    public function handle(Response $response): array
    {
        $data = $response->json() ?? [];

        if ($response->failed()) {
            $message = $data['result']['message']
                ?? $data['message']
                ?? 'خطای ناشناخته در ارتباط با دیجی‌پی';

            throw new RuntimeException($message, $response->status());
        }

        // بعضی از خطاهای بیزنسی با status=200 برمی‌گردن ولی result.status != 0
        if (isset($data['result']['status']) && $data['result']['status'] !== 0) {
            throw new RuntimeException(
                $data['result']['message'] ?? 'خطای بیزنسی دیجی‌پی',
                $data['result']['status']
            );
        }

        return $data;
    }
}