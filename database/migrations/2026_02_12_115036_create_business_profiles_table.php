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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Basic Business Information
            $table->string('business_name', 255);
            $table->string('legal_name', 255)->nullable();
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();

            // Registration & Tax
            $table->string('registration_number', 100)->nullable();
            $table->string('tax_id', 100)->nullable()->comment('VAT/GST/Tax ID');

            // Business Type
            $table->string('industry', 100)->nullable();
            $table->enum('business_type', ['sole_proprietor', 'partnership', 'llc', 'corporation', 'other'])->nullable();

            // Business Address
            $table->string('address_line_1', 255)->nullable();
            $table->string('address_line_2', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state_province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('locations')->nullOnDelete();

            // Contact
            $table->string('business_email', 255)->nullable();
            $table->string('business_phone', 30)->nullable();
            $table->string('website', 255)->nullable();

            // Branding
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();

            // Settings
            $table->string('default_currency', 3)->default('USD');
            $table->string('timezone', 50)->nullable();
            $table->text('return_policy')->nullable();
            $table->text('shipping_policy')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique('user_id');
            $table->index('business_name');
            $table->index('industry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
