<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->string('policy_type');
            $table->string('version')->default('1.0');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('require_acceptance')->default(false);
            $table->boolean('show_in_footer')->default(false);
            $table->boolean('show_during_registration')->default(false);
            $table->boolean('show_during_checkout')->default(false);
            $table->boolean('show_during_listing')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
