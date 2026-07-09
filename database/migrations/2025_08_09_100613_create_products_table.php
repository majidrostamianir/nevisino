<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // ارتباطات
            $table->foreignId('brand_id')->default(1)->constrained();
            $table->foreign('category_id')->references('id')->on('categories');

            // اطلاعات پایه
            $table->string('code')->nullable();
            $table->string('title');
            $table->string('dashed_url');
            $table->text('description')->nullable();
            $table->string('variant')->nullable()->default(null);

            // قیمت‌ها
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('price_previous')->nullable();
            $table->timestamp('price_updated_at')->nullable();
            $table->unsignedBigInteger('bulk_price')->nullable();
            $table->unsignedBigInteger('bulk_price_previous')->nullable();
            $table->timestamp('bulk_price_updated_at')->nullable();
            $table->unsignedBigInteger('installment_price')->nullable();
            $table->unsignedBigInteger('installment_price_previous')->nullable();
            $table->timestamp('installment_price_updated_at')->nullable();
            $table->unsignedBigInteger('discounted_price')->nullable();
            $table->unsignedBigInteger('discounted_price_previous')->nullable();
            $table->timestamp('discounted_price_updated_at')->nullable();
            $table->unsignedBigInteger('discounted_installment_price')->nullable();
            $table->unsignedBigInteger('discounted_installment_price_previous')->nullable();
            $table->timestamp('discounted_installment_price_updated_at')->nullable();

            // موجودی و فروش
            $table->unsignedBigInteger('stock')->nullable();
            $table->unsignedBigInteger('sold_quantity')->default(0);

            // ابعاد و وزن
            $table->string('size');
            $table->unsignedInteger('weight');

            // لینک ترب
            $table->string('torob_url')->nullable();

            $table->timestamps();
        });

        Schema::create('product_url', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('url_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('url_id')->references('id')->on('urls')->onDelete('cascade');

            // اضافه کردن ایندکس برای جلوگیری از رکوردهای تکراری
            $table->unique(['product_id', 'url_id']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_url');
    }
};
