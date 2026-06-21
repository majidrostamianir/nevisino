<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->string('type')->default('integer');
            $table->string('group')->default('shipping');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $settings = [
            // ========== قیمت‌های پایه ==========
            ['key' => 'post_price', 'value' => '190000', 'type' => 'integer', 'group' => 'shipping_prices', 'description' => 'قیمت کرایه پست'],
            ['key' => 'tipax_price', 'value' => '170000', 'type' => 'integer', 'group' => 'shipping_prices', 'description' => 'قیمت کرایه تیپاکس'],

            // ========== قیمت کارتن‌ها ==========
            ['key' => 'box_1', 'value' => '14000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۱'],
            ['key' => 'box_2', 'value' => '25000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۲'],
            ['key' => 'box_3', 'value' => '36000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۳'],
            ['key' => 'box_4', 'value' => '46000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۴'],
            ['key' => 'box_5', 'value' => '60000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۵'],
            ['key' => 'box_6', 'value' => '70000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۶'],
            ['key' => 'box_7', 'value' => '80000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۷'],
            ['key' => 'box_8', 'value' => '140000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۸'],
            ['key' => 'box_9', 'value' => '250000', 'type' => 'integer', 'group' => 'box_prices', 'description' => 'قیمت کارتن سایز ۹'],

            // ========== تنظیمات هزینه کارتن ==========
            ['key' => 'charge_packaging', 'value' => '1', 'type' => 'boolean', 'group' => 'packaging', 'description' => 'آیا هزینه کارتن از مشتری گرفته شود؟'],
            ['key' => 'free_packaging_threshold', 'value' => '0', 'type' => 'integer', 'group' => 'packaging', 'description' => 'از چه مبلغی به بالا، کارتن رایگان باشد؟ (۰ یعنی هرگز)'],

            // ========== تنظیمات هزینه کرایه ==========
            ['key' => 'charge_shipping', 'value' => '1', 'type' => 'boolean', 'group' => 'shipping', 'description' => 'آیا هزینه کرایه از مشتری گرفته شود؟'],
            ['key' => 'free_shipping_threshold', 'value' => '0', 'type' => 'integer', 'group' => 'shipping', 'description' => 'از چه مبلغی به بالا، کرایه رایگان باشد؟ (۰ یعنی هرگز)'],

            // ========== درصد سود ==========
            ['key' => 'profit_percent', 'value' => '20', 'type' => 'integer', 'group' => 'profit', 'description' => 'درصد سود سیستم'],

            // ========== تنظیمات اضافی ==========
            ['key' => 'expire_order_time_minutes', 'value' => '30', 'type' => 'integer', 'group' => 'order', 'description' => 'زمان انقضای سفارش (دقیقه)'],
        ];

        DB::table('settings')->insert($settings);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
