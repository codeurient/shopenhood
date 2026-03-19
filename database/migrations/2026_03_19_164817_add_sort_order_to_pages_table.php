<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('is_published');
        });

        // Initialise existing rows with sequential order
        \App\Models\Page::orderBy('id')->each(function ($page, $index) {
            $page->update(['sort_order' => $index + 1]);
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
