<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->text('url')->nullable();
            $table->string('ip')->nullable();
            $table->boolean('is_ghesti')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('referrer')->nullable();

            // ستون‌های تشخیص ربات
            $table->boolean('is_bot')->default(false);
            $table->string('bot_name', 255)->nullable()->after('is_bot');
            $table->string('bot_category', 255)->nullable()->after('bot_name');

            // user_agent را به VARCHAR(1024) تغییر دادیم تا قابل ایندکس/فیلتر باشد
            $table->string('user_agent', 1024)->nullable();

            $table->timestamps();

            // ایندکس‌ها برای فیلتر سریع‌تر
            $table->index(['is_bot', 'created_at'], 'visits_is_bot_created_at_index');
            $table->index('bot_name', 'visits_bot_name_index');
            $table->index('ip', 'visits_ip_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};