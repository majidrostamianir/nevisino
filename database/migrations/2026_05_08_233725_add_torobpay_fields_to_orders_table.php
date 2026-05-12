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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('torobpay_payment_token')->nullable()->after('description');
            $table->string('torobpay_transaction_id')->nullable()->after('torobpay_payment_token');
            $table->string('torobpay_status')->nullable()->after('torobpay_transaction_id');
            $table->integer('torobpay_amount')->nullable()->after('torobpay_status');
            $table->timestamp('torobpay_paid_at')->nullable()->after('torobpay_amount');
        });
    }




    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'torobpay_payment_token',
                'torobpay_transaction_id',
                'torobpay_status',
                'torobpay_amount',
                'torobpay_paid_at',
            ]);
        });
    }
};
