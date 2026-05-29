<?php

namespace App\Livewire\Payment;

use App\Models\Transaction;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;


class Callback extends Component
{
    public string $message;
    public $data = null;

    public function mount(Request $request)
    {

        $authority = $request->query('trackId', '');

        try {
            $trans = Transaction::query()->where('authority', $authority);

            $receipt = Payment::amount($trans->first()->amount)->transactionId($authority)->verify();
            $this->message = 'پرداخت با موفقیت انجام شد' . PHP_EOL . 'شناسه مرجع: ' . $receipt->getReferenceId();

            if ($trans->exists()) {
                $transaction = $trans->first();
                $transaction->update(['status' => "success"]);
                $transaction->order()->update(['status' => "paid", 'shipping_status' => 'processing']);
                $this->data['order_number'] = $transaction->order->order_number;
                $this->data['authority'] = $transaction->authority;
                $this->data['total_price'] = $transaction->order->total_price;
                $this->data['shipping_price'] = $transaction->order->shipping_price;
                $this->data['amount'] = $transaction->amount;

                foreach ($transaction->order->items as $item) {

                    if ($item->variant_id && $item->variant) {
                        $item->variant->decrement('stock', $item->quantity);
                    } else {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
                $this->sendVerifyOrderSms($this->data['order_number']);
            } else {
                abort(402, 'تراکنش نامعتبر');
            }
        } catch (InvalidPaymentException $exception) {
            $this->message = $exception->getMessage();
            $this->message = $this->message . "کد پیگیری: " . PHP_EOL . $authority;
        } catch (\Throwable $e) {
            $this->message = "خطای غیرمنتظره:" . PHP_EOL . $e->getMessage();
        }
    }

    public function sendVerifyOrderSms($order): void
    {
        $link = 'https://nevisino.ir/dashboard/order?open=' . $order;
        $username = '09169889759';
        $password = 'Faraz@1920115072';
        $from = '3000505';
        $pattern_code = 'yhvz50h4nox3dq4';
        $to = array('98' . substr(\Auth::user()->mobile, 1));
        $input_data = array('link' => $link);

        $url = "https://ippanel.com/patterns/pattern?username=" . $username . "&password=" .
            urlencode($password) . "&from=$from&to=" . json_encode($to) . "&input_data=" . urlencode(json_encode($input_data)) .
            "&pattern_code=$pattern_code";
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        curl_exec($handler);
        curl_close($handler);
    }

    public function render()
    {
        return view('livewire.payment.callback');
    }
}
