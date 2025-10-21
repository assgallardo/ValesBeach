<?php

/**
 * Data Creation Test
 * Creates test records to verify full system functionality
 * 
 * Run with: php artisan tinker < tests/create_test_data.php
 */

use App\Models\User;
use App\Models\Room;
use App\Models\Cottage;
use App\Models\CottageBooking;
use App\Models\RoomMaintenanceLog;
use App\Models\RoomCleaningSchedule;

echo "\n";
echo "╔════════════════════════════════════════════════════════╗\n";
echo "║            CREATING TEST DATA                          ║\n";
echo "╚════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    DB::beginTransaction();
    
    // Test 1: Create a Cottage
    echo "🏖️ TEST 1: Creating Test Cottage\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $cottage = Cottage::create([
        'name' => 'Sunset Beach Cottage',
        'code' => 'COT-001',
        'description' => 'Beautiful beachfront cottage with stunning sunset views',
        'capacity' => 6,
        'bedrooms' => 2,
        'bathrooms' => 2,
        'price_per_day' => 5000.00,
        'price_per_hour' => 500.00,
        'weekend_rate' => 6000.00,
        'holiday_rate' => 7000.00,
        'security_deposit' => 1000.00,
        'min_hours' => 4,
        'max_hours' => 12,
        'amenities' => ['wifi', 'kitchen', 'grill', 'tv', 'ac'],
        'features' => ['sea_view', 'beachfront', 'private_parking'],
        'location' => 'Beachfront - North Wing',
        'size_sqm' => 80.00,
        'status' => 'available',
        'allow_day_use' => true,
        'allow_overnight' => true,
        'allow_pets' => false,
        'allow_events' => true,
        'advance_booking_days' => 30,
        'is_active' => true,
        'is_featured' => true,
        'sort_order' => 1,
    ]);
    
    echo "✅ Cottage created successfully!\n";
    echo "   ID: {$cottage->id}\n";
    echo "   Name: {$cottage->name}\n";
    echo "   Code: {$cottage->code}\n";
    echo "   Capacity: {$cottage->capacity} guests\n";
    echo "   Price: {$cottage->formatted_price_per_day}/day\n";
    echo "   Status: {$cottage->status}\n";
    echo "\n";
    
    // Test 2: Create a Cottage Booking
    echo "📅 TEST 2: Creating Test Cottage Booking\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $user = User::where('role', 'guest')->first();
    if (!$user) {
        $user = User::first();
    }
    
    $checkIn = now()->addDays(7);
    $checkOut = now()->addDays(9);
    $nights = $checkIn->diffInDays($checkOut);
    $totalPrice = $cottage->price_per_day * $nights;
    
    $booking = CottageBooking::create([
        'cottage_id' => $cottage->id,
        'user_id' => $user->id,
        'booking_type' => 'overnight',
        'check_in_date' => $checkIn->format('Y-m-d'),
        'check_out_date' => $checkOut->format('Y-m-d'),
        'check_in_time' => '14:00',
        'check_out_time' => '12:00',
        'nights' => $nights,
        'guests' => 4,
        'children' => 2,
        'special_requests' => 'Extra towels and early check-in if possible',
        'base_price' => $cottage->price_per_day,
        'total_price' => $totalPrice,
        'remaining_balance' => $totalPrice,
        'payment_status' => 'unpaid',
        'status' => 'pending',
    ]);
    
    echo "✅ Cottage booking created successfully!\n";
    echo "   Booking Reference: {$booking->booking_reference}\n";
    echo "   Cottage: {$cottage->name}\n";
    echo "   Guest: {$user->name}\n";
    echo "   Check-in: {$booking->check_in_date->format('M d, Y')}\n";
    echo "   Check-out: {$booking->check_out_date->format('M d, Y')}\n";
    echo "   Nights: {$booking->nights}\n";
    echo "   Guests: {$booking->guests} (Children: {$booking->children})\n";
    echo "   Total: {$booking->formatted_total_price}\n";
    echo "   Minimum Payment: {$booking->minimum_payment}\n";
    echo "   Status: {$booking->status}\n";
    echo "\n";
    
    // Test 3: Create Maintenance Log
    echo "🔧 TEST 3: Creating Test Maintenance Log\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $room = Room::first();
    if (!$room) {
        echo "⚠️  No rooms found. Skipping maintenance log test.\n\n";
    } else {
        $maintenance = RoomMaintenanceLog::create([
            'room_id' => $room->id,
            'reported_by' => $user->id,
            'type' => 'repair',
            'priority' => 'high',
            'status' => 'pending',
            'title' => 'Air conditioning unit not cooling properly',
            'description' => 'The AC unit in the room is running but not producing cold air. Needs immediate attention.',
            'notes' => 'Guest reported issue this morning',
            'estimated_cost' => 5000.00,
            'scheduled_date' => now()->addDay(),
            'due_date' => now()->addDays(2),
        ]);
        
        echo "✅ Maintenance log created successfully!\n";
        echo "   ID: {$maintenance->id}\n";
        echo "   Room: {$room->name}\n";
        echo "   Type: {$maintenance->type_label}\n";
        echo "   Priority: {$maintenance->priority_label}\n";
        echo "   Status: {$maintenance->status_label}\n";
        echo "   Title: {$maintenance->title}\n";
        echo "   Estimated Cost: {$maintenance->formatted_estimated_cost}\n";
        echo "   Scheduled: {$maintenance->scheduled_date->format('M d, Y')}\n";
        echo "\n";
    }
    
    // Test 4: Create Cleaning Schedule
    echo "🧹 TEST 4: Creating Test Cleaning Schedule\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    if (!$room) {
        echo "⚠️  No rooms found. Skipping cleaning schedule test.\n\n";
    } else {
        $staff = User::where('role', 'staff')->first();
        if (!$staff) {
            $staff = $user;
        }
        
        $cleaning = RoomCleaningSchedule::create([
            'room_id' => $room->id,
            'assigned_to' => $staff->id,
            'type' => 'deep_cleaning',
            'priority' => 'normal',
            'status' => 'scheduled',
            'scheduled_date' => now()->addHours(2),
            'notes' => 'Deep cleaning after renovation work',
            'special_instructions' => 'Pay extra attention to corners and windows',
        ]);
        
        echo "✅ Cleaning schedule created successfully!\n";
        echo "   ID: {$cleaning->id}\n";
        echo "   Room: {$room->name}\n";
        echo "   Type: {$cleaning->type_label}\n";
        echo "   Priority: {$cleaning->priority_label}\n";
        echo "   Status: {$cleaning->status_label}\n";
        echo "   Assigned to: {$staff->name}\n";
        echo "   Scheduled: {$cleaning->scheduled_date->format('M d, Y H:i')}\n";
        echo "   Checklist Completion: {$cleaning->checklist_completion}%\n";
        echo "\n";
    }
    
    // Test 5: Test Model Methods
    echo "🧪 TEST 5: Testing Model Methods\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // Test cottage availability
    $isAvailable = $cottage->isAvailableFor(
        now()->addDays(7),
        now()->addDays(9)
    );
    echo "✅ Cottage availability check: " . ($isAvailable ? 'Available' : 'Not Available') . "\n";
    
    // Test price calculation
    $calculatedPrice = $cottage->calculatePrice(
        now()->addDays(7),
        now()->addDays(9),
        'overnight'
    );
    echo "✅ Price calculation: ₱" . number_format($calculatedPrice, 2) . "\n";
    
    // Test booking methods
    $canCancel = $booking->canBeCancelled();
    echo "✅ Booking can be cancelled: " . ($canCancel ? 'Yes' : 'No') . "\n";
    
    // Test booking confirmation
    $booking->confirm();
    echo "✅ Booking confirmed: {$booking->status}\n";
    
    // Test scopes
    $activeBookings = CottageBooking::active()->count();
    echo "✅ Active cottage bookings: {$activeBookings}\n";
    
    $availableCottages = Cottage::available()->count();
    echo "✅ Available cottages: {$availableCottages}\n";
    
    if ($room) {
        $pendingMaintenance = RoomMaintenanceLog::pending()->count();
        echo "✅ Pending maintenance tasks: {$pendingMaintenance}\n";
        
        $scheduledCleaning = RoomCleaningSchedule::scheduled()->count();
        echo "✅ Scheduled cleanings: {$scheduledCleaning}\n";
    }
    
    echo "\n";
    
    // Test 6: Test Relationships
    echo "🔗 TEST 6: Testing Relationships\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $cottageWithBookings = Cottage::with('bookings')->find($cottage->id);
    echo "✅ Cottage->bookings relationship: {$cottageWithBookings->bookings->count()} booking(s)\n";
    
    $bookingWithCottage = CottageBooking::with('cottage')->find($booking->id);
    echo "✅ Booking->cottage relationship: {$bookingWithCottage->cottage->name}\n";
    
    $bookingWithUser = CottageBooking::with('user')->find($booking->id);
    echo "✅ Booking->user relationship: {$bookingWithUser->user->name}\n";
    
    if ($room) {
        if (isset($maintenance)) {
            $maintenanceWithRoom = RoomMaintenanceLog::with('room')->find($maintenance->id);
            echo "✅ Maintenance->room relationship: {$maintenanceWithRoom->room->name}\n";
        }
        
        if (isset($cleaning)) {
            $cleaningWithRoom = RoomCleaningSchedule::with('room')->find($cleaning->id);
            echo "✅ Cleaning->room relationship: {$cleaningWithRoom->room->name}\n";
        }
    }
    
    echo "\n";
    
    // Commit or rollback
    echo "💾 Would you like to keep this test data? (yes/no): ";
    echo "\n";
    echo "⚠️  Auto-rolling back test data to keep database clean.\n";
    echo "   To keep test data, run this script manually in tinker.\n";
    
    DB::rollBack();
    
    echo "\n";
    echo "╔════════════════════════════════════════════════════════╗\n";
    echo "║          TEST DATA CREATION COMPLETE                   ║\n";
    echo "║          (Changes rolled back)                         ║\n";
    echo "╚════════════════════════════════════════════════════════╝\n";
    echo "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n";
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "\n";
}
