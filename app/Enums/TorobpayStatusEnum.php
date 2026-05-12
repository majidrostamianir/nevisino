<?php

namespace App\Enums;

enum TorobpayStatusEnum: string
{
    case NEW = 'NEW';
    case IPG = 'IPG';
    case W_FOR_VERIFY = 'W_FOR_VERIFY';
    case W_FOR_SETTLE = 'W_FOR_SETTLE';
    case ONGOING = 'ONGOING';
    case PAID = 'PAID';
    case UPDATED = 'UPDATED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';

    public function toSimpleStatus(): string
    {
        return match($this) {
            self::NEW, self::IPG, self::W_FOR_VERIFY => 'در انتظار پرداخت',
            self::W_FOR_SETTLE => 'پرداخت تایید شده، در انتظار تسویه',
            self::ONGOING, self::PAID, self::UPDATED => 'پرداخت موفق - تکمیل شده',
            self::FAILED => 'پرداخت ناموفق',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW, self::IPG, self::W_FOR_VERIFY => 'yellow',
            self::W_FOR_SETTLE => 'blue',
            self::ONGOING, self::PAID, self::UPDATED => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'gray',
        };
    }
}