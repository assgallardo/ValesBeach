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
        Schema::create('room_cleaning_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('type', [
                'checkout_cleaning',    // After guest checkout
                'daily_service',        // Daily room service during stay
                'deep_cleaning',        // Thorough cleaning
                'turndown_service',     // Evening service
                'maintenance_cleaning'  // After maintenance work
            ])->default('checkout_cleaning');
            
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
                'skipped'
            ])->default('scheduled');
            
            $table->timestamp('scheduled_date');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('special_instructions')->nullable();
            
            // Cleaning checklist tracking
            $table->boolean('bed_made')->default(false);
            $table->boolean('bathroom_cleaned')->default(false);
            $table->boolean('floor_vacuumed')->default(false);
            $table->boolean('trash_removed')->default(false);
            $table->boolean('towels_replaced')->default(false);
            $table->boolean('amenities_restocked')->default(false);
            $table->boolean('surfaces_dusted')->default(false);
            $table->boolean('linens_changed')->default(false);
            
            $table->json('custom_checklist')->nullable(); // Additional checklist items
            $table->json('supplies_used')->nullable(); // Track cleaning supplies
            $table->json('images')->nullable(); // Before/after photos
            
            $table->integer('duration_minutes')->nullable(); // Time taken
            $table->decimal('quality_rating', 3, 2)->nullable(); // Quality score (1-5)
            
            $table->timestamps();
            
            // Indexes
            $table->index('room_id');
            $table->index('booking_id');
            $table->index('status');
            $table->index('scheduled_date');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_cleaning_schedules');
    }
};
