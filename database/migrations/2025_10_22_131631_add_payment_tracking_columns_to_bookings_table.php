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
        Schema::table('bookings', function (Blueprint $table) {
            // Add payment tracking columns
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_price');
            $table->decimal('remaining_balance', 10, 2)->default(0)->after('amount_paid');
            $table->string('payment_status')->default('unpaid')->after('remaining_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Remove payment tracking columns
            $table->dropColumn(['amount_paid', 'remaining_balance', 'payment_status']);
        });
    }
};
