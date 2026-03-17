<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_platform_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // registration, checkout, product_listing, auction_creation, etc.
            $table->unique(['policy_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_platform_actions');
    }
};
