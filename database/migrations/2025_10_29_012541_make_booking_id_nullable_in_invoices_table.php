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
        // Make booking_id nullable to support customer combined invoices
        DB::statement('ALTER TABLE invoices MODIFY booking_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert booking_id to NOT NULL
        DB::statement('ALTER TABLE invoices MODIFY booking_id BIGINT UNSIGNED NOT NULL');
    }
};
