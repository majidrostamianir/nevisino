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

        switch ($transaction->payment_gateway) {
            case 'torobpay':
                $telegram->sendTorobPaySuccess(
                    $transaction,
                    $transaction->torobpay_transaction_id ?? '-'
                );
                break;
        }
    }
}