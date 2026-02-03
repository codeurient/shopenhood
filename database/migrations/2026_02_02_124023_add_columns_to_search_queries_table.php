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
        Schema::table('search_queries', function (Blueprint $table) {
            $table->string('query')->after('id');
            $table->foreignId('user_id')->nullable()->after('query')->constrained()->nullOnDelete();
            $table->integer('results_count')->default(0)->after('user_id');
            $table->json('filters')->nullable()->after('results_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_queries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['query', 'user_id', 'results_count', 'filters']);
        });
    }
};
