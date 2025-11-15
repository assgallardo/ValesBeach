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
        Schema::table('payments', function (Blueprint $table) {
            // Add payment_transaction_id to group payments into sessions
            // When a guest makes payments, they're grouped together
            // Once all payments in a transaction are completed, the next payment creates a new transaction
            $table->string('payment_transaction_id')->nullable()->after('payment_reference');
            
            // Add index for faster queries
            $table->index(['user_id', 'payment_transaction_id']);
            $table->index(['payment_transaction_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'payment_transaction_id']);
            $table->dropIndex(['payment_transaction_id', 'status']);
            $table->dropColumn('payment_transaction_id');
        });
    }
};
