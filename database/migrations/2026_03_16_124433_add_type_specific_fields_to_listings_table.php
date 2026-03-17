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
            // Buy type: optional maximum budget
            $table->decimal('budget_max', 15, 2)->nullable()->after('base_price');

            // Barter type: what the poster wants in exchange
            $table->text('barter_exchange_for')->nullable()->after('budget_max');

            // Auction type fields
            $table->dateTime('auction_end_date')->nullable()->after('barter_exchange_for');
            $table->decimal('auction_reserve_price', 15, 2)->nullable()->after('auction_end_date');
            $table->decimal('auction_bid_increment', 15, 2)->nullable()->after('auction_reserve_price');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'budget_max',
                'barter_exchange_for',
                'auction_end_date',
                'auction_reserve_price',
                'auction_bid_increment',
            ]);
        });
    }
};
