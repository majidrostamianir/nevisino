<?php

namespace App\Http\Controllers;

use App\Services\TorobPayService;
use App\Enums\TorobpayStatusEnum;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TorobPayController extends Controller
{
    protected $torobpay;

    public function __construct()
    {
        $this->torobpay = new TorobPayService();
    }

    /**
     * شروع فرآیند پرداخت با ترب پی
     */
    public function initiate(Request $request)
    {
        try {

            $orderId = session('torobpay_order_id');

            if (!$orderId) {
                return redirect()->route('checkout')->with('error', 'اطلاعات سفارش یافت نشد');
            }

            $order = Order::find($orderId);

            if (!$order) {
                return redirect()->route('checkout')->with('error', 'سفارش یافت نشد');
            }


            // 2. ساخت ساختار cartList طبق مستندات ترب پی
            $cartList = $this->buildCartList($order);

            // 3. آماده سازی داده‌ها برای درخواست به API token
            $paymentData = [
                'amount' => $order->amount, // مبلغ به ریال
                'discountAmount' => $order->discount_amount ?? 0,
                'externalSourceAmount' => 0,
                'mobile' => $order->mobile ?? auth()->user()?->mobile,
                'paymentMethodTypeDto' => 'ONLINE_CREDIT',
                'returnURL' => route('torobpay.callback'),
                'transactionId' => $order->id . '_' . time(),
                'cartList' => $cartList,
                'address' => $order->address,
                'postalCode' => $order->postal_code,
                'customer_full_name' => $order->customer_full_name,
                'city' => $order->city,
                'province' => $order->province,
                'registration_phone_number' => $order->user?->mobile ?? $order->mobile,
            ];

            // 4. ارسال درخواست به ترب پی
            $result = $this->torobpay->createPaymentToken($paymentData);

            // 5. ذخیره paymentToken در دیتابیس
            $order->torobpay_payment_token = $result['paymentToken'];
            $order->torobpay_status = TorobpayStatusEnum::NEW;
            $order->torobpay_amount = $paymentData['amount'];
            $order->save();

            // 6. هدایت کاربر به صفحه پرداخت ترب پی
            return redirect()->away($result['paymentPageUrl']);

        } catch (\Exception $e) {
            Log::error('TorobPay Initiate Error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'خطا در اتصال به درگاه پرداخت: ' . $e->getMessage());
        }
    }

    /**
     * ساخت cartList طبق فرمت مورد نیاز ترب پی
     */
    private function buildCartList($order)
    {
        $cartItems = [];

        foreach ($order->items as $item) {
            $cartItems[] = [
                'id' => (string) $item->product_id,
                'name' => $item->product_name,
                'count' => $item->quantity,
                'amount' => $item->price,
                'category' => $item->category ?? 'general',
                'commissionType' => 0,
            ];
        }

        return [
            [
                'cartId' => (string) $order->id,
                'totalAmount' => $order->total_amount,
                'taxAmount' => $order->tax_amount ?? 0,
                'shippingAmount' => $order->shipping_amount ?? 0,
                'isTaxIncluded' => true,
                'isShipmentIncluded' => true,
                'cartItems' => $cartItems,
            ]
        ];
    }
    /**
     * بازگشت از درگاه ترب پی
     */
    public function callback(Request $request)
    {
        // دریافت پارامترهایی که ترب پی برمیگردونه
        $transactionId = $request->input('transactionId');
        $state = $request->input('state'); // OK یا FAILED
        $amount = $request->input('amount');

        Log::info('TorobPay Callback Received', [
            'transactionId' => $transactionId,
            'state' => $state,
            'amount' => $amount,
            'all' => $request->all()
        ]);

        // پیدا کردن سفارش بر اساس transactionId (که ما فرستادیم)
        $orderId = explode('_', $transactionId)[0] ?? null;
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('checkout')->with('error', 'سفارش یافت نشد');
        }

        if ($state === 'OK') {
            // پرداخت موفق - وریفای می‌کنیم
            return redirect()->route('torobpay.verify', ['paymentToken' => $order->torobpay_payment_token]);
        } else {
            // پرداخت ناموفق
            $order->updateTorobpayStatus(TorobpayStatusEnum::FAILED);
            return redirect()->route('checkout')->with('error', 'پرداخت ناموفق بود. لطفاً مجدداً تلاش کنید.');
        }
    }
    /**
     * تایید پرداخت بعد از بازگشت از درگاه
     */
    public function verify(Request $request)
    {
        $paymentToken = $request->query('paymentToken');

        if (!$paymentToken) {
            return redirect()->route('checkout')->with('error', 'اطلاعات پرداخت یافت نشد');
        }

        try {
            // پیدا کردن سفارش
            $order = Order::where('torobpay_payment_token', $paymentToken)->first();

            if (!$order) {
                return redirect()->route('checkout')->with('error', 'سفارش یافت نشد');
            }

            // تایید پرداخت (verify)
            $result = $this->torobpay->verifyPayment($paymentToken);

            // به‌روزرسانی وضعیت سفارش
            $order->updateTorobpayStatus(TorobpayStatusEnum::W_FOR_SETTLE, $result['transactionId']);

            // نمایش پیام موفقیت به کاربر
            return redirect()->route('order.success', $order->id)
                ->with('success', 'پرداخت با موفقیت انجام شد. سفارش شما در انتظار تسویه نهایی است.');

        } catch (\Exception $e) {
            Log::error('TorobPay Verify Error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'خطا در تایید پرداخت: ' . $e->getMessage());
        }
    }
    /**
     * تسویه نهایی سفارش (بعد از تأیید نهایی توسط مرچنت)
     */
    public function settle(Request $request)
    {
        $paymentToken = $request->input('payment_token');

        if (!$paymentToken) {
            if ($request->ajax()) {
                return response()->json(['error' => 'payment_token required'], 400);
            }
            return back()->with('error', 'پارامتر payment_token الزامی است');
        }

        try {
            // پیدا کردن سفارش
            $order = Order::where('torobpay_payment_token', $paymentToken)->first();

            if (!$order) {
                $message = 'سفارش یافت نشد';
                return $request->ajax()
                    ? response()->json(['error' => $message], 404)
                    : back()->with('error', $message);
            }

            // چک کن که وضعیت قابل تسویه باشد
            if (!$order->canSettle()) {
                $message = 'وضعیت سفارش برای تسویه مناسب نیست. وضعیت فعلی: ' . $order->torobpay_status?->value;
                return $request->ajax()
                    ? response()->json(['error' => $message], 400)
                    : back()->with('error', $message);
            }

            // فراخوانی سرویس settle
            $result = $this->torobpay->settlePayment($paymentToken);

            // به‌روزرسانی وضعیت سفارش
            $order->updateTorobpayStatus(TorobpayStatusEnum::ONGOING, $result['transactionId']);

            $successMessage = 'تسویه سفارش با موفقیت انجام شد. وضعیت سفارش: در حال انجام';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'transaction_id' => $result['transactionId'],
                    'status' => $order->torobpay_status->value
                ]);
            }

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('TorobPay Settle Error: ' . $e->getMessage());

            $errorMessage = 'خطا در تسویه: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
    /**
     * برگشت آنی سفارش (فقط کمتر از 30 دقیقه و وضعیت W_FOR_VERIFY)
     */
    public function revert(Request $request)
    {
        $paymentToken = $request->input('payment_token');

        if (!$paymentToken) {
            if ($request->ajax()) {
                return response()->json(['error' => 'payment_token required'], 400);
            }
            return back()->with('error', 'پارامتر payment_token الزامی است');
        }

        try {
            // پیدا کردن سفارش
            $order = Order::where('torobpay_payment_token', $paymentToken)->first();

            if (!$order) {
                $message = 'سفارش یافت نشد';
                return $this->handleResponse($request, $message, null, 404);
            }

            // بررسی وضعیت
            if ($order->torobpay_status !== \App\Enums\TorobpayStatusEnum::W_FOR_VERIFY) {
                $message = 'فقط سفارش‌های در انتظار تایید (W_FOR_VERIFY) قابل برگشت آنی هستند. وضعیت فعلی: ' . $order->torobpay_status?->value;
                return $this->handleResponse($request, $message, null, 400);
            }

            // فراخوانی سرویس revert
            $result = $this->torobpay->revertPayment($paymentToken);

            // به‌روزرسانی وضعیت سفارش
            $order->updateTorobpayStatus(\App\Enums\TorobpayStatusEnum::CANCELLED, $result['transactionId']);

            $successMessage = 'سفارش با موفقیت لغو و مبلغ به صورت آنی به کاربر برگشت داده شد.';

            return $this->handleResponse($request, $successMessage, $result, 200, true);

        } catch (\Exception $e) {
            Log::error('TorobPay Revert Error: ' . $e->getMessage());
            return $this->handleResponse($request, 'خطا در برگشت: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * لغو سفارش (با بازپرداخت از طریق پایا در صورت نیاز)
     */
    public function cancel(Request $request)
    {
        $paymentToken = $request->input('payment_token');

        if (!$paymentToken) {
            if ($request->ajax()) {
                return response()->json(['error' => 'payment_token required'], 400);
            }
            return back()->with('error', 'پارامتر payment_token الزامی است');
        }

        try {
            // پیدا کردن سفارش
            $order = Order::where('torobpay_payment_token', $paymentToken)->first();

            if (!$order) {
                $message = 'سفارش یافت نشد';
                return $this->handleResponse($request, $message, null, 404);
            }

            // وضعیت‌های قابل لغو
            $cancellableStatuses = [
                \App\Enums\TorobpayStatusEnum::W_FOR_VERIFY,
                \App\Enums\TorobpayStatusEnum::W_FOR_SETTLE,
                \App\Enums\TorobpayStatusEnum::ONGOING,
            ];

            if (!in_array($order->torobpay_status, $cancellableStatuses)) {
                $message = 'وضعیت سفارش برای لغو مناسب نیست. وضعیت فعلی: ' . $order->torobpay_status?->value;
                return $this->handleResponse($request, $message, null, 400);
            }

            // فراخوانی سرویس cancel
            $result = $this->torobpay->cancelPayment($paymentToken);

            // به‌روزرسانی وضعیت سفارش
            $order->updateTorobpayStatus(\App\Enums\TorobpayStatusEnum::CANCELLED, $result['transactionId']);

            $successMessage = 'سفارش با موفقیت لغو شد. مبلغ به کاربر برگشت داده خواهد شد.';

            return $this->handleResponse($request, $successMessage, $result, 200, true);

        } catch (\Exception $e) {
            Log::error('TorobPay Cancel Error: ' . $e->getMessage());
            return $this->handleResponse($request, 'خطا در لغو: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * متد کمکی برای پاسخ‌دهی یکسان به درخواست‌های Ajax و عادی
     */
    private function handleResponse($request, $message, $data = null, $statusCode = 200, $success = false)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
                'data' => $data
            ], $statusCode);
        }

        if ($success) {
            return redirect()->route('admin.orders.show', $data['transactionId'] ?? null)
                ->with('success', $message);
        }

        return back()->with('error', $message);
    }
    /**
     * دریافت وضعیت سفارش از ترب پی
     */
    public function getStatus(Request $request)
    {
        $paymentToken = $request->input('payment_token');

        if (!$paymentToken) {
            if ($request->ajax()) {
                return response()->json(['error' => 'payment_token required'], 400);
            }
            return back()->with('error', 'پارامتر payment_token الزامی است');
        }

        try {
            // پیدا کردن سفارش در دیتابیس خودمون
            $order = Order::where('torobpay_payment_token', $paymentToken)->first();

            if (!$order) {
                $message = 'سفارش یافت نشد';
                return $this->handleResponse($request, $message, null, 404);
            }

            // گرفتن وضعیت از ترب پی
            $result = $this->torobpay->getPaymentStatus($paymentToken);

            // همگام‌سازی وضعیت دیتابیس با وضعیت ترب پی (اختیاری)
            $this->syncStatusWithTorobpay($order, $result);

            $statusMessage = $this->getStatusMessage($result['status']);

            $responseData = [
                'status' => $result['status'],
                'status_fa' => $statusMessage,
                'amount' => $result['amount'],
                'transaction_id' => $result['transactionId'] ?? $order->torobpay_transaction_id,
                'local_status' => $order->torobpay_status?->value,
                'local_status_fa' => $order->torobpay_status?->toSimpleStatus(),
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $responseData
                ]);
            }

            return view('torobpay.status', $responseData);

        } catch (\Exception $e) {
            Log::error('TorobPay GetStatus Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', 'خطا در دریافت وضعیت: ' . $e->getMessage());
        }
    }

    /**
     * همگام‌سازی وضعیت دیتابیس با ترب پی
     */
    private function syncStatusWithTorobpay($order, $torobpayStatus)
    {
        // نگاشت وضعیت‌های ترب پی به وضعیت‌های دیتابیس ما
        $statusMapping = [
            'PENDING' => \App\Enums\TorobpayStatusEnum::W_FOR_VERIFY,
            'VERIFY' => \App\Enums\TorobpayStatusEnum::W_FOR_SETTLE,
            'SETTLE' => \App\Enums\TorobpayStatusEnum::ONGOING,
            'REVERT' => \App\Enums\TorobpayStatusEnum::CANCELLED,
        ];

        $mappedStatus = $statusMapping[$torobpayStatus['status']] ?? null;

        if ($mappedStatus && $order->torobpay_status !== $mappedStatus) {
            $order->torobpay_status = $mappedStatus;
            $order->save();
            Log::info('TorobPay status synced', [
                'order_id' => $order->id,
                'old_status' => $order->torobpay_status,
                'new_status' => $mappedStatus,
                'torobpay_status' => $torobpayStatus['status']
            ]);
        }
    }

    /**
     * دریافت پیام فارسی وضعیت
     */
    private function getStatusMessage($status)
    {
        return match($status) {
            'PENDING' => 'در انتظار پرداخت',
            'VERIFY' => 'پرداخت تایید شده، در انتظار تسویه',
            'SETTLE' => 'تسویه شده - در حال انجام',
            'REVERT' => 'برگشت خورده',
            default => 'نامشخص',
        };
    }
    /**
     * بروزرسانی سفارش (فقط کاهش مبلغ)
     */
    public function update(Request $request)
    {
        $request->validate([
            'payment_token' => 'required|string',
            'new_amount' => 'required|integer|min:200000',
            'items' => 'nullable|array',
        ]);

        try {
            // پیدا کردن سفارش
            $order = Order::where('torobpay_payment_token', $request->payment_token)->first();

            if (!$order) {
                return response()->json(['error' => 'سفارش یافت نشد'], 404);
            }

            // بررسی وضعیت (فقط ONGOING قابل بروزرسانیه)
            if ($order->torobpay_status !== \App\Enums\TorobpayStatusEnum::ONGOING) {
                return response()->json([
                    'error' => 'فقط سفارش‌های با وضعیت ONGOING قابل بروزرسانی هستند',
                    'current_status' => $order->torobpay_status?->value
                ], 400);
            }

            // بررسی اینکه مبلغ جدید کمتر از مبلغ قبلی باشه
            if ($request->new_amount >= $order->torobpay_amount) {
                return response()->json([
                    'error' => 'مبلغ جدید باید کمتر از مبلغ قبلی باشد',
                    'current_amount' => $order->torobpay_amount,
                    'new_amount' => $request->new_amount
                ], 400);
            }

            // آماده‌سازی داده‌های بروزرسانی
            $updateData = [
                'paymentToken' => $request->payment_token,
                'amount' => $request->new_amount,
                'discountAmount' => $request->discount_amount ?? 0,
                'externalSourceAmount' => 0,
                'paymentMethodTypeDto' => 'ONLINE_CREDIT',
                'cartList' => $request->items ? $this->buildCartListFromArray($request->items) : $order->cart_list,
            ];

            // فراخوانی سرویس update
            $result = $this->torobpay->updatePayment($updateData);

            // به‌روزرسانی دیتابیس
            $order->torobpay_amount = $request->new_amount;
            $order->torobpay_status = \App\Enums\TorobpayStatusEnum::UPDATED;
            $order->save();

            // محاسبه مبلغ برگشتی به کاربر
            $refundAmount = $order->torobpay_amount - $request->new_amount;

            Log::info('TorobPay Order Updated', [
                'order_id' => $order->id,
                'old_amount' => $order->torobpay_amount,
                'new_amount' => $request->new_amount,
                'refund_amount' => $refundAmount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'سفارش با موفقیت بروزرسانی شد',
                'data' => [
                    'transaction_id' => $result['transactionId'],
                    'old_amount' => $order->torobpay_amount,
                    'new_amount' => $request->new_amount,
                    'refund_amount' => $refundAmount,
                    'refund_message' => "مبلغ " . number_format($refundAmount) . " ریال به کاربر برگشت داده خواهد شد"
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('TorobPay Update Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ساخت cartList از آرایه برای آپدیت
     */
    private function buildCartListFromArray($items)
    {
        return [[
            'cartId' => (string) time(),
            'totalAmount' => array_sum(array_column($items, 'amount')),
            'taxAmount' => 0,
            'shippingAmount' => 0,
            'isTaxIncluded' => true,
            'isShipmentIncluded' => true,
            'cartItems' => array_map(function($item) {
                return [
                    'id' => (string) $item['id'],
                    'name' => $item['name'],
                    'count' => $item['count'],
                    'amount' => $item['amount'],
                    'category' => $item['category'] ?? 'general',
                    'commissionType' => 0,
                ];
            }, $items),
        ]];
    }
}