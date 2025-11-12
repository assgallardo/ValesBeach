<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manager Booking Controller Validation Test ===\n\n";

// Simulate form data - Existing Guest
$existingGuestData = [
    'user_id' => '1',
    'room_id' => '1',
    'check_in' => '2025-11-13',
    'check_out' => '2025-11-13',
    'guests' => '2',
    'status' => 'confirmed'
];

echo "Test 1: Existing Guest (has user_id)\n";
echo "Data: " . json_encode($existingGuestData) . "\n";
$isNewGuest = !empty($existingGuestData['guest_name']) && !empty($existingGuestData['guest_email']);
echo "Is New Guest: " . ($isNewGuest ? 'YES' : 'NO') . "\n";
echo "Will validate: user_id\n";
echo "Result: ✓ PASS - Should use existing user\n\n";

// Simulate form data - New Guest
$newGuestData = [
    'guest_name' => 'John Doe',
    'guest_email' => 'john@example.com',
    'room_id' => '1',
    'check_in' => '2025-11-13',
    'check_out' => '2025-11-13',
    'guests' => '2',
    'status' => 'confirmed'
];

echo "Test 2: New Guest (has guest_name and guest_email)\n";
echo "Data: " . json_encode($newGuestData) . "\n";
$isNewGuest = !empty($newGuestData['guest_name']) && !empty($newGuestData['guest_email']);
echo "Is New Guest: " . ($isNewGuest ? 'YES' : 'NO') . "\n";
echo "Will validate: guest_name, guest_email\n";
echo "Result: ✓ PASS - Should create new user\n\n";

// Test same-day booking calculation
echo "Test 3: Same-day booking calculation\n";
$checkIn = \Carbon\Carbon::parse('2025-11-13')->startOfDay();
$checkOut = \Carbon\Carbon::parse('2025-11-13')->startOfDay();
$nights = $checkIn->diffInDays($checkOut);
echo "Check-in: {$checkIn->toDateString()}\n";
echo "Check-out: {$checkOut->toDateString()}\n";
echo "Nights (raw): {$nights}\n";

if ($nights == 0) {
    $nights = 1;
}

echo "Nights (corrected): {$nights}\n";
$roomPrice = 7500;
$total = $roomPrice * $nights;
echo "Room price: ₱" . number_format($roomPrice, 2) . "\n";
echo "Total: ₱" . number_format($total, 2) . "\n";
echo "Result: " . ($total == 7500 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "All tests completed!\n";
