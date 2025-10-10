<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the 'completed' status already exists
        $hasCompleted = DB::select("SHOW COLUMNS FROM bookings LIKE 'status'");

        if (!empty($hasCompleted)) {
            // Modify the existing enum to include 'completed'
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
        } else {
            // If status column doesn't exist, add it
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'])
                      ->default('pending');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without 'completed'
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
