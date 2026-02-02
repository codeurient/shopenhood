<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('listing_limit')->nullable()->after('is_business_enabled')
                ->comment('Max active listings. NULL = unlimited');
            $table->timestamp('business_valid_until')->nullable()->after('listing_limit')
                ->comment('When business user status expires');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['listing_limit', 'business_valid_until']);
        });
    }
};
