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
        Schema::create('room_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('type', [
                'cleaning',
                'repair',
                'inspection',
                'preventive',
                'emergency',
                'upgrade'
            ])->default('repair');
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            $table->enum('status', [
                'pending',
                'in_progress',
                'completed',
                'cancelled',
                'on_hold'
            ])->default('pending');
            
            $table->string('title');
            $table->text('description');
            $table->text('notes')->nullable();
            $table->text('resolution_notes')->nullable();
            
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('due_date')->nullable();
            
            $table->json('images')->nullable(); // Store maintenance photos
            $table->json('checklist')->nullable(); // Maintenance checklist items
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('room_id');
            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index('scheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_maintenance_logs');
    }
};
