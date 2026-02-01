<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_reminders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_subscription_id')->constrained('user_subscriptions')->onDelete('cascade');
            $table->timestamp('sent_at');
            $table->integer('reminder_day')->comment('Days before expiry: 7,6,5,4,3,2,1');
            $table->enum('email_status', ['sent', 'failed', 'bounced'])->default('sent');
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('user_subscription_id', 'idx_subscription_id');
            $table->index('sent_at', 'idx_sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_reminders');
    }
};
