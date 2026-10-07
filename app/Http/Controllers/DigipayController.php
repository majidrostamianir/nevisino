<?php

namespace App\Http\Controllers;

use App\Models\DigipayTransaction;
use App\Services\Digipay\VerifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigipayController extends Controller
{
    /**
     * دریافت callback از دیجی‌پی + verify
     */
    public function callback(Request $request, VerifyService $verifyService)
    {
        $data = $request->all();
        Log::info('Digipay Callback received', $data);

        $providerId = $data['providerId'] ?? null;
        if (!$providerId) {
            return redirect()->route('home');
        }

        $digipayTx = DigipayTransaction::with(['order', 'transaction'])
            ->where('provider_id', $providerId)
            ->first();

        if (!$digipayTx) {
            Log::warning('Digipay Callback: transaction not found', ['providerId' => $providerId]);
            return redirect()->route('home')->with('error', 'تراکنش یافت نشد');
        }

        // اگه قبلاً پردازش شده، فقط result رو نشون بده
        if (!$digipayTx->isPending()) {
            $this->putResultInSession($digipayTx, $this->statusFromTx($digipayTx));
            return redirect()->route('digipay.result');
        }

        $result = $data['result'] ?? null;

        // ─────────────────────────────────────────────
        // کنسل
        // ─────────────────────────────────────────────
        if ($result === 'CANCEL') {
            $digipayTx->markAsFailed($data);
            $digipayTx->transaction?->update(['status' => 'failed']);

            $this->putResultInSession($digipayTx, 'cancel');
            return redirect()->route('digipay.result');
        }

        // ─────────────────────────────────────────────
        // ناموفق
        // ─────────────────────────────────────────────
        if ($result !== 'SUCCESS') {
            $digipayTx->markAsFailed($data);
            $digipayTx->transaction?->update(['status' => 'failed']);

            $this->putResultInSession($digipayTx, 'failure');
            return redirect()->route('digipay.result');
        }

        // ─────────────────────────────────────────────
        // موفق → verify همینجا
        // ─────────────────────────────────────────────
        $digipayTx->update([
            'tracking_code' => $data['trackingCode'] ?? null,
        ]);

        try {
            $verifyResult = $verifyService->verify(
                trackingCode: $data['trackingCode'],
                providerId:   $providerId,
                type:         (int) ($data['type'] ?? 11),
            );

            $digipayTx->markAsVerified($verifyResult);
            $digipayTx->transaction?->update([
                'status' => 'success',
                'ref_id' => $verifyResult['rrn'] ?? null,
            ]);
            $digipayTx->order?->update(['status' => 'paid']);

            Log::info('Digipay verify success', [
                'provider_id' => $providerId,
                'tracking'    => $data['trackingCode'],
            ]);

            $this->putResultInSession($digipayTx->fresh(), 'success', $verifyResult);

        } catch (\Throwable $e) {
            Log::error('Digipay verify error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $digipayTx->markAsFailed(['verify_error' => $e->getMessage()]);
            $digipayTx->transaction?->update(['status' => 'failed']);

            $this->putResultInSession($digipayTx, 'failure');
        }

        return redirect()->route('digipay.result');
    }

    /**
     * تشخیص وضعیت از روی مدل
     */
    private function statusFromTx(DigipayTransaction $tx): string
    {
        return match ($tx->status) {
            DigipayTransaction::STATUS_VERIFIED => 'success',
            DigipayTransaction::STATUS_FAILED   => 'failure',
            default => 'failure',
        };
    }

    /**
     * ذخیره اطلاعات در session برای صفحه نتیجه
     */
    private function putResultInSession(DigipayTransaction $tx, string $status, array $verifyResult = []): void
    {
        $order = $tx->order;

        session([
            'digipay_result' => [
                'status'          => $status, // success | cancel | failure
                'success'         => $status === 'success',
                'order_number'    => $order?->order_number,
                'total_price'     => $order?->total_price ?? $order?->sum ?? 0,
                'shipping_price'  => $order?->shipping_price ?? 0,
                'packaging_price' => $order?->packaging_price ?? 0,
                'amount'          => (int) ($tx->amount / 10), // ریال → تومان
                'tracking_code'   => $tx->tracking_code,
                'rrn'             => $verifyResult['rrn'] ?? $tx->rrn,
                'masked_pan'      => $verifyResult['maskedPan'] ?? $tx->masked_pan,
                'psp_name'        => $verifyResult['pspName'] ?? $tx->psp_name,
            ],
        ]);
    }
}