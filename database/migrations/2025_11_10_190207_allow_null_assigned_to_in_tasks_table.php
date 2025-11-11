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
            // Change assigned_to to allow NULL values for unassigned tasks
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert assigned_to to NOT NULL (only if rolling back)
            $table->unsignedBigInteger('assigned_to')->nullable(false)->change();
        });
    }
};
