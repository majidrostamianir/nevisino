<?php

return [
    'access_token'  => env('BASALAM_ACCESS_TOKEN'),
    'refresh_token' => env('BASALAM_REFRESH_TOKEN'),
    'vendor_id'     => env('BASALAM_VENDOR_ID'),
    'auto_sync'     => env('BASALAM_AUTO_SYNC', true),
    'default_status' => 2976,
    'brand_attribute_id' => 1751,

    // فاصله بین درخواست‌ها (ثانیه) — قابل تنظیم از .env
    // زیر ۳ نرو؛ لاگ‌ها نشون داد ۲ باعث 429 می‌شه.
    'request_interval' => (int) env('BASALAM_REQUEST_INTERVAL', 5),
];