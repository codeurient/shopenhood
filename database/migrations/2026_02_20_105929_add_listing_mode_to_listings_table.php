<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->enum('listing_mode', ['normal', 'business'])->default('normal')->after('id');
        });

        // Backfill: listings that have product variations are business listings
        DB::statement("
            UPDATE listings
            SET listing_mode = 'business'
            WHERE id IN (
                SELECT DISTINCT listing_id FROM product_variations
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('listing_mode');
        });
    }
};
