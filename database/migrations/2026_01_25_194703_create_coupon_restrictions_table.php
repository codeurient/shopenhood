<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_restrictions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('coupon_id');
            $table->string('restrictable_type');
            $table->unsignedBigInteger('restrictable_id');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('coupon_id')
                ->references('id')
                ->on('coupons')
                ->onDelete('cascade');

            $table->index('coupon_id', 'idx_coupon_id');
            $table->index(
                ['restrictable_type', 'restrictable_id'],
                'idx_restrictable'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_restrictions');
    }
};
