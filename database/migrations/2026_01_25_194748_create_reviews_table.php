<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('seller_id');

            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->text('pros')->nullable();
            $table->text('cons')->nullable();

            $table->boolean('is_verified_purchase')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->integer('helpful_count')->default(0);

            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('listing_id', 'idx_listing_id');
            $table->index('reviewer_id', 'idx_reviewer_id');
            $table->index('seller_id', 'idx_seller_id');
            $table->index('status', 'idx_status');
            $table->index('rating', 'idx_rating');

            $table->check('rating BETWEEN 1 AND 5');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
