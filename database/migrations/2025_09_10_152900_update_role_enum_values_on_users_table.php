<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure the role enum includes all expected values
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','manager','staff','guest') NOT NULL DEFAULT 'guest'");

        // Ensure the status enum includes expected values as well
        DB::statement("ALTER TABLE `users` MODIFY `status` ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to a conservative subset (adjust if your previous schema differed)
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff'");
        DB::statement("ALTER TABLE `users` MODIFY `status` ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active'");
    }
};




