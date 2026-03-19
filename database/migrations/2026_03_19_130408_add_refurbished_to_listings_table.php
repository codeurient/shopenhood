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
        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `condition` ENUM('new', 'used', 'refurbished') NOT NULL DEFAULT 'new'");

        Schema::table('listings', function (Blueprint $table) {
            $table->text('refurbished_description')->nullable()->after('condition');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('refurbished_description');
        });

        DB::statement("ALTER TABLE `listings` MODIFY COLUMN `condition` ENUM('new', 'used') NOT NULL DEFAULT 'new'");
    }
};
