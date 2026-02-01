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
        Schema::create('product_variation_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained();
            $table->foreignId('variant_item_id')->constrained();

            $table->timestamp('created_at')->useCurrent();

            // Unique constraint: Each variation can only have one value per variant
            $table->unique(['product_variation_id', 'variant_id'], 'unique_variation_variant');

            // Indexes for faster lookups
            $table->index('product_variation_id', 'idx_variation');
            $table->index(['variant_id', 'variant_item_id'], 'idx_variant_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_attributes');
    }
};
