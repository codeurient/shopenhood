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
        Schema::create('phone_country_codes', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->string('phone_code', 10);
            $table->string('trunk_prefix', 10)->nullable();
            $table->string('idd_prefix', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_country_codes');
    }
};
