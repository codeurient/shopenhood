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
            // Widen existing plain-text columns to TEXT so they can hold encrypted blobs
            $table->text('registration_number')->nullable()->change();
            $table->text('tax_id')->nullable()->change();

            // New encrypted personal identity & tax fields (user-submitted, admin-readable only)
            $table->text('fin')->nullable()->after('tax_id')->comment('Financial Identification Number (encrypted)');
            $table->text('id_number')->nullable()->after('fin')->comment('National ID / Passport number (encrypted)');
            $table->text('id_full_name')->nullable()->after('id_number')->comment('Full legal name as on ID card (encrypted)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('registration_number', 100)->nullable()->change();
            $table->string('tax_id', 100)->nullable()->change();
            $table->dropColumn(['fin', 'id_number', 'id_full_name']);
        });
    }
};
