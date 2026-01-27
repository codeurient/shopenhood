<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('listing_type_id')->constrained('listing_types')->restrictOnDelete();
            
            $table->string('title', 255);
            $table->string('slug', 500)->unique();
            $table->text('description');
            $table->decimal('base_price', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            $table->enum('status', [
                'draft',
                'pending',
                'active',
                'sold',
                'expired',
                'rejected'
            ])->default('draft');

            $table->boolean('is_visible')->default(true)->comment('User-controlled visibility');
            $table->boolean('hidden_due_to_subscription')->default(false)->comment('Auto-hidden when subscription expires');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_negotiable')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('address')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->enum('created_as_role', [ 'admin', 'normal_user','business_user']);

            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('category_id');
            $table->index('listing_type_id');
            $table->index('status');
            $table->index('is_visible');
            $table->index('hidden_due_to_subscription');
            $table->index('slug');
            $table->index('created_at');
            $table->index(['status', 'is_visible', 'hidden_due_to_subscription'], 'idx_public_listings');
            $table->index(['latitude', 'longitude'], 'idx_location');

            // Fulltext
            $table->fullText(['title', 'description'], 'fulltext_search');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
