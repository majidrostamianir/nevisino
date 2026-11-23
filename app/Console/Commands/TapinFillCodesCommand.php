<?php

namespace App\Console\Commands;

use App\Exceptions\TapinException;
use App\Models\City;
use App\Models\Province;
use App\Services\Tapin\TapinClient;
use Illuminate\Console\Command;

class TapinFillCodesCommand extends Command
{
    protected $signature = 'tapin:fill-codes
                            {--force : بازنویسی کدهای موجود}';

    protected $description = 'پر کردن tapin_code استان‌ها و شهرها از API تاپین';

    private const PAGE_SIZE = 100;

    public function handle(TapinClient $client): int
    {
        $force = (bool) $this->option('force');

        // =========================================================
        // ۱) دریافت استان‌ها
        // =========================================================

        $this->info('⏳ دریافت استان‌ها از تاپین...');

        try {
            $response = $client->states();
        } catch (TapinException $e) {
            $this->error('❌ دریافت استان‌ها ناموفق بود: ' . $e->getMessage());

            return self::FAILURE;
        }

        $tapinProvinces = $this->extractList($response);

        if (empty($tapinProvinces)) {
            $this->error('❌ هیچ استانی از تاپین دریافت نشد.');

            return self::FAILURE;
        }

        $this->info('✓ تعداد استان‌های دریافتی: ' . count($tapinProvinces));

        // =========================================================
        // ۲) ساخت نقشه استان‌ها
        // =========================================================

        $tapinProvinceMap = [];
        $ambiguousProvinces = [];

        foreach ($tapinProvinces as $province) {
            $name = $this->normalize($province['title'] ?? '');
            $code = (int) ($province['code'] ?? 0);

            if ($name === '' || $code <= 0) {
                continue;
            }

            if (array_key_exists($name, $tapinProvinceMap)) {
                if ($tapinProvinceMap[$name] !== $code) {
                    $tapinProvinceMap[$name] = null;
                    $ambiguousProvinces[$name] = true;
                }

                continue;
            }

            $tapinProvinceMap[$name] = $code;
        }

        // =========================================================
        // ۳) مچ کردن استان‌ها
        // =========================================================

        $this->newLine();
        $this->info('⏳ مچ کردن استان‌ها...');

        $provinces = Province::query()
            ->when(
                !$force,
                fn ($query) => $query->whereNull('tapin_code')
            )
            ->get();

        $matchedProvinces = 0;
        $unmatchedProvinces = [];
        $safeProvinceIds = [];

        foreach ($provinces as $dbProvince) {
            $name = $this->normalize($dbProvince->name);

            if (
                array_key_exists($name, $tapinProvinceMap)
                && $tapinProvinceMap[$name] !== null
            ) {
                $code = $tapinProvinceMap[$name];

                $dbProvince->tapin_code = $code;
                $dbProvince->save();

                $safeProvinceIds[] = $dbProvince->id;
                $matchedProvinces++;
            } else {
                $unmatchedProvinces[] = [
                    'id' => $dbProvince->id,
                    'name' => $dbProvince->name,
                    'ambiguous' => isset($ambiguousProvinces[$name]),
                ];
            }
        }

        // در حالت عادی، استان‌هایی که قبلاً کد داشته‌اند
        // نیز قابل استفاده هستند.
        if (!$force) {
            $existingProvinceIds = Province::query()
                ->whereNotNull('tapin_code')
                ->where('tapin_code', '>', 0)
                ->pluck('id')
                ->all();

            $safeProvinceIds = array_unique(array_merge(
                $safeProvinceIds,
                $existingProvinceIds
            ));
        }

        $this->info("✓ استان‌های مچ‌شده در این اجرا: {$matchedProvinces}");

        if (!empty($unmatchedProvinces)) {
            $this->warn('⚠ استان‌های مچ‌نشده یا مبهم:');

            foreach ($unmatchedProvinces as $item) {
                $reason = $item['ambiguous']
                    ? 'نام استان در تاپین مبهم است'
                    : 'استان در تاپین پیدا نشد';

                $this->line(
                    "  - {$item['name']} (ID: {$item['id']}) - {$reason}"
                );
            }
        }

        // =========================================================
        // ۴) دریافت تمام شهرهای هر استان
        // =========================================================

        $this->newLine();
        $this->info('⏳ دریافت و مچ کردن شهرها...');

        $provincesWithCode = Province::query()
            ->whereIn('id', $safeProvinceIds)
            ->whereNotNull('tapin_code')
            ->where('tapin_code', '>', 0)
            ->get();

        $bar = $this->output->createProgressBar(
            $provincesWithCode->count()
        );

        $bar->start();

        $totalFetched = 0;
        $totalMatched = 0;
        $totalUnmatched = 0;
        $totalAmbiguous = 0;
        $totalSkipped = 0;

        $unmatchedList = [];
        $provinceReports = [];

        foreach ($provincesWithCode as $dbProvince) {
            $provinceName = $dbProvince->name;
            $stateCode = (int) $dbProvince->tapin_code;

            try {
                // دریافت تمام صفحات شهرهای همین استان
                $tapinCities = $this->fetchAllCities(
                    $client,
                    $stateCode
                );
            } catch (TapinException $e) {
                $provinceReports[] = [
                    'name' => $provinceName,
                    'status' => 'خطای دریافت API',
                    'message' => $e->getMessage(),
                ];

                $totalSkipped++;
                $bar->advance();

                continue;
            }

            $totalFetched += count($tapinCities);

            // =====================================================
            // ساخت نقشه شهرها فقط برای همین استان
            // =====================================================

            $tapinCityMap = [];

            foreach ($tapinCities as $tapinCity) {
                $cityName = $this->normalizeCityName(
                    $tapinCity['title'] ?? '',
                    $provinceName
                );

                $cityCode = (int) ($tapinCity['code'] ?? 0);

                if ($cityName === '' || $cityCode <= 0) {
                    continue;
                }

                if (!array_key_exists($cityName, $tapinCityMap)) {
                    $tapinCityMap[$cityName] = $cityCode;
                } elseif ($tapinCityMap[$cityName] !== $cityCode) {
                    // یک نام با چند کد متفاوت در یک استان
                    // نباید به صورت خودکار مچ شود.

                    $tapinCityMap[$cityName] = null;
                }
            }

            // =====================================================
            // مچ شهرهای دیتابیس
            // =====================================================

            $dbCities = City::query()
                ->where('province_id', $dbProvince->id)
                ->when(
                    !$force,
                    fn ($query) => $query->whereNull('tapin_code')
                )
                ->get();

            $matchedInProvince = 0;
            $unmatchedInProvince = 0;
            $ambiguousInProvince = 0;

            foreach ($dbCities as $dbCity) {
                $cityName = $this->normalizeCityName(
                    $dbCity->name,
                    $provinceName
                );

                if (
                    !array_key_exists($cityName, $tapinCityMap)
                    || $tapinCityMap[$cityName] === null
                ) {
                    if (
                        array_key_exists($cityName, $tapinCityMap)
                        && $tapinCityMap[$cityName] === null
                    ) {
                        $ambiguousInProvince++;
                        $totalAmbiguous++;

                        $unmatchedList[] = [
                            'province' => $provinceName,
                            'city' => $dbCity->name,
                            'id' => $dbCity->id,
                            'reason' => 'نام شهر دارای چند کد در تاپین است',
                        ];
                    } else {
                        $unmatchedInProvince++;
                        $totalUnmatched++;

                        $unmatchedList[] = [
                            'province' => $provinceName,
                            'city' => $dbCity->name,
                            'id' => $dbCity->id,
                            'reason' => 'شهر در تاپین پیدا نشد',
                        ];
                    }

                    continue;
                }

                $code = $tapinCityMap[$cityName];

                // فقط کد مطمئن ثبت می‌شود.
                // در حالت عادی فقط شهرهای بدون کد بررسی می‌شوند.
                $dbCity->tapin_code = $code;
                $dbCity->save();

                $matchedInProvince++;
                $totalMatched++;
            }

            $provinceReports[] = [
                'name' => $provinceName,
                'state_code' => $stateCode,
                'api_cities' => count($tapinCities),
                'database_cities' => $dbCities->count(),
                'matched' => $matchedInProvince,
                'unmatched' => $unmatchedInProvince,
                'ambiguous' => $ambiguousInProvince,
                'status' => 'موفق',
            ];

            $bar->advance();
        }

        $bar->finish();

        // =========================================================
        // ۵) گزارش نهایی
        // =========================================================

        $this->newLine(2);

        $this->info('================ گزارش نهایی ================');

        $this->table(
            ['عنوان', 'تعداد'],
            [
                ['استان‌های مچ‌شده در این اجرا', $matchedProvinces],
                ['استان‌های بررسی‌شده برای شهرها', $provincesWithCode->count()],
                ['کل شهرهای دریافتی از تاپین', $totalFetched],
                ['شهرهای مچ‌شده', $totalMatched],
                ['شهرهای مچ‌نشده', $totalUnmatched],
                ['شهرهای دارای تطبیق مبهم', $totalAmbiguous],
                ['استان‌های دارای خطای دریافت', $totalSkipped],
            ]
        );

        // =========================================================
        // ۶) گزارش تفکیکی استان‌ها
        // =========================================================

        $this->newLine();
        $this->info('گزارش تفکیکی استان‌ها:');

        foreach ($provinceReports as $report) {
            if ($report['status'] !== 'موفق') {
                $this->warn(
                    "{$report['name']} - {$report['status']}: "
                    . $report['message']
                );

                continue;
            }

            $this->line(sprintf(
                '%s | کد: %s | دریافتی: %d | دیتابیس: %d | مچ: %d | ناموفق: %d | مبهم: %d',
                $report['name'],
                $report['state_code'],
                $report['api_cities'],
                $report['database_cities'],
                $report['matched'],
                $report['unmatched'],
                $report['ambiguous']
            ));
        }

        // =========================================================
        // ۷) نمایش شهرهای مچ‌نشده
        // =========================================================

        if (!empty($unmatchedList)) {
            $this->newLine();
            $this->warn(
                'شهرهای مچ‌نشده یا مبهم (حداکثر ۵۰ مورد):'
            );

            foreach (array_slice($unmatchedList, 0, 50) as $item) {
                $this->line(
                    "  - {$item['province']} / {$item['city']} "
                    . "(ID: {$item['id']}) - {$item['reason']}"
                );
            }

            if (count($unmatchedList) > 50) {
                $this->line(
                    '... و ' . (count($unmatchedList) - 50)
                    . ' مورد دیگر'
                );
            }
        }

        // =====================================================
        // ۸) بررسی کدهای بزرگ‌تر از SMALLINT
        // =====================================================

        $bigCodes = City::query()
            ->whereNotNull('tapin_code')
            ->where('tapin_code', '>', 65535)
            ->count();

        if ($bigCodes > 0) {
            $this->newLine();

            $this->warn(
                "⚠ تعداد {$bigCodes} شهر دارای کد بزرگ‌تر از 65535 هستند."
            );

            $this->warn(
                'ستون tapin_code باید از نوع INT UNSIGNED باشد.'
            );
        }

        $this->newLine();
        $this->info('✅ عملیات پایان یافت.');

        return self::SUCCESS;
    }

