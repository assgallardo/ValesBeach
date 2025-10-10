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
        Schema::table('service_requests', function (Blueprint $table) {
            // Add deadline field
            if (!Schema::hasColumn('service_requests', 'deadline')) {
                $table->datetime('deadline')->nullable()->after('priority');
            }
            
            // Add estimated duration
            if (!Schema::hasColumn('service_requests', 'estimated_duration')) {
                $table->integer('estimated_duration')->nullable()->comment('Duration in minutes')->after('deadline');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['deadline', 'estimated_duration']);
        });
    }
};
