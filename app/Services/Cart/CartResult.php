<?php

namespace App\Services\Cart;

final class CartResult
{
    private function __construct(
        public readonly string $message,
        public readonly string $type,
    ) {
    }

    public static function success(string $message): self
    {
        return new self($message, 'success');
    }

    public static function warning(string $message): self
    {
        return new self($message, 'warning');
    }

    public static function error(string $message): self
    {
        return new self($message, 'error');
    }
}