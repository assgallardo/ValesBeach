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
        Schema::create('cottages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Unique cottage identifier (e.g., COT-001)
            $table->text('description')->nullable();
            
            // Capacity
            $table->integer('capacity')->default(4); // Maximum guests
            $table->integer('bedrooms')->default(1);
            $table->integer('bathrooms')->default(1);
            
            // Pricing
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('price_per_hour', 10, 2)->nullable(); // Hourly rate if applicable
            $table->decimal('weekend_rate', 10, 2)->nullable(); // Weekend pricing
            $table->decimal('holiday_rate', 10, 2)->nullable(); // Holiday pricing
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->integer('min_hours')->default(4); // Minimum rental hours
            $table->integer('max_hours')->default(12); // Maximum rental hours per day
            
            // Features & Amenities (JSON for flexibility)
            $table->json('amenities')->nullable(); // ['wifi', 'kitchen', 'grill', etc.]
            $table->json('features')->nullable(); // ['sea_view', 'private_pool', etc.]
            
            // Location
            $table->string('location')->nullable(); // Beach front, hillside, etc.
            $table->decimal('size_sqm', 8, 2)->nullable(); // Size in square meters
            
            // Status
            $table->enum('status', [
                'available',
                'occupied',
                'maintenance',
                'reserved',
                'unavailable'
            ])->default('available');
            
            // Booking Rules
            $table->boolean('allow_day_use')->default(true);
            $table->boolean('allow_overnight')->default(true);
            $table->boolean('allow_pets')->default(false);
            $table->boolean('allow_events')->default(false);
            $table->integer('advance_booking_days')->default(30); // How far in advance can book
            
            // Images
            $table->string('primary_image')->nullable();
            $table->json('images')->nullable(); // Additional cottage images
            
            // Maintenance
            $table->timestamp('last_maintenance')->nullable();
            $table->timestamp('next_maintenance')->nullable();
            
            // Sorting & Display
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cottages');
    }
};
