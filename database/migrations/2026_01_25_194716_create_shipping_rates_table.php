<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shipping_zone_id');
            $table->unsignedBigInteger('shipping_method_id');

            $table->decimal('min_weight', 10, 2)->nullable()->comment('In kg');
            $table->decimal('max_weight', 10, 2)->nullable();
            $table->decimal('min_order_amount', 15, 2)->nullable();
            $table->decimal('max_order_amount', 15, 2)->nullable();
            $table->decimal('price', 15, 2);

            $table->integer('estimated_days_min')->nullable();
            $table->integer('estimated_days_max')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('shipping_zone_id')->references('id')->on('shipping_zones')->onDelete('cascade');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('cascade');

            $table->index(
                ['shipping_zone_id', 'shipping_method_id'],
                'idx_zone_method'
            );
            $table->index('is_active', 'idx_is_active');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
