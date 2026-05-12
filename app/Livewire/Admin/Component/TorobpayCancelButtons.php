<?php

namespace App\Livewire\Admin\Component;

use App\Enums\TorobpayStatusEnum;
use App\Models\Order;
use App\Services\TorobPayService;
use Livewire\Component;

class TorobpayCancelButtons extends Component
{
    public $order;
    public $isProcessing = false;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function revert()
    {
        $this->isProcessing = true;

        try {
            $torobpay = new TorobPayService();
            $result = $torobpay->revertPayment($this->order->torobpay_payment_token);

            $this->order->updateTorobpayStatus(TorobpayStatusEnum::CANCELLED, $result['transactionId']);

            session()->flash('success', 'سفارش با موفقیت لغو و مبلغ به صورت آنی برگشت داده شد.');

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در برگشت آنی: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function cancel()
    {
        $this->isProcessing = true;

        try {
            $torobpay = new TorobPayService();
            $result = $torobpay->cancelPayment($this->order->torobpay_payment_token);

            $this->order->updateTorobpayStatus(TorobpayStatusEnum::CANCELLED, $result['transactionId']);

            session()->flash('success', 'سفارش با موفقیت لغو شد. مبلغ به کاربر برگشت داده خواهد شد.');

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در لغو سفارش: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $canRevert = $this->order->torobpay_status === TorobpayStatusEnum::W_FOR_VERIFY;

        $cancellableStatuses = [
            TorobpayStatusEnum::W_FOR_VERIFY,
            TorobpayStatusEnum::W_FOR_SETTLE,
            TorobpayStatusEnum::ONGOING,
        ];
        $canCancel = in_array($this->order->torobpay_status, $cancellableStatuses);

        return view('livewire.admin.component.torobpay-cancel-buttons', [
            'canRevert' => $canRevert,
            'canCancel' => $canCancel
        ]);
    }
}
