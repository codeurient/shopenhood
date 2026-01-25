<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->string('phone', 20)->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            $table->enum('current_role', [
                'admin',
                'normal_user',
                'business_user'
            ])->default('normal_user');

            $table->boolean('is_business_enabled')->default(false);

            $table->enum('status', [
                'active',
                'suspended',
                'banned'
            ])->default('active');

            $table->integer('daily_listing_count')->default(0);
            $table->date('last_listing_date')->nullable();

            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // INDEXES
            $table->index('email', 'idx_email');
            $table->index('status', 'idx_status');
            $table->index('current_role', 'idx_current_role');
            $table->index('is_business_enabled', 'idx_is_business_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
