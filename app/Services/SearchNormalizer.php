<?php

namespace App\Services;

class SearchNormalizer
{
    private static array $synonyms = [
        'مدادرنگی'   => ' مداد رنگی ',
        'مداد رنگی' => ' مدادرنگی ',

        'پاکن'      => ' پاک کن ',
        'پاک کن'    => ' پاکن ',

        'فابرکاستل' => ' فابر کاستل ',
        'فابر کاستل'=> ' فابرکاستل ',

        'کوییلو' => ' کویلو ',
        'کویلو'=> ' کوییلو ',
        'quillo'=> ' کوییلو ',

        'ووک'=> ' وک ',
        'وک'=> ' ووک ',
        'woke'=> ' ووک ',
        'wook'=> ' ووک ',
        'wooke'=> ' ووک ',
        'wok'=> ' ووک ',

        'امکیو'=> ' ام کیو ',
        'ام کیو'=> ' ام کیو ',
        'mq'=> ' ام کیو ',

        'دفتر'=> ' دفتر ',
        'مداد'=> ' مداد ',
        'ماژیک'=> ' ماژیک ',
        'خودکار'=> ' خودکار ',
        'خمیر'=> ' خمیر ',
        'خمیربازی'=> ' خمیر بازی ',
        'چسب'=> ' چسب ',
    ];

    public static function normalize(string $q): string
    {
        $q = trim($q);

        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $q = str_replace($persian, $english, $q);

        return str_replace(['آ','ي','ك'], ['ا','ی','ک'], $q);
    }

    public static function expand(string $q): array
    {
        $q = self::normalize($q);

        $queries = [$q];

        foreach (self::$synonyms as $from => $to) {
            foreach ($queries as $current) {
                if (mb_strpos($current, $from) !== false) {
                    $queries[] = str_replace($from, $to, $current);
                }
            }
        }

        return array_values(array_unique($queries));
    }

}
