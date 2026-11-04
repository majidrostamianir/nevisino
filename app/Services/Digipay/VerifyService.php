<?php

namespace App\Services\Digipay;

class VerifyService
{
    public function __construct(
        protected DigipayClient $client
    ) {}

    /**
     * تایید پرداخت
     */
    public function verify(string $trackingCode, string $providerId, int $type = 11): array
    {
        $response = $this->client->post(
            "/purchases/verify?type={$type}",
            [
                'trackingCode' => $trackingCode,
                'providerId'   => $providerId,
            ]
        );

        return $this->client->handle($response);
    }
}