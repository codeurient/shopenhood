<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference', 100)->unique();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('payment_method');
            $table->string('gateway_transaction_id')->nullable();

            $table->enum('status', [
                'pending', 
                'processing', 
                'completed', 
                'failed', 
                'refunded', 
                'cancelled'])->default('pending');
                
            $table->json('gateway_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->decimal('refunded_amount', 15, 2)->default(0.00);
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            // Indexlər
            $table->index(['payable_type', 'payable_id'], 'idx_payable');
            $table->index('payment_reference', 'idx_payment_reference');
            $table->index('user_id', 'idx_user_id');
            $table->index('status', 'idx_status');
            $table->index('gateway_transaction_id', 'idx_gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

