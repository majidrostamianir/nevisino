<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digipay_transactions', function (Blueprint $table) {
            $table->id();

            // 🔗 لینک به جداول ما
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();

            // اطلاعات پایه خرید
            $table->string('provider_id')->unique();       // شناسه یونیک ما
            $table->string('tracking_code')->nullable();   // کد پیگیری دیجی‌پی
            $table->string('ticket')->nullable();          // ticket

            // اطلاعات کاربر
            $table->string('cell_number');
            $table->unsignedBigInteger('amount');

            // نوع خرید
            $table->integer('type')->default(11);          // 0=IPG, 11=Wallet, 5=Credit, 13=BNPL, 24=CreditCard
            $table->integer('preferred_gateway')->nullable(); // 0=Wallet, 2=IPG

            // وضعیت
            $table->string('status')->default('pending');
            // pending → verified → delivered → refunded / reversed / failed

            // اطلاعات callback
            $table->string('callback_url');
            $table->string('redirect_url')->nullable();

            // اطلاعات وریفای (بعد از پرداخت پر میشه)
            $table->string('ref_id')->nullable();          // ← جدید (rrn / ref_id)
            $table->string('rrn')->nullable();
            $table->string('masked_pan')->nullable();
            $table->string('psp_code')->nullable();
            $table->string('psp_name')->nullable();
            $table->string('terminal_id')->nullable();

            // اطلاعات پاسخ کامل (برای دیباگ)
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->json('verify_payload')->nullable();

            // زمان‌ها
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('cell_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digipay_transactions');
    }
};