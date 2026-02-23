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
        Schema::table('business_profiles', function (Blueprint $table) {
            // null = no request, 'pending' = submitted, 'approved' = green, 'rejected' = yellow
            $table->string('confident_seller_status')->nullable()->after('approved_at');
            $table->text('confident_seller_rejection_reason')->nullable()->after('confident_seller_status');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['confident_seller_status', 'confident_seller_rejection_reason']);
        });
    }
};
