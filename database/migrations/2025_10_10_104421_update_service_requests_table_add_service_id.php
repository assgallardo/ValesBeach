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
            // Add columns that your form expects but might be missing
            if (!Schema::hasColumn('service_requests', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('id')->constrained('services')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('service_requests', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('service_id')->constrained('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('service_requests', 'service_type')) {
                $table->string('service_type')->nullable()->after('guest_id');
            }
            
            if (!Schema::hasColumn('service_requests', 'scheduled_date')) {
                $table->datetime('scheduled_date')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('service_requests', 'guests_count')) {
                $table->integer('guests_count')->default(1)->after('scheduled_date');
            }
            
            if (!Schema::hasColumn('service_requests', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('guest_id');
            }
            
            if (!Schema::hasColumn('service_requests', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('guest_name');
            }
            
            if (!Schema::hasColumn('service_requests', 'deadline')) {
                $table->datetime('deadline')->nullable()->after('scheduled_date');
            }
            
            if (!Schema::hasColumn('service_requests', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('status');
            }
            
            if (!Schema::hasColumn('service_requests', 'manager_notes')) {
                $table->text('manager_notes')->nullable()->after('priority');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['guest_id']);
            $table->dropColumn([
                'service_id', 'guest_id', 'service_type', 'scheduled_date', 
                'guests_count', 'guest_name', 'guest_email', 'deadline', 
                'priority', 'manager_notes'
            ]);
        });
    }
};