    // =============================================================
    // دریافت تمام صفحات شهرهای یک استان
    // =============================================================

    private function fetchAllCities(
        TapinClient $client,
        int $stateCode
    ): array {
        $allCities = [];

        $page = 1;
        $receivedCount = 0;
        $totalCount = null;

        do {
            $response = $client->cities([
                'count' => self::PAGE_SIZE,
                'page' => $page,
                'state_code' => $stateCode,
            ]);

            $cities = $this->extractList($response);

            if ($page === 1) {
                $apiTotal = data_get(
                    $response,
                    'entries.total_count'
                );

                $totalCount = is_numeric($apiTotal)
                    ? (int) $apiTotal
                    : null;
            }

            if (empty($cities)) {
                if ($totalCount !== null && $receivedCount < $totalCount) {
                    throw new TapinException(
                        "دریافت شهرهای استان با کد {$stateCode} ناقص بود. "
                        . "دریافتی: {$receivedCount} از {$totalCount}"
                    );
                }

                break;
            }

            $receivedCount += count($cities);

            foreach ($cities as $city) {
                $code = (int) ($city['code'] ?? 0);

                if ($code <= 0) {
                    continue;
                }

                // جلوگیری از ثبت تکراری یک کد
                $allCities[$code] = $city;
            }

            if ($totalCount !== null) {
                if ($receivedCount >= $totalCount) {
                    break;
                }
            } else {
                if (count($cities) < self::PAGE_SIZE) {
                    break;
                }
            }

            $page++;

            // محافظ در برابر پاسخ غیرعادی API
            if ($page > 1000) {
                throw new TapinException(
                    'تعداد صفحات دریافتی از حد مجاز بیشتر شد.'
                );
            }
        } while (true);

        if (
            $totalCount !== null
            && $receivedCount < $totalCount
        ) {
            throw new TapinException(
                "دریافت شهرهای استان با کد {$stateCode} ناقص بود. "
                . "دریافتی: {$receivedCount} از {$totalCount}"
            );
        }

        return array_values($allCities);
    }

