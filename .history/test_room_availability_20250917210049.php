<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

echo "Testing Room Availability System\n";
echo "===============================\n";

$user = User::where('role', 'guest')->first();
$room = Room::first();

if (!$user || !$room) {
    echo "ERROR: Missing user or room for testing\n";
    exit;
}

echo "Testing room: {$room->name}\n";
echo "User: {$user->name}\n\n";

// Test 1: Create a booking
$checkIn1 = Carbon::now()->addDays(10);
$checkOut1 = $checkIn1->copy()->addDays(3); // 3 nights

$booking1 = Booking::create([
    'user_id' => $user->id,
    'room_id' => $room->id,
    'check_in' => $checkIn1,
    'check_out' => $checkOut1,
    'guests' => 2,
    'total_price' => $room->price * 3,
    'status' => 'confirmed'
]);

echo "Created test booking:\n";
echo "Check-in: {$checkIn1->format('Y-m-d')}\n";
echo "Check-out: {$checkOut1->format('Y-m-d')}\n";
echo "Status: {$booking1->status}\n\n";

// Test 2: Check availability for overlapping dates (should conflict)
$checkIn2 = $checkIn1->copy()->addDays(1); // Starts during existing booking
$checkOut2 = $checkOut1->copy()->addDays(2); // Ends after existing booking

echo "Test 2: Overlapping booking check\n";
echo "Requested Check-in: {$checkIn2->format('Y-m-d')}\n";
echo "Requested Check-out: {$checkOut2->format('Y-m-d')}\n";

$hasConflict = $room->bookings()
    ->where(function($query) use ($checkIn2, $checkOut2) {
        $query->whereBetween('check_in', [$checkIn2, $checkOut2])
              ->orWhereBetween('check_out', [$checkIn2, $checkOut2])
              ->orWhere(function($q) use ($checkIn2, $checkOut2) {
                  $q->where('check_in', '<=', $checkIn2)
                    ->where('check_out', '>=', $checkOut2);
              });
    })
    ->where('status', '!=', 'cancelled')
    ->exists();

echo "Conflict detected: " . ($hasConflict ? 'YES (CORRECT)' : 'NO (ERROR)') . "\n\n";

// Test 3: Check availability for non-overlapping dates (should be available)
$checkIn3 = $checkOut1->copy()->addDays(1); // Starts after existing booking ends
$checkOut3 = $checkIn3->copy()->addDays(2); // 2 nights later

echo "Test 3: Non-overlapping booking check\n";
echo "Requested Check-in: {$checkIn3->format('Y-m-d')}\n";
echo "Requested Check-out: {$checkOut3->format('Y-m-d')}\n";

$hasConflict3 = $room->bookings()
    ->where(function($query) use ($checkIn3, $checkOut3) {
        $query->whereBetween('check_in', [$checkIn3, $checkOut3])
              ->orWhereBetween('check_out', [$checkIn3, $checkOut3])
              ->orWhere(function($q) use ($checkIn3, $checkOut3) {
                  $q->where('check_in', '<=', $checkIn3)
                    ->where('check_out', '>=', $checkOut3);
              });
    })
    ->where('status', '!=', 'cancelled')
    ->exists();

echo "Conflict detected: " . ($hasConflict3 ? 'YES (ERROR)' : 'NO (CORRECT)') . "\n\n";

// Test 4: Test cancelled booking (should not conflict)
$booking1->update(['status' => 'cancelled']);

echo "Test 4: After cancelling the booking\n";
$hasConflictAfterCancel = $room->bookings()
    ->where(function($query) use ($checkIn2, $checkOut2) {
        $query->whereBetween('check_in', [$checkIn2, $checkOut2])
              ->orWhereBetween('check_out', [$checkIn2, $checkOut2])
              ->orWhere(function($q) use ($checkIn2, $checkOut2) {
                  $q->where('check_in', '<=', $checkIn2)
                    ->where('check_out', '>=', $checkOut2);
              });
    })
    ->where('status', '!=', 'cancelled')
    ->exists();

echo "Conflict with cancelled booking: " . ($hasConflictAfterCancel ? 'YES (ERROR)' : 'NO (CORRECT)') . "\n\n";

echo "Room availability system test completed!\n";
echo "Summary:\n";
echo "✓ Overlapping dates detection: " . ($hasConflict ? 'WORKING' : 'BROKEN') . "\n";
echo "✓ Non-overlapping dates: " . (!$hasConflict3 ? 'WORKING' : 'BROKEN') . "\n";
echo "✓ Cancelled bookings ignored: " . (!$hasConflictAfterCancel ? 'WORKING' : 'BROKEN') . "\n";
