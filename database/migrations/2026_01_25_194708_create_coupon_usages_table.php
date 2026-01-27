<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id')->nullable();

            $table->decimal('discount_amount', 15, 2);

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            
            $table->index('coupon_id', 'idx_coupon_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('order_id', 'idx_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usage');
    }
};
