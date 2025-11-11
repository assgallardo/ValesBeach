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
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->after('service_request_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->string('task_type')->default('service')->after('booking_id'); // 'service' or 'housekeeping'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropColumn(['booking_id', 'task_type']);
        });
    }
};
