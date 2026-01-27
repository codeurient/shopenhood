<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_responses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('review_id');
            $table->unsignedBigInteger('responder_id');

            $table->text('response');

            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('responder_id')->references('id')->on('users')->onDelete('cascade');

            $table->index('review_id', 'idx_review_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_responses');
    }
};
