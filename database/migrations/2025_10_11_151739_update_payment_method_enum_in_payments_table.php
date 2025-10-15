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
        // For SQLite, ENUM is implemented as TEXT with CHECK constraint or at application level
        // Since we're using SQLite, we don't need to modify the column structure
        // The validation will be handled at the application level
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No schema changes needed for SQLite
    }
};
