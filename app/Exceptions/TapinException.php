<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * خطای اختصاصی تاپین.
 *
 * شامل پیام فارسی تاپین + HTTP status + بدنهٔ پاسخ
 * تا در Job و پنل ادمین بتوانیم دقیق نمایش دهیم.
 */
class TapinException extends Exception
{
    protected ?int $httpStatus;
    protected ?array $response;

    public function __construct(
        string $message,
        ?int $httpStatus = null,
        ?array $response = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->httpStatus = $httpStatus;
        $this->response   = $response;
    }

    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
}