<?php

namespace App\Services\Tapin;

use App\Exceptions\TapinException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TapinClient
{
    protected string $baseUrl;
    protected string $token;
    protected string $shopId;
    protected int $timeout;
    protected int $retry;
    protected array $retryBackoff;

    public function __construct()
    {
        $this->baseUrl      = (string) config('tapin.base_url');
        $this->token        = (string) config('tapin.token');
        $this->shopId       = (string) config('tapin.shop_id');
        $this->timeout      = (int) config('tapin.timeout', 15);
        $this->retry        = (int) config('tapin.retry', 3);
        $this->retryBackoff = (array) config('tapin.retry_backoff', [5, 30, 120]);
    }

    public function shopId(): string
    {
        return $this->shopId;
    }

    // ---------------------------------------------------------
    // Endpoints
    // ---------------------------------------------------------

    public function states(): array
    {
        return $this->post('state/tree/', []);
    }

    public function cities(array $payload): array
    {
        return $this->post('city/list/', $payload);
    }

    public function packingBoxes(array $payload): array
    {
        return $this->post('order/post/packing-box/', $payload);
    }

    public function kiosks(array $payload): array
    {
        return $this->post('order/post/kiosk/', $payload);
    }

    public function checkPrice(array $payload): array
    {
        return $this->post('order/post/check-price/', $payload);
    }

    public function register(array $payload): array
    {
        return $this->post('order/post/register/', $payload);
    }

    public function detail(array $payload): array
    {
        return $this->post('order/post/detail/', $payload);
    }

    public function label(array $payload): array
    {
        return $this->post('order/post/detail/label/', $payload);
    }

    // ---------------------------------------------------------
    // Internals
    // ---------------------------------------------------------

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->retry(
                times: $this->retry,
                sleepMilliseconds: function (int $attempt) {
                    $idx = min($attempt - 1, count($this->retryBackoff) - 1);
                    return ($this->retryBackoff[$idx] ?? 5) * 1000;
                },
                when: function (Throwable $e) {
                    if ($e instanceof ConnectionException) {
                        return true;
                    }
                    if ($e instanceof RequestException && $e->response?->serverError()) {
                        return true;
                    }
                    return false;
                },
            );
    }

    protected function post(string $path, array $payload): array
    {
        $startedAt = microtime(true);

        $this->log('info', "→ POST {$path}", ['payload' => $payload]);

        try {
            $response = $this->http()->post($path, $payload);
        } catch (ConnectionException $e) {
            $this->log('error', "✗ connection error on {$path}", [
                'error'       => $e->getMessage(),
                'duration_ms' => $this->ms($startedAt),
            ]);
            throw new TapinException(
                "اتصال به تاپین برقرار نشد: {$e->getMessage()}",
                null,
                null,
                $e
            );
        } catch (Throwable $e) {
            $this->log('error', "✗ unexpected error on {$path}", [
                'error'       => $e->getMessage(),
                'duration_ms' => $this->ms($startedAt),
            ]);
            throw new TapinException("خطای غیرمنتظره در تماس با تاپین", null, null, $e);
        }

        return $this->handleResponse($path, $response, $startedAt);
    }

    protected function handleResponse(string $path, Response $response, float $startedAt): array
    {
        if ($response->failed()) {
            $body = $this->safeJson($response);
            $this->log('error', "✗ HTTP error on {$path}", [
                'http_status' => $response->status(),
                'body'        => $body,
                'duration_ms' => $this->ms($startedAt),
            ]);

            $message = data_get($body, 'returns.message')
                ?? data_get($body, 'message')
                ?? "خطای تاپین (HTTP {$response->status()})";

            throw new TapinException($message, $response->status(), $body);
        }

        $body = $this->safeJson($response);
        if (!is_array($body)) {
            $this->log('error', "✗ invalid JSON on {$path}", [
                'raw' => substr($response->body(), 0, 500),
            ]);
            throw new TapinException(
                "پاسخ تاپین JSON معتبر نبود",
                $response->status(),
                null
            );
        }

        $status = data_get($body, 'returns.status');
        if ($status !== null && (int) $status !== 200) {
            $message = data_get($body, 'returns.message', 'خطای نامشخص تاپین');
            $this->log('warning', "✗ logical error on {$path}", [
                'status'  => $status,
                'message' => $message,
                'body'    => $body,
            ]);
            throw new TapinException($message, $response->status(), $body);
        }

        $this->log('info', "✓ success on {$path}", [
            'duration_ms' => $this->ms($startedAt),
        ]);

        return $body;
    }

    protected function safeJson(Response $response): ?array
    {
        try {
            $json = $response->json();
            return is_array($json) ? $json : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected function ms(float $startedAt): int
    {
        return (int) ((microtime(true) - $startedAt) * 1000);
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        Log::channel('tapin')->{$level}($message, $context);
    }
}