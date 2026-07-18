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
            ['key' => 'post_price', 'value' => '190000', 'type' => 'integer', 'group' => 'shipping_prices', 'description' => 'قیمت کرایه پست', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'tipax_price', 'value' => '170000', 'type' => 'integer', 'group' => 'shipping_prices', 'description' => 'قیمت کرایه تیپاکس', 'created_at' => now() , 'updated_at' => now()],

            // ========== قیمت کارتن‌ها ==========
            ['key' => 'packaging_1', 'value' => '14000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۱', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_2', 'value' => '25000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۲', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_3', 'value' => '36000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۳', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_4', 'value' => '46000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۴', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_5', 'value' => '60000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۵', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_6', 'value' => '70000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۶', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_7', 'value' => '80000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۷', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_8', 'value' => '140000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۸', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'packaging_9', 'value' => '250000', 'type' => 'integer', 'group' => 'packaging_price', 'description' => 'قیمت کارتن سایز ۹', 'created_at' => now() , 'updated_at' => now()],

            // ========== تنظیمات هزینه کارتن ==========
            ['key' => 'charge_packaging', 'value' => '1', 'type' => 'boolean', 'group' => 'packaging', 'description' => 'آیا هزینه کارتن از مشتری گرفته شود؟', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'free_packaging_threshold', 'value' => '0', 'type' => 'integer', 'group' => 'packaging', 'description' => 'از چه مبلغی به بالا، کارتن رایگان باشد؟ (۰ یعنی هرگز)', 'created_at' => now() , 'updated_at' => now()],

            // ========== تنظیمات هزینه کرایه ==========
            ['key' => 'charge_shipping', 'value' => '1', 'type' => 'boolean', 'group' => 'shipping', 'description' => 'آیا هزینه کرایه از مشتری گرفته شود؟', 'created_at' => now() , 'updated_at' => now()],
            ['key' => 'free_shipping_threshold', 'value' => '0', 'type' => 'integer', 'group' => 'shipping', 'description' => 'از چه مبلغی به بالا، کرایه رایگان باشد؟ (۰ یعنی هرگز)', 'created_at' => now() , 'updated_at' => now()],

            // ========== درصد سود ==========
            ['key' => 'profit_percent', 'value' => '20', 'type' => 'integer', 'group' => 'profit', 'description' => 'درصد سود سیستم', 'created_at' => now() , 'updated_at' => now()],

            // ========== تنظیمات اضافی ==========
            ['key' => 'expire_order_time_minutes', 'value' => '30', 'type' => 'integer', 'group' => 'order', 'description' => 'زمان انقضای سفارش (دقیقه)'  , 'created_at' => now() , 'updated_at' => now()],


            // ========== تنظیمات نمایش قیمت ==========
            ['key' => 'display_price_type', 'value' => 'installment', 'type' => 'string', 'group' => 'display', 'description' => 'نوع قیمت در سایت (نقدی یا قسطی)', 'created_at' => now() , 'updated_at' => now()],

            ];

        DB::table('settings')->insert($settings);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
