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
        // Modify the enum to include 'confirmed' and 'overdue'
        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `status` ENUM('pending', 'confirmed', 'processing', 'completed', 'overdue', 'failed', 'refunded', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'confirmed' and 'overdue' from the enum
        DB::statement("ALTER TABLE `payments` MODIFY COLUMN `status` ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
