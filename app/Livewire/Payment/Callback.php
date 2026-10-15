<?php

namespace App\Livewire\Payment;

use App\Jobs\SendTelegramTransactionNotification;
use App\Models\Transaction;
use App\Services\ProductService;
use App\Services\SMS;
use Livewire\Component;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

class Callback extends Component
{
    public string $message;
    public $data = null;

    public function mount(Request $request, ProductService $productService, SMS $sms)
    {
        $authority = $request->query('trackId', '');

        if (empty($authority)) {
            abort(402, 'تراکنش نامعتبر');
        }

        // تراکنش رو فقط اگه مال کاربر فعلی باشه می‌گیریم
        $transaction = Transaction::query()
            ->where('authority', $authority)
            ->whereHas('order', fn ($q) => $q->where('user_id', auth()->id()))
            ->first();

        if (!$transaction) {
            abort(402, 'تراکنش نامعتبر');
        }

        // اگه قبلاً پردازش شده، دوباره پردازش نکن
        if ($transaction->status === 'success') {
            $this->message = 'این پرداخت قبلاً با موفقیت ثبت شده است.';
            $this->data = [
                'order_number'   => $transaction->order->order_number ?? null,
                'authority'      => $transaction->authority,
                'total_price'    => $transaction->order->total_price ?? null,
                'shipping_price' => $transaction->order->shipping_price ?? null,
                'amount'         => $transaction->amount,
            ];
            return;
        }

        try {
            $receipt = Payment::amount($transaction->amount)
                ->transactionId($authority)
                ->verify();

            $this->message = 'پرداخت با موفقیت انجام شد' . PHP_EOL
                . 'شناسه مرجع: ' . $receipt->getReferenceId();

            $transaction->update(['status' => 'success']);
            $transaction->order()->update([
                'status'          => 'paid',
                'shipping_status' => 'processing',
            ]);

            $this->data['order_number']   = $transaction->order->order_number;
            $this->data['authority']      = $transaction->authority;
            $this->data['total_price']    = $transaction->order->total_price;
            $this->data['shipping_price'] = $transaction->order->shipping_price;
            $this->data['amount']         = $transaction->amount;

            // کم کردن موجودی همه آیتم‌های سفارش
            $productService->decrementStockForOrder($transaction->order);

            // پیامک به مشتری
            $sms->successPaid(
                orderNumber: $this->data['order_number'],
                mobile:      auth()->user()->mobile,
            );

            // تلگرام به ادمین
            SendTelegramTransactionNotification::dispatch($transaction->id);

        } catch (InvalidPaymentException $exception) {
            $this->message = $exception->getMessage();
            $this->message .= "کد پیگیری: " . PHP_EOL . $authority;
        } catch (\Throwable $e) {
            $this->message = "خطای غیرمنتظره:" . PHP_EOL . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.payment.callback');
    }
}