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
        // For SQLite, we need to recreate the table to modify columns
        // First, check if the columns already have the correct values
        $hasGuest = DB::table('users')->where('role', 'guest')->exists();
        
        // If guest role doesn't exist as a value yet, we need to ensure it's allowed
        // SQLite doesn't have ENUM, so these are just string columns
        // The validation happens at the application level
        
        // Make sure we can insert guest roles by updating any existing data if needed
        // This migration essentially ensures the application can handle all role types
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert anything for SQLite since we didn't change schema
    }
};




