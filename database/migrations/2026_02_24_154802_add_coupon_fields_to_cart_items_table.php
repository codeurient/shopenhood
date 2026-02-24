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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('coupon_code', 50)->nullable()->after('is_selected');
            $table->decimal('coupon_discount', 10, 2)->nullable()->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'coupon_discount']);
        });
    }
};
