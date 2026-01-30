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
        Schema::table('listings', function (Blueprint $table) {
            // Short description
            $table->text('short_description')->nullable()->after('description');

            // Product availability
            $table->enum('availability_type', ['in_stock', 'available_by_order'])
                ->default('in_stock')
                ->after('is_negotiable');

            // Discount pricing
            $table->decimal('discount_price', 15, 2)->nullable()->after('base_price');
            $table->timestamp('discount_start_date')->nullable()->after('discount_price');
            $table->timestamp('discount_end_date')->nullable()->after('discount_start_date');

            // Location (country and city)
            $table->string('country', 100)->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('country');

            // Store name for business users
            $table->string('store_name', 255)->nullable()->after('created_as_role');

            // Index for discount queries
            $table->index(['discount_price', 'discount_start_date', 'discount_end_date'], 'idx_discount');
            $table->index(['country', 'city'], 'idx_country_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex('idx_discount');
            $table->dropIndex('idx_country_city');

            $table->dropColumn([
                'short_description',
                'availability_type',
                'discount_price',
                'discount_start_date',
                'discount_end_date',
                'country',
                'city',
                'store_name',
            ]);
        });
    }
};
