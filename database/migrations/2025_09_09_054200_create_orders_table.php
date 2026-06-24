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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_number');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'paid', 'canceled'])->default('pending');
            $table->enum('shipping_status', ['pending','processing', 'preparing', 'shipped' , 'delivered' , 'returned'])->default('pending');
            $table->string('tracking_code')->nullable();
            $table->unsignedBigInteger('total_price')->default(0);
            $table->unsignedBigInteger('shipping_price')->default(0);
            $table->enum('shipping_method',['post_cod' , 'post_cash' , 'tipax_cod','tipax_cash','post_free','tipax_free'])->default('post_cod');
            $table->enum('packaging_size',['packaging_1' , 'packaging_2' , 'packaging_3','packaging_4','packaging_5','packaging_6','packaging_7','packaging_8','packaging_9','packaging_10'])->default('packaging_1');
            $table->unsignedBigInteger('packaging_price')->default(0);
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('recipient_name');
            $table->string('recipient_mobile');
            $table->text('postal_address');
            $table->string('zipcode');
            $table->string('province');
            $table->string('city');
            $table->text('description')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
