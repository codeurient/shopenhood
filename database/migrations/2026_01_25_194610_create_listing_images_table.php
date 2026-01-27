<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listing_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');

            $table->string('image_path', 500);
            $table->string('thumbnail_path', 500)->nullable();
            $table->string('medium_path', 500)->nullable();

            $table->string('original_filename')->nullable();
            $table->integer('file_size')->nullable()->comment('Size in bytes');
            $table->string('mime_type', 100)->nullable();

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index('listing_id', 'idx_listing_id');
            $table->index(['listing_id', 'is_primary'], 'idx_is_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_images');
    }
};
