<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "=== Testing Admin Booking Same-Day Logic ===\n\n";

// Simulate the form data
$checkInStr = '2025-11-13';
$checkOutStr = '2025-11-13';

echo "Form Input:\n";
echo "Check-in: {$checkInStr}\n";
echo "Check-out: {$checkOutStr}\n\n";

// Simulate the controller logic
$checkIn = Carbon::parse($checkInStr)->startOfDay();
$checkOut = Carbon::parse($checkOutStr)->startOfDay();
$nights = $checkIn->diffInDays($checkOut);

echo "After parsing:\n";
echo "Check-in (Carbon): {$checkIn->toDateTimeString()}\n";
echo "Check-out (Carbon): {$checkOut->toDateTimeString()}\n";
echo "Nights (diffInDays): {$nights}\n";
echo "Nights type: " . gettype($nights) . "\n\n";

// Apply same-day logic
if ($nights == 0) {
    $nights = 1;
    echo "✓ Same-day booking detected! Corrected nights to: 1\n\n";
}

// Calculate price (Executive Cottage = ₱7500)
$roomPrice = 7500.00;
$totalPrice = $roomPrice * $nights;

echo "Price Calculation:\n";
echo "Room price: ₱" . number_format($roomPrice, 2) . "\n";
echo "Nights: {$nights}\n";
echo "Total price: ₱" . number_format($totalPrice, 2) . "\n\n";

echo "Expected result: ₱7,500.00\n";
echo "Actual result: ₱" . number_format($totalPrice, 2) . "\n";
echo "\nTest " . ($totalPrice == 7500 ? "PASSED ✓" : "FAILED ✗") . "\n";
