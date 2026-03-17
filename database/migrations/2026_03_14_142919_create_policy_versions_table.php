<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->longText('content');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['policy_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_versions');
    }
};
