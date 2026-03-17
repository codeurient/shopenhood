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
        Schema::table('phone_country_codes', function (Blueprint $table) {
            $table->string('flag')->nullable()->after('idd_prefix')
                ->comment('Optional path or URL to the country flag image');
        });
    }

    public function down(): void
    {
        Schema::table('phone_country_codes', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
};
