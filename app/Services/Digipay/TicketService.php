<?php

namespace App\Services\Digipay;

class TicketService
{
    public function __construct(
        protected DigipayClient $client
    ) {}

    /**
     * ساخت تیکت خرید (فقط API - بدون ذخیره در DB)
     */
    public function create(array $data): array
    {
        $type = $data['type'] ?? 11;
        unset($data['type']);

        $response = $this->client->post(
            "/tickets/business?type={$type}",
            $data
        );

        return $this->client->handle($response);
    }

    /**
     * ساخت تیکت با درگاه مشخص
     */
    public function createWithPreferredGateway(array $data, int $gateway): array
    {
        $data['additionalInfo'] = array_merge(
            $data['additionalInfo'] ?? [],
            ['preferredGateway' => $gateway]
        );

        return $this->create($data);
    }
}