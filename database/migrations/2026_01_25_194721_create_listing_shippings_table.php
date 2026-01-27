<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_shipping', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('shipping_method_id');

            $table->boolean('is_enabled')->default(true);
            $table->decimal('additional_cost', 15, 2)->default(0.00);

            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('cascade');

            $table->unique(
                ['listing_id', 'shipping_method_id'],
                'unique_listing_shipping'
            );

            $table->index('listing_id', 'idx_listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_shipping');
    }
};
