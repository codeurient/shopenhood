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
            $table->boolean('has_delivery')->default(false)->after('availability_type');
            $table->boolean('has_domestic_delivery')->default(false)->after('has_delivery');
            $table->decimal('domestic_delivery_price', 15, 2)->nullable()->after('has_domestic_delivery');
            $table->boolean('has_international_delivery')->default(false)->after('domestic_delivery_price');
            $table->decimal('international_delivery_price', 15, 2)->nullable()->after('has_international_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'has_delivery',
                'has_domestic_delivery',
                'domestic_delivery_price',
                'has_international_delivery',
                'international_delivery_price',
            ]);
        });
    }
};
