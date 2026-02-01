<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->comment('sell, buy, gift, barter, auction');
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_price')->default(true);
            $table->string('icon', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('slug', 'idx_slug');
            $table->index('is_active', 'idx_is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_types');
    }
};
