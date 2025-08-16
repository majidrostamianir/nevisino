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
            $table->id()->startingValue(12230);
            $table->string('title');
            $table->string('dashed_title');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('inventory');
            $table->unsignedBigInteger('sold_quantity')->default(0);
            $table->unsignedInteger('length');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('weight');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
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
