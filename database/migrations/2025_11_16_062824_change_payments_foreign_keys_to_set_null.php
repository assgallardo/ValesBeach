<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Change foreign key constraints on payments table from cascade to set null
     * to preserve completed payment records even when related entities are deleted.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop existing foreign key constraints
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['food_order_id']);
            
            // Recreate foreign keys with onDelete('set null') instead of cascade
            // This ensures completed payments remain as historical records
            $table->foreign('service_request_id')
                  ->references('id')
                  ->on('service_requests')
                  ->onDelete('set null');
            
            $table->foreign('food_order_id')
                  ->references('id')
                  ->on('food_orders')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop the set null foreign keys
            $table->dropForeign(['service_request_id']);
            $table->dropForeign(['food_order_id']);
            
            // Restore original cascade behavior
            $table->foreign('service_request_id')
                  ->references('id')
                  ->on('service_requests')
                  ->onDelete('cascade');
            
            $table->foreign('food_order_id')
                  ->references('id')
                  ->on('food_orders')
                  ->onDelete('cascade');
        });
    }
};
