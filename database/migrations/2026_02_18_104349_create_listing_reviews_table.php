<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned()->comment('1-5');
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['listing_id', 'user_id']);
            $table->index(['listing_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_reviews');
    }
};
