<?php

if (!function_exists('english_to_persian_num')) {
    /**
     * تبدیل اعداد انگلیسی به فارسی
     */
    function english_to_persian_num($number): array|string
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return str_replace($english, $persian, $number);
    }
}

if (!function_exists('persian_to_english_num')) {
    /**
     * تبدیل اعداد فارسی/عربی به انگلیسی
     */
    function persian_to_english_num($number): array|string
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $number = str_replace($persian, $english, $number);
        return str_replace($arabic, $english, $number);
    }
}
