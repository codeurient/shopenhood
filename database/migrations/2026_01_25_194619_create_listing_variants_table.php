<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_item_id')->nullable()->constrained('variant_items')->onDelete('cascade');
            $table->text('custom_value')->nullable()->comment('For text/number/range types');

            $table->timestamps();

            $table->index('listing_id');
            $table->index('variant_id');
            $table->index('variant_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_variants');
    }
};
