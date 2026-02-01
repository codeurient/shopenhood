<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Size, Color, Storage, etc.');
            $table->string('slug')->unique();
            $table->enum('type', ['select', 'radio', 'checkbox', 'text', 'number', 'range'])->default('select');
            $table->boolean('is_required')->default(false);
            $table->text('description')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
