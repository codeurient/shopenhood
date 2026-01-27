<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');

            $table->string('sku', 100)->unique();
            $table->json('variant_combination')->nullable()->comment('Map of variant_id => variant_item_id');
            $table->decimal('price_adjustment', 15, 2)->default(0.00)->comment('Added to base_price');
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->boolean('is_available')->default(true);
            $table->decimal('weight', 10, 2)->nullable()->comment('For shipping calculation');
            $table->json('dimensions')->nullable()->comment('{length, width, height}');

            $table->timestamps();

            // Indexes
            $table->index('listing_id', 'idx_listing_id');
            $table->index('sku', 'idx_sku');
            $table->index('is_available', 'idx_is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
