<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TorobPayService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        // این مقادیر را در فایل .env قرار بده
        $this->baseUrl = config('services.torobpay.base_url', 'https://cpg.torobpay.com');
        $this->clientId = config('services.torobpay.client_id');
        $this->clientSecret = config('services.torobpay.client_secret');
        $this->username = config('services.torobpay.username');
        $this->password = config('services.torobpay.password');
    }

    /**
     * دریافت توکن JWT (با کش یک ساعته)
     */
    public function getAccessToken(): string
    {
        $cacheKey = 'torobpay_access_token';

        return Cache::remember($cacheKey, 3500, function () { // 3500 ثانیه ≈ 58 دقیقه
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->post($this->baseUrl . '/api/online/v1/oauth/token', [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);


            if ($response->failed()) {
                throw new \Exception('خطا در دریافت توکن ترب پی: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * بررسی صلاحیت کاربر برای خرید اعتباری
     */
    public function checkEligibility(int $amountInRials): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get($this->baseUrl . '/api/online/offer/v1/eligible', [
                'amount' => $amountInRials
            ]);

        if ($response->failed()) {
            return [
                'eligible' => false,
                'error' => $response->json('error.message') ?? 'خطا در بررسی صلاحیت'
            ];
        }

        return $response->json('response');
    }

    /**
     * دریافت توکن پرداخت برای شروع تراکنش
     */
    public function createPaymentToken(array $data): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/token', $data);

        if ($response->failed()) {
            throw new \Exception('خطا در ایجاد توکن پرداخت: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }

    /**
     * تایید پرداخت (verify)
     */
    public function verifyPayment(string $paymentToken): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/verify', [
                'paymentToken' => $paymentToken
            ]);

        if ($response->failed()) {
            throw new \Exception('خطا در تایید پرداخت: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }

    /**
     * تسویه نهایی (settle)
     */
    public function settlePayment(string $paymentToken): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/settle', [
                'paymentToken' => $paymentToken
            ]);

        if ($response->failed()) {
            throw new \Exception('خطا در تسویه: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }

    /**
     * برگرداندن سفارش (revert) - فقط در وضعیت W_FOR_VERIFY و کمتر از 30 دقیقه
     */
    public function revertPayment(string $paymentToken): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/revert', [
                'paymentToken' => $paymentToken
            ]);

        if ($response->failed()) {
            throw new \Exception('خطا در برگرداندن پرداخت: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }

    /**
     * لغو سفارش (cancel) - با بازپرداخت خودکار
     */
    public function cancelPayment(string $paymentToken): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/cancel', [
                'paymentToken' => $paymentToken
            ]);

        if ($response->failed()) {
            throw new \Exception('خطا در لغو سفارش: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }

    /**
     * دریافت وضعیت سفارش
     */
    public function getPaymentStatus(string $paymentToken): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get($this->baseUrl . '/api/online/payment/v1/status', [
                'paymentToken' => $paymentToken
            ]);

        if ($response->failed()) {
            throw new \Exception('خطا در دریافت وضعیت: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }
    /**
     * بروزرسانی سفارش (فقط کاهش مبلغ)
     */
    public function updatePayment(array $data): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/api/online/payment/v1/update', $data);

        if ($response->failed()) {
            throw new \Exception('خطا در بروزرسانی سفارش: ' . $response->json('error.message'));
        }

        return $response->json('response');
    }
}