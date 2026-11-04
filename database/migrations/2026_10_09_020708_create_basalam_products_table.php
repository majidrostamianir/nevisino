<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basalam_products', function (Blueprint $table) {
            $table->id();

            // ارتباط با محصول/واریانت خودمان
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->cascadeOnDelete();

            // شناسه‌های سمت باسلام
            $table->string('basalam_product_id')->nullable();  // شناسه محصول اصلی در باسلام
            $table->string('basalam_sku')->nullable();         // SKU که به باسلام دادیم

            // وضعیت سینک
            $table->enum('status', ['pending', 'synced', 'failed'])->default('pending');
            $table->timestamp('synced_at')->nullable();
            $table->text('last_error')->nullable();

            // اطلاعات کمکی برای دیباگ و بازپخش
            $table->json('payload')->nullable();   // آخرین payload ارسالی به باسلام
            $table->json('response')->nullable();  // آخرین پاسخ باسلام

            $table->timestamps();

            // ایندکس‌ها
            $table->index(['product_id', 'product_variant_id'], 'idx_basalam_product_variant');
            $table->index('basalam_product_id');
            $table->index(['status', 'synced_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basalam_products');
    }
};