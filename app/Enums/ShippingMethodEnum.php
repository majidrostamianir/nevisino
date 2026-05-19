<?php

namespace App\Enums;

enum ShippingMethodEnum: string
{
//    case POST_COD = 'post_cod';      // پست - پرداخت آنلاین
//    case POST_CASH = 'post_cash';    // پست - پرداخت در محل
//    case TIPAX_COD = 'tipax_cod';    // تیپاکس - پرداخت آنلاین
//    case TIPAX_CASH = 'tipax_cash';  // تیپاکس - پرداخت در محل
//
//    /**
//     * عنوان فارسی متد ارسال
//     */
//    public function label(): string
//    {
//        return match ($this) {
//            self::POST_COD => 'پست پیشتاز - پس‌کرایه',
//            self::POST_CASH => 'پست پیشتاز -پیش‌کرایه',
//            self::TIPAX_COD => 'تیپاکس پس‌کرایه',
//            self::TIPAX_CASH => 'تیپاکس - پیش‌کرایه',
//        };
//    }
//
//    /**
//     * آیا پرداخت در محل است؟
//     */
//    public function isCashOnDelivery(): bool
//    {
//        return in_array($this, [self::POST_CASH, self::TIPAX_CASH]);
//    }
//
//    /**
//     * آیا آنلاین است؟
//     */
//    public function isOnline(): bool
//    {
//        return in_array($this, [self::POST_COD, self::TIPAX_COD]);
//    }
//
//    /**
//     * نام شرکت حمل و نقل
//     */
//    public function carrier(): string
//    {
//        return match ($this) {
//            self::POST_COD, self::POST_CASH => 'post',
//            self::TIPAX_COD, self::TIPAX_CASH => 'tipax',
//        };
//    }
//
//    /**
//     * گرفتن همه مقادیر برای validation
//     */
//    public static function values(): array
//    {
//        return array_column(self::cases(), 'value');
//    }
//
//    /**
//     * گرفتن همه عنوان‌ها برای dropdown
//     */
//    public static function options(): array
//    {
//        return collect(self::cases())->mapWithKeys(fn($case) => [
//            $case->value => $case->label()
//        ])->toArray();
//    }
}