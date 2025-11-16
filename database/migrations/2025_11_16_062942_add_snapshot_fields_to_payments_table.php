<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add snapshot fields to store payment details permanently
     * even when related entities (service requests, food orders, etc.) are deleted.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Store item description snapshot
            $table->string('item_description')->nullable()->after('notes');
            
            // Store item type for reference (booking, service, food_order, extra_charge)
            $table->string('item_type')->nullable()->after('item_description');
            
            // Store additional details as JSON for flexibility
            $table->json('item_details')->nullable()->after('item_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['item_description', 'item_type', 'item_details']);
        });
    }
};
