<?php

namespace App\Http\Controllers;

use App\Enums\TorobpayStatusEnum;
use App\Models\Transaction;
use App\Services\TorobpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TorobPayController extends Controller
{
    public function __construct(private TorobpayService $torobpay) {}

    // ─────────────────────────────────────────────
    //  Callback - ترب‌پی کاربر رو اینجا برمیگردونه
    //  POST /payment/torobpay/callback
    // ─────────────────────────────────────────────

    public function callback(Request $request)
    {
        // فقط transactionId (همون id جدول transactions خودمون) رو از POST میگیریم
        $transactionId = $request->input('transactionId');
        $stateFromPost = $request->input('state'); // فقط برای لاگ، بهش اعتماد نمیکنیم

        if (!$transactionId) {
            Log::warning('TorobPay callback: missing transactionId');
            return $this->failRedirect();
        }

        // تراکنش رو از DB پیدا میکنیم - نه از پارامترهای POST
        $transaction = Transaction::query()
            ->with('order')
            ->where('id', $transactionId)
            ->where('payment_gateway', 'torobpay')
            ->where('status', 'pending')
            ->first();

        if (!$transaction || !$transaction->payment_token) {
            Log::warning('TorobPay callback: transaction not found or missing token', [
                'transaction_id' => $transactionId,
            ]);
            return $this->failRedirect();
        }

        Log::info('TorobPay callback received', [
            'transaction_id'  => $transaction->id,
            'state_from_post' => $stateFromPost, // فقط لاگ، ملاک نیست
        ]);

        // اگر state از ترب‌پی FAILED بود، مستقیم شکست
        // این فقط برای جلوگیری از verify غیرضروریه، ملاک اصلی verify هست
        if ($stateFromPost === 'FAILED') {
            $this->markTransactionFailed($transaction, TorobpayStatusEnum::FAILED->value);
            return $this->failRedirect();
        }

        // ─── Verify از API ترب‌پی - اینجاست که واقعاً پرداخت رو تایید میکنیم ───
        try {
            DB::beginTransaction();

            $torobpayTransactionId = $this->torobpay->verify($transaction->payment_token);

            // بعد از verify موفق، settle رو هم صدا میزنیم
            $this->torobpay->settle($transaction->payment_token);

            // آپدیت تراکنش
            $transaction->update([
                'status'                  => 'success',
                'torobpay_transaction_id' => $torobpayTransactionId,
                'torobpay_status'         => TorobpayStatusEnum::ONGOING->value,
            ]);

            // آپدیت سفارش
            $transaction->order->update(['status' => 'paid', 'shipping_status' => 'processing']);
            foreach ($transaction->order->items as $item) {
                if ($item->variant_id && $item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            DB::commit();

            Log::info('TorobPay: payment verified and settled', [
                'transaction_id'          => $transaction->id,
                'torobpay_transaction_id' => $torobpayTransactionId,
            ]);

            return $this->successRedirect($transaction);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TorobPay: verify/settle failed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);

            // اگر verify فیل شد revert میزنیم تا پول کاربر برگرده
            $this->tryRevert($transaction);

            return $this->failRedirect();
        }
    }

    // ─────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────

    /**
     * تلاش برای revert - اگر verify فیل شد
     * خطاش رو می‌بلعیم تا flow اصلی خراب نشه
     */
    private function tryRevert(Transaction $transaction): void
    {
        try {
            $this->torobpay->revert($transaction->payment_token);
            $this->markTransactionFailed($transaction, TorobpayStatusEnum::CANCELLED->value);
        } catch (\Exception $e) {
            // revert فیل شد - احتمالاً بیشتر از ۳۰ دقیقه گذشته یا وضعیت اشتباهه
            // فقط لاگ میکنیم، تیم باید دستی بررسی کنه
            $this->markTransactionFailed($transaction, TorobpayStatusEnum::FAILED->value);
            Log::error('TorobPay: revert also failed - manual review needed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    private function markTransactionFailed(Transaction $transaction, string $torobpayStatus): void
    {
        $transaction->update([
            'status'          => 'failed',
            'torobpay_status' => $torobpayStatus,
        ]);
    }

    private function successRedirect(Transaction $transaction)
    {
        return redirect()->route('torobpay.result')->with('torobpay_result', [
            'success'        => true,
            'order_number'   => $transaction->order->order_number,
            'amount'         => $transaction->amount,
            'total_price'    => $transaction->order->total_price,
            'shipping_price' => $transaction->order->shipping_price,
        ]);
    }

    private function failRedirect()
    {
        return redirect()->route('torobpay.result')->with('torobpay_result', [
            'success' => false,
        ]);
    }
}