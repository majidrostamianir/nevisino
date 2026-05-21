<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TorobpayService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $username;
    private string $password;

    // کلید Cache برای JWT
    private const CACHE_KEY = 'torobpay_jwt_token';

    // مدت اعتبار Cache - ۵ دقیقه کمتر از یک ساعت برای safety margin
    private const CACHE_TTL = 55 * 60;

    public function __construct()
    {
        $this->baseUrl      = config('torobpay.base_url');
        $this->clientId     = config('torobpay.client_id');
        $this->clientSecret = config('torobpay.client_secret');
        $this->username     = config('torobpay.username');
        $this->password     = config('torobpay.password');
    }

    // ─────────────────────────────────────────────
    //  JWT Token Management
    // ─────────────────────────────────────────────

    /**
     * گرفتن توکن JWT - از Cache یا API
     */
    private function getToken(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->fetchNewToken();
        });
    }

    /**
     * دریافت توکن جدید از API
     */
    private function fetchNewToken(): string
    {
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $response = Http::withHeaders([
            'Authorization' => "Basic {$credentials}",
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/api/online/v1/oauth/token", [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if (!$response->successful()) {
            Log::error('TorobPay: failed to get JWT token', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            throw new \Exception('خطا در دریافت توکن ترب‌پی');
        }

        return $response->json('access_token');
    }

    /**
     * هدرهای پایه با JWT
     */
    private function authHeaders(): array
    {
        try {
            return ['Authorization' => 'Bearer ' . $this->getToken(), 'Content-Type' => 'application/json'];
        } catch (\Exception $e) {
            Cache::forget(self::CACHE_KEY); // پاک کن و دوباره بگیر
            return ['Authorization' => 'Bearer ' . $this->fetchNewToken(), 'Content-Type' => 'application/json'];
        }
    }

    // ─────────────────────────────────────────────
    //  بررسی صلاحیت مرچنت برای نمایش ترب‌پی
    // ─────────────────────────────────────────────

    /**
     * قبل از نمایش درگاه ترب‌پی در checkout، این متد رو صدا بزن
     * مبلغ به تومان وارد میشه، داخل سرویس به ریال تبدیل میشه
     *
     * @return array{eligible: bool, message_title: string, description: string}
     */
    public function checkEligible(int $amountInToman): array
    {
        $amountInRial = $amountInToman * 10;

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->baseUrl}/api/online/offer/v1/eligible", [
                    'amount' => $amountInRial,
                ]);

            if (!$response->successful()) {
                return ['eligible' => false, 'message_title' => '', 'description' => ''];
            }

            return [
                'eligible'      => $response->json('response.eligible') === true,
                'message_title' => $response->json('response.message_title') ?? 'پرداخت اقساطی با ترب پی',
                'description'   => $response->json('response.description') ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('TorobPay: eligible check failed', ['error' => $e->getMessage()]);
            return ['eligible' => false, 'message_title' => '', 'description' => ''];
        }
    }

    // ─────────────────────────────────────────────
    //  ایجاد توکن پرداخت
    // ─────────────────────────────────────────────

    /**
     * صدور توکن پرداخت و گرفتن URL صفحه پرداخت
     * مبلغ‌ها به تومان وارد میشن، داخل سرویس به ریال تبدیل میشن
     *
     * @return array{paymentToken: string, paymentPageUrl: string}
     * @throws \Exception
     */
    public function createPaymentToken(Transaction $transaction, array $orderData): array
    {
        $order = $transaction->order;

        // تبدیل تومان به ریال
        $amountInRial = $transaction->amount * 10;
        $payload = [
            'amount'                    => $amountInRial,
            'paymentMethodTypeDto'      => 'ONLINE_CREDIT',
            'returnURL'                 => route('torobpay.callback'),
            'transactionId'             => (string) $transaction->id,
            'address'                   => $order->postal_address,
            'postalCode'                => $order->zipcode,
            'name_full_customer'        => $order->recipient_name,
            'city'                      => $order->city,
            'province'                  => $order->province,
            'number_phone_registration' => $order->user->mobile,
            'mobile'                    => $order->recipient_mobile,
            'cartList'                  => $this->buildCartList($transaction, $orderData),
        ];

        $response = Http::withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/online/payment/v1/token", $payload);

        if (!$response->successful()) {
            Log::error('TorobPay: create token failed', [
                'transaction_id' => $transaction->id,
                'status'         => $response->status(),
                'body'           => $response->json(),
            ]);
            throw new \Exception('خطا در ایجاد توکن پرداخت ترب‌پی');
        }

        return [
            'paymentToken'   => $response->json('response.paymentToken'),
            'paymentPageUrl' => $response->json('response.paymentPageUrl'),
        ];
    }

    // ─────────────────────────────────────────────
    //  Verify
    // ─────────────────────────────────────────────

    /**
     * تایید پرداخت - بعد از redirect کاربر به سایت صدا بزن
     * وضعیت سفارش از W_FOR_VERIFY به W_FOR_SETTLE میره
     *
     * @return string transactionId سمت ترب‌پی
     * @throws \Exception
     */
    public function verify(string $paymentToken): string
    {
        $response = Http::withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/online/payment/v1/verify", [
                'paymentToken' => $paymentToken,
            ]);

        if (!$response->successful()) {
            Log::error('TorobPay: verify failed', [
                'payment_token' => $paymentToken,
                'status'        => $response->status(),
                'body'          => $response->json(),
            ]);
            throw new \Exception('خطا در تایید پرداخت ترب‌پی');
        }

        return $response->json('response.transactionId');
    }

    // ─────────────────────────────────────────────
    //  Settle
    // ─────────────────────────────────────────────

    /**
     * تایید نهایی - بعد از verify صدا بزن
     * وضعیت از W_FOR_SETTLE به ONGOING میره
     *
     * @return string transactionId
     * @throws \Exception
     */
    public function settle(string $paymentToken): string
    {
        $response = Http::withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/online/payment/v1/settle", [
                'paymentToken' => $paymentToken,
            ]);

        if (!$response->successful()) {
            Log::error('TorobPay: settle failed', [
                'payment_token' => $paymentToken,
                'status'        => $response->status(),
                'body'          => $response->json(),
            ]);
            throw new \Exception('خطا در تایید نهایی ترب‌پی');
        }

        return $response->json('response.transactionId');
    }

    // ─────────────────────────────────────────────
    //  Revert (لغو - کمتر از ۳۰ دقیقه)
    // ─────────────────────────────────────────────

    /**
     * لغو سفارش قبل از تایید - فقط در W_FOR_VERIFY و کمتر از ۳۰ دقیقه
     *
     * @throws \Exception
     */
    public function revert(string $paymentToken): string
    {
        $response = Http::withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/online/payment/v1/revert", [
                'paymentToken' => $paymentToken,
            ]);

        if (!$response->successful()) {
            Log::error('TorobPay: revert failed', [
                'payment_token' => $paymentToken,
                'status'        => $response->status(),
                'body'          => $response->json(),
            ]);
            throw new \Exception('خطا در لغو سفارش ترب‌پی');
        }

        return $response->json('response.transactionId');
    }

    // ─────────────────────────────────────────────
    //  Cancel (لغو - بیشتر از ۳۰ دقیقه)
    // ─────────────────────────────────────────────

    /**
     * لغو سفارش بعد از تایید یا بیشتر از ۳۰ دقیقه
     * سفارش‌های ONGOING, W_FOR_SETTLE, W_FOR_VERIFY قابل لغوند
     *
     * @throws \Exception
     */
    public function cancel(string $paymentToken): string
    {
        $response = Http::withHeaders($this->authHeaders())
            ->post("{$this->baseUrl}/api/online/payment/v1/cancel", [
                'paymentToken' => $paymentToken,
            ]);

        if (!$response->successful()) {
            Log::error('TorobPay: cancel failed', [
                'payment_token' => $paymentToken,
                'status'        => $response->status(),
                'body'          => $response->json(),
            ]);
            throw new \Exception('خطا در کنسل سفارش ترب‌پی');
        }

        return $response->json('response.transactionId');
    }

    // ─────────────────────────────────────────────
    //  Status
    // ─────────────────────────────────────────────

    /**
     * بررسی وضعیت سفارش
     *
     * @return array{status: string, amount: int, transactionId: string}
     * @throws \Exception
     */
    public function getStatus(string $paymentToken): array
    {
        $response = Http::withHeaders($this->authHeaders())
            ->get("{$this->baseUrl}/api/online/payment/v1/status", [
                'paymentToken' => $paymentToken,
            ]);

        if (!$response->successful()) {
            Log::error('TorobPay: status check failed', [
                'payment_token' => $paymentToken,
                'status'        => $response->status(),
                'body'          => $response->json(),
            ]);
            throw new \Exception('خطا در بررسی وضعیت ترب‌پی');
        }

        return $response->json('response');
    }

    // ─────────────────────────────────────────────
    //  Helper: ساخت cartList از تراکنش
    // ─────────────────────────────────────────────

    private function buildCartList(Transaction $transaction, array $orderData): array
    {
        $order     = $transaction->order()->with('items.product')->first();
        $cartItems = $order->items->map(function ($item) {
            return [
                'id'             => (string) $item->product_id,
                'name'           => $item->product->title,
                'count'          => $item->quantity,
                'amount'         => $item->price_snapshot * 10, // تومان به ریال
                'category'       => $item->product->category?->title ?? 'general',
                'commissionType' => 0,
            ];
        })->toArray();

        return [[
            'cartId'              => (string) $order->id,
            'totalAmount'         => $transaction->amount * 10, // تومان به ریال
            'taxAmount'           => 0,
            'shippingAmount'      => $order->shipping_price * 10,
            'isTaxIncluded'       => false,
            'isShipmentIncluded'  => $order->shipping_price > 0,
            'cartItems'           => $cartItems,
        ]];
    }
}