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
        Schema::table('listings', function (Blueprint $table): void {
            $table->boolean('has_same_city_delivery')->default(false)->after('has_delivery');
            $table->decimal('same_city_delivery_price', 15, 2)->nullable()->after('has_same_city_delivery');
            $table->unsignedSmallInteger('same_city_delivery_days')->nullable()->after('same_city_delivery_price');
            $table->unsignedSmallInteger('domestic_delivery_days')->nullable()->after('domestic_delivery_price');
            $table->unsignedSmallInteger('international_delivery_days')->nullable()->after('international_delivery_price');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table): void {
            $table->dropColumn([
                'has_same_city_delivery',
                'same_city_delivery_price',
                'same_city_delivery_days',
                'domestic_delivery_days',
                'international_delivery_days',
            ]);
        });
    }
};
