<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('reservation_id')->nullable();
            
            // Guest information
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('room_number')->nullable();
            
            // Booking details
            $table->date('requested_date')->nullable();
            $table->time('requested_time')->nullable();
            $table->integer('guests')->default(1);
            $table->text('special_requests')->nullable();
            $table->text('description')->nullable();
            
            // Status and timing
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled', 'confirmed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->datetime('requested_at')->nullable();
            $table->datetime('scheduled_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->datetime('confirmed_at')->nullable();
            
            // Staff assignment and notes
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->text('manager_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['service_id', 'status']);
            $table->index('requested_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};