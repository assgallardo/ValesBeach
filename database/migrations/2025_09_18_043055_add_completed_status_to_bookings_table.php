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
        // For SQLite, we need to recreate the table with the new enum values
        DB::statement("UPDATE bookings SET status = 'completed' WHERE 0=1"); // This won't affect any rows but tests the column
        
        // Add completed status to the enum - this approach works for SQLite
        DB::statement("CREATE TABLE bookings_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            room_id INTEGER NOT NULL,
            check_in DATETIME NOT NULL,
            check_out DATETIME NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            guests INTEGER NOT NULL DEFAULT 1,
            status TEXT CHECK(status IN ('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed')) NOT NULL DEFAULT 'pending',
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
        )");
        
        DB::statement("INSERT INTO bookings_new SELECT * FROM bookings");
        DB::statement("DROP TABLE bookings");
        DB::statement("ALTER TABLE bookings_new RENAME TO bookings");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the migration by recreating table without 'completed' status
        DB::statement("CREATE TABLE bookings_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            room_id INTEGER NOT NULL,
            check_in DATETIME NOT NULL,
            check_out DATETIME NOT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            guests INTEGER NOT NULL DEFAULT 1,
            status TEXT CHECK(status IN ('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled')) NOT NULL DEFAULT 'pending',
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
        )");
        
        // Update any 'completed' status to 'checked_out' before copying
        DB::statement("UPDATE bookings SET status = 'checked_out' WHERE status = 'completed'");
        DB::statement("INSERT INTO bookings_new SELECT * FROM bookings");
        DB::statement("DROP TABLE bookings");
        DB::statement("ALTER TABLE bookings_new RENAME TO bookings");
    }
};
