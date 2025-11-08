<?php

/**
 * Migrate Cottage-Type Rooms to Cottages Table
 * 
 * This script transfers all "Umbrella Cottage" and "Bahay Kubo" type rooms
 * from the rooms table to the cottages table.
 * 
 * Run with: php migrate_cottages_from_rooms.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;
use App\Models\Cottage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║   MIGRATING COTTAGES FROM ROOMS TABLE                  ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    DB::beginTransaction();
    
    // Find all cottage-type rooms
    $cottageTypes = ['Umbrella Cottage', 'Bahay Kubo'];
    $cottageRooms = Room::whereIn('type', $cottageTypes)->get();
    
    echo "📊 Found " . $cottageRooms->count() . " cottage-type rooms to migrate\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $migratedCount = 0;
    $errors = [];
    
    foreach ($cottageRooms as $room) {
        try {
            echo "➤ Migrating: {$room->name} ({$room->type})\n";
            
            // Generate a unique code for the cottage
            $code = 'COT-' . strtoupper(Str::random(3)) . '-' . str_pad($room->id, 3, '0', STR_PAD_LEFT);
            
            // Determine pricing based on description
            $pricePerDay = $room->price;
            $pricePerHour = null;
            
            // For Umbrella Cottages, extract night rate from description
            if ($room->type === 'Umbrella Cottage') {
                $pricePerHour = round($pricePerDay / 8, 2); // Estimate hourly rate
                // Extract night rate if mentioned (₱400.00)
                if (preg_match('/Night Rate[^\d]*([\d,]+\.?\d*)/', $room->description, $matches)) {
                    $nightRate = floatval(str_replace(',', '', $matches[1]));
                    // Use average of day and night as base price
                    $pricePerDay = $nightRate;
                }
            }
            
            // For Bahay Kubo, extract rates
            if ($room->type === 'Bahay Kubo') {
                // Extract night rate (₱250.00)
                if (preg_match('/Night Rate[^\d]*([\d,]+\.?\d*)/', $room->description, $matches)) {
                    $nightRate = floatval(str_replace(',', '', $matches[1]));
                    $pricePerDay = $nightRate;
                }
                $pricePerHour = round($pricePerDay / 8, 2);
            }
            
            // Decode amenities
            $amenities = [];
            if ($room->amenities) {
                $decoded = is_string($room->amenities) ? json_decode($room->amenities, true) : $room->amenities;
                $amenities = $decoded ?: [];
            }
            
            // Create cottage record
            $cottage = Cottage::create([
                'name' => $room->name,
                'code' => $code,
                'description' => $room->description,
                'capacity' => $room->capacity ?? 20,
                'bedrooms' => 0, // Cottages don't have bedrooms
                'bathrooms' => 0, // Not specified in rooms
                'price_per_day' => $pricePerDay,
                'price_per_hour' => $pricePerHour,
                'weekend_rate' => $pricePerDay * 1.15, // 15% markup for weekends
                'holiday_rate' => $pricePerDay * 1.25, // 25% markup for holidays
                'security_deposit' => 500.00, // Standard deposit
                'min_hours' => 4,
                'max_hours' => 12,
                'amenities' => $amenities,
                'features' => ['outdoor', 'beach_access'],
                'location' => 'Beachfront Area',
                'size_sqm' => $room->type === 'Umbrella Cottage' ? 30.00 : 25.00,
                'status' => $room->status === 'available' ? 'available' : 'unavailable',
                'allow_day_use' => true,
                'allow_overnight' => true,
                'allow_pets' => false,
                'allow_events' => true,
                'advance_booking_days' => 30,
                'primary_image' => null,
                'images' => [],
                'last_maintenance' => null,
                'next_maintenance' => now()->addMonths(3),
                'sort_order' => $room->id,
                'is_featured' => false,
                'is_active' => true,
            ]);
            
            echo "  ✅ Created cottage: {$cottage->code} - {$cottage->name}\n";
            echo "     Price: ₱{$pricePerDay}/day" . ($pricePerHour ? ", ₱{$pricePerHour}/hour" : "") . "\n";
            echo "     Capacity: {$cottage->capacity} persons\n";
            
            $migratedCount++;
            
        } catch (\Exception $e) {
            $errors[] = "Failed to migrate {$room->name}: " . $e->getMessage();
            echo "  ❌ Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    // Show summary
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 MIGRATION SUMMARY\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Successfully migrated: {$migratedCount} cottages\n";
    
    if (count($errors) > 0) {
        echo "❌ Errors encountered: " . count($errors) . "\n";
        foreach ($errors as $error) {
            echo "   - {$error}\n";
        }
    }
    
    echo "\n";
    echo "🔍 NEXT STEPS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "1. Review the migrated cottages in the database\n";
    echo "2. If everything looks correct, delete the cottage-type rooms:\n";
    echo "   DELETE FROM rooms WHERE type IN ('Umbrella Cottage', 'Bahay Kubo');\n";
    echo "3. Or run this script with --delete flag to auto-delete\n";
    echo "\n";
    
    // Check if --delete flag is present
    $shouldDelete = in_array('--delete', $argv ?? []);
    
    if ($shouldDelete && $migratedCount > 0) {
        echo "🗑️  Deleting cottage-type rooms from rooms table...\n";
        $deleted = Room::whereIn('type', $cottageTypes)->delete();
        echo "✅ Deleted {$deleted} cottage-type rooms\n\n";
    }
    
    DB::commit();
    
    echo "✅ MIGRATION COMPLETED SUCCESSFULLY!\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n";
    echo "❌ MIGRATION FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
