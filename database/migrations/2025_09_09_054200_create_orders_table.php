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
            $table->enum('shipping_method',['post_cod' , 'post_cash' , 'tipax_cod','tipax_cash'])->default('post_cod');
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
