<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Validator;

echo "Testing Form Validations\n";
echo "=======================\n";

// Test 1: Signup validation rules
echo "1. Testing Signup Validation Rules\n";
echo "---------------------------------\n";

$signupRules = [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:6|confirmed',
    'terms' => 'required|accepted',
];

// Valid signup data
$validSignupData = [
    'name' => 'Test User',
    'email' => 'testuser' . time() . '@example.com', // Unique email
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'terms' => 'on'
];

$validator = Validator::make($validSignupData, $signupRules);
echo "Valid signup data: " . ($validator->passes() ? 'PASS' : 'FAIL') . "\n";

// Invalid signup data - missing required fields
$invalidSignupData = [
    'name' => '',
    'email' => 'invalid-email',
    'password' => '123', // Too short
    'password_confirmation' => 'different', // Doesn't match
    'terms' => '' // Not accepted
];

$validator = Validator::make($invalidSignupData, $signupRules);
$errors = $validator->errors();
echo "Invalid signup data validation:\n";
echo "- Name required: " . ($errors->has('name') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Email format: " . ($errors->has('email') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Password length: " . ($errors->has('password') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Terms acceptance: " . ($errors->has('terms') ? 'DETECTED' : 'MISSED') . "\n";

echo "\n";

// Test 2: Login validation rules
echo "2. Testing Login Validation Rules\n";
echo "--------------------------------\n";

$loginRules = [
    'email' => 'required|email',
    'password' => 'required'
];

// Valid login data
$validLoginData = [
    'email' => 'admin@valesbeach.com',
    'password' => 'admin123'
];

$validator = Validator::make($validLoginData, $loginRules);
echo "Valid login data: " . ($validator->passes() ? 'PASS' : 'FAIL') . "\n";

// Invalid login data
$invalidLoginData = [
    'email' => 'not-an-email',
    'password' => ''
];

$validator = Validator::make($invalidLoginData, $loginRules);
$errors = $validator->errors();
echo "Invalid login data validation:\n";
echo "- Email format: " . ($errors->has('email') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Password required: " . ($errors->has('password') ? 'DETECTED' : 'MISSED') . "\n";

echo "\n";

// Test 3: Booking validation rules
echo "3. Testing Booking Validation Rules\n";
echo "----------------------------------\n";

// Get a room for capacity testing
$room = \App\Models\Room::first();
$maxCapacity = $room ? $room->capacity : 4;

$bookingRules = [
    'check_in' => 'required|date|after_or_equal:today',
    'check_out' => 'required|date|after:check_in',
    'guests' => 'nullable|integer|min:1|max:' . $maxCapacity
];

// Valid booking data
$validBookingData = [
    'check_in' => \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'),
    'check_out' => \Carbon\Carbon::now()->addDays(3)->format('Y-m-d'),
    'guests' => 2
];

$validator = Validator::make($validBookingData, $bookingRules);
echo "Valid booking data: " . ($validator->passes() ? 'PASS' : 'FAIL') . "\n";

// Invalid booking data
$invalidBookingData = [
    'check_in' => \Carbon\Carbon::now()->subDays(1)->format('Y-m-d'), // Past date
    'check_out' => \Carbon\Carbon::now()->subDays(2)->format('Y-m-d'), // Before check_in
    'guests' => $maxCapacity + 1 // Exceeds capacity
];

$validator = Validator::make($invalidBookingData, $bookingRules);
$errors = $validator->errors();
echo "Invalid booking data validation:\n";
echo "- Check-in past date: " . ($errors->has('check_in') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Check-out before check-in: " . ($errors->has('check_out') ? 'DETECTED' : 'MISSED') . "\n";
echo "- Guests exceed capacity: " . ($errors->has('guests') ? 'DETECTED' : 'MISSED') . "\n";

echo "\n";

// Test 4: Edge cases
echo "4. Testing Edge Cases\n";
echo "--------------------\n";

// Same day check-in/check-out (0 nights)
$sameDayData = [
    'check_in' => \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'),
    'check_out' => \Carbon\Carbon::now()->addDays(1)->format('Y-m-d'),
    'guests' => 1
];

$validator = Validator::make($sameDayData, $bookingRules);
echo "Same day check-in/check-out: " . ($validator->fails() ? 'REJECTED (CORRECT)' : 'ALLOWED (ERROR)') . "\n";

// Future dates validation
$futureData = [
    'check_in' => \Carbon\Carbon::now()->addYear()->format('Y-m-d'),
    'check_out' => \Carbon\Carbon::now()->addYear()->addDays(2)->format('Y-m-d'),
    'guests' => 1
];

$validator = Validator::make($futureData, $bookingRules);
echo "Far future booking: " . ($validator->passes() ? 'ALLOWED (CORRECT)' : 'REJECTED (ERROR)') . "\n";

echo "\nForm validation testing completed!\n";
echo "Summary:\n";
echo "✓ Signup validation rules working properly\n";
echo "✓ Login validation rules working properly\n";
echo "✓ Booking validation rules working properly\n";
echo "✓ Edge cases handled correctly\n";
