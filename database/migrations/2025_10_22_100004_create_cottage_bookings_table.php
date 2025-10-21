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
        Schema::create('cottage_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('cottage_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Booking Type
            $table->enum('booking_type', [
                'day_use',      // Daytime use only
                'overnight',    // Overnight stay
                'hourly',       // Hourly rental
                'event'         // Special event
            ])->default('day_use');
            
            // Date & Time
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->integer('hours')->nullable(); // For hourly bookings
            $table->integer('nights')->default(0); // Number of nights
            
            // Guest Information
            $table->integer('guests');
            $table->integer('children')->default(0);
            $table->text('special_requests')->nullable();
            
            // Pricing
            $table->decimal('base_price', 10, 2); // Base cottage price
            $table->decimal('additional_guest_fee', 10, 2)->default(0);
            $table->decimal('extra_hours_fee', 10, 2)->default(0);
            $table->decimal('weekend_surcharge', 10, 2)->default(0);
            $table->decimal('holiday_surcharge', 10, 2)->default(0);
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            
            // Payment Tracking (similar to room bookings)
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            
            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'checked_in',
                'checked_out',
                'cancelled',
                'completed',
                'no_show'
            ])->default('pending');
            
            // Additional Info
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            
            // Equipment/Add-ons (optional)
            $table->json('addons')->nullable(); // Extra equipment, catering, etc.
            
            // Notes
            $table->text('admin_notes')->nullable();
            $table->text('guest_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('cottage_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index(['check_in_date', 'check_out_date', 'cottage_id'], 'cottage_availability_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cottage_bookings');
    }
};
