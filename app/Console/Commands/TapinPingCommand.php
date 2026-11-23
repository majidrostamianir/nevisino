<?php

namespace App\Console\Commands;

use App\Exceptions\TapinException;
use App\Services\Tapin\TapinClient;
use Illuminate\Console\Command;

class TapinPingCommand extends Command
{
    protected $signature = 'tapin:ping';
    protected $description = 'تست اتصال و توکن تاپین (دریافت لیست استان‌ها)';

    public function handle(TapinClient $client): int
    {
        if (blank(config('tapin.token'))) {
            $this->error('❌ TAPIN_TOKEN در .env تنظیم نشده است.');
            return self::FAILURE;
        }
        if (blank(config('tapin.shop_id'))) {
            $this->error('❌ TAPIN_SHOP_ID در .env تنظیم نشده است.');
            return self::FAILURE;
        }

        $this->info('⏳ در حال تست اتصال به تاپین...');

        try {
            $response = $client->states();
        } catch (TapinException $e) {
            $this->newLine();
            $this->error('❌ ' . $e->getMessage());
            if ($e->getHttpStatus()) {
                $this->line('   HTTP Status: ' . $e->getHttpStatus());
            }
            return self::FAILURE;
        }

        $states = $response['entries'] ?? [];
        $count  = is_array($states) ? count($states) : 0;

        $this->newLine();
        $this->info("✅ اتصال موفق. {$count} استان دریافت شد.");
        $this->line('   Shop ID: ' . config('tapin.shop_id'));

        if ($count > 0) {
            $this->newLine();
            $this->table(
                ['کد', 'نام استان'],
                collect($states)->take(5)
                    ->map(fn ($s) => [$s['code'] ?? '?', $s['title'] ?? '?'])
                    ->toArray()
            );
            if ($count > 5) {
                $this->line('... و ' . ($count - 5) . ' مورد دیگر');
            }
        }

        return self::SUCCESS;
    }
}