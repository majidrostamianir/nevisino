<?php

namespace App\Enums;

enum TorobpayStatusEnum: string
{
    /*
     در صورتی که پس از گذشت ۱۵ دقیقه از ساخته شدن سفارش کاربر وارد درگاه پرداخت نشود، سفارش به صورت خودکار وارد وضعیت FAILED میشود

     در صورتی که سفارشی که وارد وضعیت IPG شده باشد پس از گذشت ۱۰ دقیقه توسط کاربر پرداخت نشود، سفارش به وضعیت FAILED تغییر پیدا میکند

     در صورتی که کاربر پرداخت موفق انجام دهد و برای بازگشت به وبسایت فروشگاه یا وبسایت تربپی با مشکل مواجه شود، سفارش پس از گذشت حداکثر ۱۰ دقیقه به صورت خودکار وارد وضعیت VERIFY_FOR_W میشود

     در صورتی که کاربر با پرداخت ناموفق به وبسایت تربپی هدایت شود سفارش وارد وضعیت FAILED میشود

     همچنین در صورتی که کاربر پس از پرداخت با موفقیت به وبسایت تربپی ریدایرکت شود سفارش بالفاصله وارد وضعیت VERIFY_FOR_W میشود

    */
    case NEW = 'NEW'; // لمس دکمه پرداخت در نویسینو
    case IPG = 'IPG'; // لمس دکمه پرداخت در ترب پی
    case W_FOR_VERIFY = 'W_FOR_VERIFY';
    case W_FOR_SETTLE = 'W_FOR_SETTLE';
    case ONGOING = 'ONGOING';
    case PAID = 'PAID';
    case UPDATED = 'UPDATED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';

    public function toSimpleStatus(): string
    {
        return match ($this) {
            self::NEW, self::IPG, self::W_FOR_VERIFY => 'در انتظار پرداخت',
            self::W_FOR_SETTLE => 'پرداخت تایید شده، در انتظار تسویه',
            self::ONGOING, self::PAID, self::UPDATED => 'پرداخت موفق - تکمیل شده',
            self::FAILED => 'پرداخت ناموفق',
            self::CANCELLED => 'لغو شده',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW, self::IPG, self::W_FOR_VERIFY => 'yellow',
            self::W_FOR_SETTLE => 'blue',
            self::ONGOING, self::PAID, self::UPDATED => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'gray',
        };
    }
}