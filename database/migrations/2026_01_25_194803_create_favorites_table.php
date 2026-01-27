<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('listing_id');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');

            $table->unique(
                ['user_id', 'listing_id'],
                'unique_favorite'
            );

            $table->index('user_id', 'idx_user_id');
            $table->index('listing_id', 'idx_listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
