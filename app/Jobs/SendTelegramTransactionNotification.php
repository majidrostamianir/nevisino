<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendTelegramTransactionNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $transactionId
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $transaction = Transaction::with('order.items.product', 'order.items.variant')
            ->find($this->transactionId);

        if (!$transaction || $transaction->status !== 'success') {
            return;
        }

        $label = match ($transaction->payment_gateway) {
            'torobpay' => 'ترب پی',
            'zibal'    => 'زیبال',
            default    => 'آنلاین',
        };

        $telegram->sendPaymentSuccess($transaction, $label);
    }
}