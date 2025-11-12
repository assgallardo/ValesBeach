<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "=== Testing Same-Day Booking Calculation ===\n\n";

// Test 1: Dates without time (as they come from form)
$checkInStr = '2025-11-13';
$checkOutStr = '2025-11-13';

$checkIn = Carbon::parse($checkInStr);
$checkOut = Carbon::parse($checkOutStr);

echo "Input dates (from form):\n";
echo "Check-in: {$checkInStr} -> Parsed: {$checkIn->toDateTimeString()}\n";
echo "Check-out: {$checkOutStr} -> Parsed: {$checkOut->toDateTimeString()}\n\n";

// Calculate nights using startOfDay
$checkInDay = $checkIn->copy()->startOfDay();
$checkOutDay = $checkOut->copy()->startOfDay();
$nights = $checkInDay->diffInDays($checkOutDay);

echo "After startOfDay normalization:\n";
echo "Check-in day: {$checkInDay->toDateTimeString()}\n";
echo "Check-out day: {$checkOutDay->toDateTimeString()}\n";
echo "Nights (diffInDays): {$nights}\n\n";

// Apply same-day logic
if ($nights === 0) {
    $nights = 1;
    echo "Same-day booking detected! Setting nights to 1\n";
}

// Calculate price
$roomPrice = 200.00;
$totalPrice = $roomPrice * $nights;

echo "\nFinal calculation:\n";
echo "Room price: ₱{$roomPrice}\n";
echo "Nights: {$nights}\n";
echo "Total price: ₱{$totalPrice}\n\n";

// Test 2: Check if Room model returns correct price
$room = \App\Models\Room::where('name', 'Bahay Kubo 1')->first();
if ($room) {
    echo "=== Room Model Test ===\n";
    echo "Room name: {$room->name}\n";
    echo "Room->price: " . ($room->price ?? 'NULL') . "\n";
    echo "Type of price: " . gettype($room->price) . "\n";
    echo "Float cast: " . ((float)$room->price) . "\n";
}
