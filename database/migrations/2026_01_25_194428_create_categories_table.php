<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->integer('level')->default(1)->comment('Depth level: 1,2,3...');
            $table->string('path', 500)->nullable()->comment('Ancestor IDs: 1/5/12');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('parent_id', 'idx_parent_id');
            $table->index('slug', 'idx_slug');
            $table->index('is_active', 'idx_is_active');
            $table->index('level', 'idx_level');
            $table->index('path', 'idx_path');
            $table->index('sort_order', 'idx_sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
