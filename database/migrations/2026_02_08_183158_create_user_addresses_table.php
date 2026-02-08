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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Address label/type
            $table->string('label', 50)->default('Home'); // Home, Work, Other
            $table->boolean('is_default')->default(false);

            // Recipient information
            $table->string('recipient_name', 100);
            $table->string('phone', 30);
            $table->string('email', 100)->nullable();

            // Address details
            $table->string('country', 100);
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->string('street', 255);
            $table->string('building', 50)->nullable();
            $table->string('apartment', 50)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('additional_notes')->nullable();

            // Geolocation (optional for future use)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
