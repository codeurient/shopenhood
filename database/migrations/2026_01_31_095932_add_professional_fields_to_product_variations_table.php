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
        Schema::table('product_variations', function (Blueprint $table) {
            // Add actual price (not just adjustment)
            $table->decimal('price', 15, 2)->default(0)->after('variant_combination');

            // Discount pricing
            $table->decimal('discount_price', 15, 2)->nullable()->after('price');
            $table->timestamp('discount_start_date')->nullable()->after('discount_price');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');

            // Cost price for profit tracking
            $table->decimal('cost_price', 15, 2)->nullable()->after('price_adjustment');

            // Enhanced inventory management
            $table->boolean('manage_stock')->default(true)->after('low_stock_threshold');
            $table->boolean('allow_backorder')->default(false)->after('manage_stock');

            // Physical dimensions as separate columns
            $table->decimal('length', 10, 2)->nullable()->comment('in cm')->after('weight');
            $table->decimal('width', 10, 2)->nullable()->comment('in cm')->after('length');
            $table->decimal('height', 10, 2)->nullable()->comment('in cm')->after('width');

            // Add indexes
            $table->index('stock_quantity', 'idx_stock');
        });

        // Rename column in a separate call
        Schema::table('product_variations', function (Blueprint $table) {
            $table->renameColumn('is_available', 'is_active');
        });

        // Add new status columns after rename
        Schema::table('product_variations', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->comment('Default variation for this listing')->after('is_active');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('is_default');

            $table->index(['listing_id', 'is_default'], 'idx_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'discount_price',
                'discount_start_date',
                'discount_end_date',
                'cost_price',
                'manage_stock',
                'allow_backorder',
                'length',
                'width',
                'height',
                'is_default',
                'sort_order',
            ]);

            $table->renameColumn('is_active', 'is_available');

            $table->dropIndex('idx_stock');
            $table->dropIndex('idx_default');
        });
    }
};
