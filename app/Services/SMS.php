<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMS
{
    public function successPaid(string $orderNumber, string $mobile): void
    {
        $link = 'https://nevisino.ir/dashboard/order?open=' . $orderNumber;

        $this->sendPattern(
            patternCode: config('services.ippanel.pattern_codes.success_paid'),
            mobile:      $mobile,
            inputData:   ['link' => $link],
        );
    }

    protected function sendPattern(string $patternCode, string $mobile, array $inputData): void
    {
        $to = '98' . substr($mobile, 1);

        try {
            $response = Http::timeout(10)->asForm()->post(
                'https://ippanel.com/patterns/pattern',
                [
                    'username'     => config('services.ippanel.username'),
                    'password'     => config('services.ippanel.password'),
                    'from'         => config('services.ippanel.from'),
                    'to'           => json_encode([$to]),
                    'input_data'   => json_encode($inputData),
                    'pattern_code' => $patternCode,
                ]
            );

            if ($response->failed()) {
                Log::warning('IPPANEL SMS failed', [
                    'mobile'  => $mobile,
                    'pattern' => $patternCode,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('IPPANEL SMS exception', [
                'mobile'  => $mobile,
                'pattern' => $patternCode,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}