    // =============================================================
    // استخراج لیست از پاسخ API
    // =============================================================

    private function extractList(array $response): array
    {
        $entries = $response['entries'] ?? [];

        if (!is_array($entries)) {
            return [];
        }

        if (
            isset($entries['list'])
            && is_array($entries['list'])
        ) {
            return $entries['list'];
        }

        if (isset($entries[0]) || $entries === []) {
            return $entries;
        }

        if (isset($entries['code'])) {
            return [$entries];
        }

        return [];
    }

    // =============================================================
    // نرمال‌سازی نام شهر با حذف پسوند دقیق استان
    // =============================================================

    private function normalizeCityName(
        ?string $cityName,
        ?string $provinceName
    ): string {
        $cityName = $this->normalize($cityName);
        $provinceName = $this->normalize($provinceName);

        if ($cityName === '') {
            return '';
        }

        // فقط پسوندی حذف می‌شود که دقیقاً نام همین استان باشد.
        // مثال:
        // حسن آباد-تهران => حسنآباد
        // صالح آباد-تهران => صالحآباد

        if ($provinceName !== '') {
            $suffix = '-' . $provinceName;

            if (str_ends_with($cityName, $suffix)) {
                $cityName = substr(
                    $cityName,
                    0,
                    -strlen($suffix)
                );
            }
        }

        return $cityName;
    }

    // =============================================================
    // نرمال‌سازی نام‌های فارسی
    // =============================================================

    private function normalize(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        // حذف کاراکترهای نامرئی
        $text = str_replace(
            [
                "\u{200C}",
                "\u{200D}",
                "\u{200B}",
                "\u{FEFF}",
            ],
            '',
            $text
        );

        // یکسان‌سازی حروف عربی و فارسی
        $text = str_replace(
            [
                'ي',
                'ك',
                'ة',
                'ۀ',
                'أ',
                'إ',
                'آ',
                'ٱ',
                'ؤ',
                'ئ',
            ],
            [
                'ی',
                'ک',
                'ه',
                'ه',
                'ا',
                'ا',
                'ا',
                'ا',
                'و',
                'ی',
            ],
            $text
        );

        // حذف فاصله و خطوط جدید
        $text = preg_replace('/\s+/u', '', $text);

        return mb_strtolower($text, 'UTF-8');
    }
}
