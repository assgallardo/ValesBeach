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
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'scheduled_date')) {
                $table->timestamp('scheduled_date')->nullable()->after('deadline');
            }
        });

        // Copy existing deadline values to scheduled_date for existing records
        DB::statement('UPDATE service_requests SET scheduled_date = deadline WHERE scheduled_date IS NULL AND deadline IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'scheduled_date')) {
                $table->dropColumn('scheduled_date');
            }
        });
    }
};
