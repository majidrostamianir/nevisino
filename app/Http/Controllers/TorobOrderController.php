<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class TorobOrderController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. بررسی فعال بودن رهگیری سفارش
        if (!config('torob.order_tracking_enabled', false)) {
            return $this->jsonResponse([
                'error' => 'order tracking is disabled for this shop'
            ], 403);
        }

        // 2. احراز هویت JWT
        try {
            $this->verifyToken($request);
        } catch (Throwable $e) {
            Log::warning('Torob Order API: Authentication failed', [
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return $this->jsonResponse([
                'error' => 'unauthorized: ' . $e->getMessage()
            ], 401);
        }

        // 3. دریافت پارامترهای درخواست
        $rawSince = $request->query('purchase_timestamp_gt');
        $rawLimit = $request->query('limit');

        if (!is_string($rawSince) || trim($rawSince) === '') {
            return $this->jsonResponse([
                'error' => 'purchase_timestamp_gt is required'
            ], 400);
        }

        try {
            $since = Carbon::parse($rawSince)->utc();
        } catch (Throwable $e) {
            return $this->jsonResponse([
                'error' => 'purchase_timestamp_gt must be a valid ISO 8601 timestamp'
            ], 400);
        }

        if ($rawLimit === null || !is_numeric($rawLimit)) {
            return $this->jsonResponse([
                'error' => 'limit is required and must be numeric'
            ], 400);
        }

        $limit = (int) $rawLimit;

        if ($limit < 1 || $limit > 1000) {
            return $this->jsonResponse([
                'error' => 'limit must be between 1 and 1000'
            ], 400);
        }

        // 4. دریافت سفارش‌های جدیدتر از timestamp درخواست
        $sinceForDatabase = $since
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s.u');

        $orders = Order::query()
            ->whereNotNull('torob_clid')
            ->whereIn('status', ['paid', 'canceled'])
            ->where('created_at', '>', $sinceForDatabase)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->with(['items.product', 'transactions'])
            ->get();

        // 5. تبدیل سفارش‌ها به فرمت مورد انتظار ترب
        $data = $orders
            ->map(fn (Order $order) => $this->transformOrder($order))
            ->values()
            ->all();

        return $this->jsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function transformOrder(Order $order): array
    {
        $payload = [
            'purchase_timestamp' => $this->toIsoUtc($order->created_at),
            'last_updated_timestamp' => $this->toIsoUtc(
                $order->updated_at ?? $order->created_at
            ),
            'torob_clid' => (string) $order->torob_clid,
            'status' => $order->status === 'paid'
                ? 'completed'
                : 'cancelled',
        ];

        $psp = $this->resolvePsp($order);

        if ($psp !== null) {
            $payload['psp'] = $psp;
        }

        if ($order->total_price !== null) {
            $payload['order_value'] = (int) $order->total_price;
        }

        $shipping = (int) ($order->shipping_price ?? 0)
            + (int) ($order->packaging_price ?? 0);

        if ($shipping > 0) {
            $payload['shipping_amount'] = $shipping;
        }

        if (!empty($order->recipient_mobile)) {
            $payload['phone_number'] = (string) $order->recipient_mobile;
        }

        $products = $this->transformProducts($order);

        if (!empty($products)) {
            $payload['products'] = $products;
        }

        return $payload;
    }

    private function transformProducts(Order $order): array
    {
        if (!$order->relationLoaded('items') || $order->items->isEmpty()) {
            return [];
        }

        return $order->items->map(function ($item) {
            $productUrl = $item->product
                ? url('/product/' . $item->product->id . '/' . $item->product->dashed_url)
                : url('/product/' . $item->product_id);

            if ($item->variant_id) {
                $productUrl .= '?nvi=' . $item->variant_id;
            }

            return [
                'product_url' => $productUrl,
                'product_price' => (int) $item->price_snapshot,
                'quantity' => (int) $item->quantity,
            ];
        })->values()->all();
    }

    private function resolvePsp(Order $order): ?string
    {
        if (!$order->relationLoaded('transactions') || $order->transactions->isEmpty()) {
            return null;
        }

        $torobpayTx = $order->transactions
            ->whereNotNull('torobpay_transaction_id')
            ->first();

        if ($torobpayTx) {
            return 'torobpay';
        }

        $latest = $order->transactions
            ->sortByDesc('created_at')
            ->first();

        if ($latest && !empty($latest->payment_gateway)) {
            return strtolower($latest->payment_gateway);
        }

        return null;
    }

    private function toIsoUtc($date): string
    {
        return Carbon::parse($date)
            ->utc()
            ->format('Y-m-d\TH:i:s.u\Z');
    }

    private function jsonResponse(array $data, int $status = 200)
    {
        return response()->json(
            $data,
            $status,
            [],
            JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    }

    private function verifyToken(Request $request): void
    {
        $tokenHeader = config('torob.token_header', 'X-Torob-Token');
        $publicKey = config('torob.public_key');
        $algo = config('torob.jwt_algo', 'EdDSA');

        $token = $request->header($tokenHeader);

        if (!$token) {
            throw new \RuntimeException("missing {$tokenHeader} header");
        }

        if (!$publicKey) {
            throw new \RuntimeException(
                'torob public key not configured (TOROB_PUBLIC_KEY)'
            );
        }

        $seed = $this->extractEd25519Seed($publicKey);

        try {
            $decoded = JWT::decode($token, new Key($seed, $algo));
        } catch (Throwable $e) {
            throw new \RuntimeException(
                'invalid token: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $expectedAud = config('torob.expected_audience')
            ?: $request->getHttpHost();

        if (($decoded->aud ?? null) !== $expectedAud) {
            throw new \RuntimeException(
                "invalid audience: expected {$expectedAud}"
            );
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