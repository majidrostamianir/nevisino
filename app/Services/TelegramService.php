<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $botToken;
    private array $chatIds;

    public function __construct()
    {
        $this->botToken = (string) config('services.telegram.bot_token');
        $this->chatIds  = (array) config('services.telegram.chat_ids');
    }

    /**
     * ارسال پیام به همه Chat IDهای تنظیم‌شده
     */
    public function sendMessage(string $message): void
    {
        if (empty($this->botToken) || empty($this->chatIds)) {
            Log::warning('Telegram bot token or chat ids is not set.');
            return;
        }

        foreach ($this->chatIds as $chatId) {
            try {
                $response = Http::timeout(5)->post(
                    "https://api.telegram.org/bot{$this->botToken}/sendMessage",
                    [
                        'chat_id' => $chatId,
                        'text'    => $message,
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true,
                    ]
                );



            } catch (\Throwable $e) {
                Log::error('Telegram notification failed', [
                    'chat_id' => $chatId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * پیام پرداخت موفق ترب پی
     */
    public function sendTorobPaySuccess(
        Transaction $transaction,
        string $torobpayTransactionId
    ): void {
        $order = $transaction->order;

        $message = "🟢 پرداخت موفق | ترب پی\n\n";

        $message .= " مبلغ تراکنش: " . number_format($transaction->amount) . " تومان\n";

        $message .= " گیرنده: {$order->recipient_name}\n";
        $message .= " موبایل: {$order->recipient_mobile}\n";
        $message .= " آدرس: {$order->province} - {$order->city} - {$order->postal_address}\n\n";

        $message .= "🚚 روش ارسال: {$order->shipping_method->label()}\n";

        $message .= "🛍 محصولات:\n";

        foreach ($order->items as $item) {
            $title = $item->product->title ?? 'محصول';

            $message .= "• {$title}\n";
            $message .= "  تعداد: {$item->quantity} عدد\n";
            $message .= "  قیمت واحد: " . number_format($item->price_snapshot) . " تومان\n";

            if ($item->variant_id && $item->variant) {
                $message .= "  تنوع: {$item->variant->name}\n";
            }

            $message .= "\n";
        }



        if (!empty($order->description)) {
            $message .= "\n📝 توضیحات مشتری:\n";
            $message .= "{$order->description}\n";
        }

        $message .= "\n زمان تراکنش: ";
        $message .= english_to_persian_num(verta($transaction->created_at)->format('Y/m/d H:i:s'));

        $this->sendMessage($message);
    }
}