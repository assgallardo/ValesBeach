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
        // Make issue_date nullable to support customer combined invoices which use invoice_date instead
        DB::statement('ALTER TABLE invoices MODIFY issue_date DATE NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert issue_date to NOT NULL
        DB::statement('ALTER TABLE invoices MODIFY issue_date DATE NOT NULL');
    }
};
