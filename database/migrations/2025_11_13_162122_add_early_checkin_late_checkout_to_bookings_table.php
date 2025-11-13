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
            $table->boolean('early_checkin')->default(false)->after('special_requests');
            $table->time('early_checkin_time')->nullable()->after('early_checkin');
            $table->decimal('early_checkin_fee', 10, 2)->default(0)->after('early_checkin_time');
            $table->boolean('late_checkout')->default(false)->after('early_checkin_fee');
            $table->time('late_checkout_time')->nullable()->after('late_checkout');
            $table->decimal('late_checkout_fee', 10, 2)->default(0)->after('late_checkout_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'early_checkin',
                'early_checkin_time',
                'early_checkin_fee',
                'late_checkout',
                'late_checkout_time',
                'late_checkout_fee'
            ]);
        });
    }
};
