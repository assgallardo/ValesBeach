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
        // For SQLite, we just need to ensure the column exists
        // Laravel's Blueprint will handle the enum as a string check constraint
        if (!Schema::hasColumn('bookings', 'status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'])
                      ->default('pending');
            });
        }
        // Note: SQLite doesn't support modifying enum values after creation
        // If status column already exists, this migration assumes it includes all values
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQLite, we cannot easily modify enum constraints
        // This is a no-op for SQLite databases
        if (Schema::hasColumn('bookings', 'status')) {
            // Keep the column as-is since SQLite doesn't support enum modification
        }
    }
};
