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
        Schema::create('product_variation_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');

            $table->string('image_path');
            $table->string('original_filename')->nullable();
            $table->unsignedInteger('file_size')->nullable()->comment('In bytes');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false)->comment('Primary image for this variation');

            $table->timestamps();

            // Indexes
            $table->index('product_variation_id');
            $table->index('is_primary');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_images');
    }
};
