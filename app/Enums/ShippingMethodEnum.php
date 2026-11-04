<?php

namespace App\Enums;

enum ShippingMethodEnum: string
{
    case POST_COD = 'post_cod';      // پست - پرداخت آنلاین
    case POST_CASH = 'post_cash';    // پست - پرداخت در محل
    case POST_FREE = 'post_free';    // پست - رایگان
    case TIPAX_COD = 'tipax_cod';    // تیپاکس - پرداخت آنلاین
    case TIPAX_CASH = 'tipax_cash';  // تیپاکس - پرداخت در محل
    case TIPAX_FREE = 'tipax_free';  // تیپاکس - رایگان

    /**
     * عنوان فارسی متد ارسال
     */
    public function label(): string
    {
        return match ($this) {
            self::POST_COD => 'پست پیشتاز - پس‌کرایه',
            self::POST_CASH => 'پست پیشتاز - پیش‌کرایه',
            self::POST_FREE => 'پست پیشتاز - رایگان',
            self::TIPAX_COD => 'تیپاکس - پس‌کرایه',
            self::TIPAX_CASH => 'تیپاکس - پیش‌کرایه',
            self::TIPAX_FREE => 'تیپاکس - رایگان',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::POST_CASH, self::POST_COD , self::POST_FREE => 'زمان تحویل: ۲ تا ۵ روز',
            self::TIPAX_CASH, self::TIPAX_COD , self::TIPAX_FREE => 'زمان تحویل: ۱ تا ۳ روز',
        };
    }


    /**
     * گرفتن همه مقادیر برای validation
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * گرفتن همه عنوان‌ها برای dropdown
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }


    /**
     * بررسی اینکه روش ارسال به صورت پرداخت در محل (پیش‌کرایه) است یا خیر
     */
    public function isCashOnDelivery(): bool
    {
        return match ($this) {
            self::POST_CASH, self::TIPAX_CASH => true,
            default => false,
        };
    }

}