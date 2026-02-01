<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');

            $table->enum('type', ['purchase', 'sale', 'return', 'adjustment', 'damage', 'initial']);
            $table->integer('quantity_change')->comment('Negative for sales/damage, positive for purchases/returns');
            $table->integer('quantity_before');
            $table->integer('quantity_after');

            $table->string('reference', 100)->nullable()->comment('External reference number');
            $table->text('notes')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('product_variation_id', 'idx_variation');
            $table->index('type', 'idx_type');
            $table->index('created_at', 'idx_created');
            $table->index(['product_variation_id', 'created_at'], 'idx_variation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
