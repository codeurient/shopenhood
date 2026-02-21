<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->boolean('is_wholesale')->default(false)->after('sort_order');
            $table->unsignedSmallInteger('wholesale_min_order_qty')->nullable()->after('is_wholesale');
            $table->unsignedSmallInteger('wholesale_qty_increment')->nullable()->default(1)->after('wholesale_min_order_qty');
            $table->unsignedSmallInteger('wholesale_lead_time_days')->nullable()->after('wholesale_qty_increment');
            $table->boolean('wholesale_sample_available')->default(false)->after('wholesale_lead_time_days');
            $table->decimal('wholesale_sample_price', 10, 2)->nullable()->after('wholesale_sample_available');
            $table->text('wholesale_terms')->nullable()->after('wholesale_sample_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_variations', function (Blueprint $table) {
            $table->dropColumn([
                'is_wholesale',
                'wholesale_min_order_qty',
                'wholesale_qty_increment',
                'wholesale_lead_time_days',
                'wholesale_sample_available',
                'wholesale_sample_price',
                'wholesale_terms',
            ]);
        });
    }
};
