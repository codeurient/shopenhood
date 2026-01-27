<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->string('display_value')->nullable()->comment('Formatted display name');
            $table->string('color_code', 7)->nullable()->comment('Hex color for color variants');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('variant_id');
            $table->index('is_active');

            $table->unique(['variant_id', 'value'], 'unique_variant_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_items');
    }
};
