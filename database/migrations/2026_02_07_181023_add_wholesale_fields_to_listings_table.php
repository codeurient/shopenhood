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
            $table->boolean('is_wholesale')->default(false)->after('is_negotiable');
            $table->unsignedInteger('wholesale_min_order_qty')->nullable()->after('is_wholesale');
            $table->unsignedInteger('wholesale_qty_increment')->nullable()->after('wholesale_min_order_qty');
            $table->unsignedSmallInteger('wholesale_lead_time_days')->nullable()->after('wholesale_qty_increment');
            $table->boolean('wholesale_sample_available')->default(false)->after('wholesale_lead_time_days');
            $table->decimal('wholesale_sample_price', 15, 2)->nullable()->after('wholesale_sample_available');
            $table->text('wholesale_terms')->nullable()->after('wholesale_sample_price');

            $table->index('is_wholesale');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['is_wholesale']);

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
