<?php

namespace App\Services\Digipay;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AuthService
{
    public function getAccessToken(): string
    {
        return Cache::remember('digipay_access_token', now()->addMinutes(5), function () {
            $response = Http::asForm()
                ->withBasicAuth(
                    config('services.digipay.client_id'),
                    config('services.digipay.client_secret')
                )
                ->post(config('services.digipay.base_url') . '/oauth/token', [
                    'username'   => config('services.digipay.username'),
                    'password'   => config('services.digipay.password'),
                    'grant_type' => 'password',
                ]);

            if ($response->failed()) {
                throw new RuntimeException(
                    'خطا در دریافت توکن دیجی‌پی: ' . $response->body()
                );
            }

            $data = $response->json();

            if (empty($data['access_token'])) {
                throw new RuntimeException('توکن دسترسی در پاسخ دیجی‌پی وجود ندارد.');
            }

            $expiresIn = (int) ($data['expires_in'] ?? 300);

            Cache::put(
                'digipay_access_token',
                $data['access_token'],
                now()->addSeconds(max(1, $expiresIn - 60))
            );

            return $data['access_token'];
        });
    }
}