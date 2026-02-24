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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->after('id')->constrained('purchases')->nullOnDelete();
            $table->string('delivery_option_name', 100)->nullable()->after('payment_method');
            $table->enum('delivery_cost_paid_by', ['seller', 'buyer'])->nullable()->after('delivery_option_name');

            $table->index('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropIndex(['purchase_id']);
            $table->dropColumn(['purchase_id', 'delivery_option_name', 'delivery_cost_paid_by']);
        });
    }
};
