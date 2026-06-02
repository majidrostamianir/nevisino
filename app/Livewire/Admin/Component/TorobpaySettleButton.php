<?php

namespace App\Livewire\Admin\Component;

use App\Models\Order;
use App\Services\TorobPayService;
use Livewire\Component;

class TorobpaySettleButton extends Component
{
    public $order;
    public $isProcessing = false;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function settle()
    {
        $this->isProcessing = true;

        try {
            $torobpay = new TorobPayService();
            $result = $torobpay->settlePayment($this->order->torobpay_payment_token);

            $this->order->updateTorobpayStatus(
                \App\Enums\TorobpayStatusEnum::ONGOING,
                $result['transactionId']
            );

            session()->flash('success', 'تسویه سفارش با موفقیت انجام شد.');

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تسویه: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        $canSettle = $this->order->canSettle();

        return view('livewire.admin.component.torobpay-settle-button', [
            'canSettle' => $canSettle
        ]);
    }

}
