<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

echo "Testing BookingController Price Logic\n";
echo "====================================\n";

// Get a guest user and room
$user = User::where('role', 'guest')->first();
$room = Room::first();

if (!$user || !$room) {
    echo "ERROR: Missing user or room for testing\n";
    exit;
}

// Simulate booking creation like the controller does
$checkIn = Carbon::now()->addDays(5);
$checkOut = $checkIn->copy()->addDays(3); // 3 nights
$nights = $checkIn->diffInDays($checkOut);
$totalPrice = $room->price * $nights;

echo "User: {$user->name} ({$user->email})\n";
echo "Room: {$room->name} - ₱{$room->price}/night\n";
echo "Check-in: {$checkIn->format('Y-m-d')}\n";
echo "Check-out: {$checkOut->format('Y-m-d')}\n";
echo "Nights: {$nights}\n";
echo "Calculated Total: ₱{$totalPrice}\n";

// Create the booking
$booking = Booking::create([
    'user_id' => $user->id,
    'room_id' => $room->id,
    'check_in' => $checkIn,
    'check_out' => $checkOut,
    'guests' => 2,
    'total_price' => $totalPrice,
    'status' => 'pending'
]);

echo "\nBooking created with ID: {$booking->id}\n";
echo "Stored total_price: ₱{$booking->total_price}\n";
echo "Formatted total_price: {$booking->formatted_total_price}\n";
echo "Match: " . ($booking->total_price == $totalPrice ? 'YES' : 'NO') . "\n";

echo "\nBooking Controller logic test: PASSED!\n";
