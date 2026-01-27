<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('listing_id')->nullable();
            $table->string('subject', 500)->nullable();

            $table->boolean('is_archived')->default(false);

            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('set null');

            $table->index('listing_id', 'idx_listing_id');
            $table->index('created_at', 'idx_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
