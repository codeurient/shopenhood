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
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['listing_id']);
            $table->unsignedBigInteger('listing_id')->nullable()->change();
            $table->foreign('listing_id')->references('id')->on('listings')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropForeign(['listing_id']);
            $table->unsignedBigInteger('listing_id')->nullable()->change();
            $table->foreign('listing_id')->references('id')->on('listings')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['listing_id']);
            $table->unsignedBigInteger('listing_id')->nullable(false)->change();
            $table->foreign('listing_id')->references('id')->on('listings')->restrictOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropForeign(['listing_id']);
            $table->unsignedBigInteger('listing_id')->nullable(false)->change();
            $table->foreign('listing_id')->references('id')->on('listings')->restrictOnDelete();
        });
    }
};
