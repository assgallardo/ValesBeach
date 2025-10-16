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
        // Add sort_order to menu_items table only (menu_categories already has it)
        Schema::table('menu_items', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('popularity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sort_order from menu_items table
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
