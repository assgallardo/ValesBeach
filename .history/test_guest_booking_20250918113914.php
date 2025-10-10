<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

echo "Testing Guest Booking Flow...\n\n";

try {
    // Find or create a guest user
    $guest = User::where('role', 'guest')->first();
    if (!$guest) {
        echo "❌ No guest user found! Creating one...\n";
        $guest = User::create([
            'name' => 'Test Guest',
            'email' => 'testguest@example.com',
            'password' => bcrypt('password123'),
            'role' => 'guest',
            'status' => 'active'
        ]);
        echo "✅ Guest user created: {$guest->email}\n\n";
    } else {
        echo "✅ Using existing guest user: {$guest->email}\n\n";
    }

    // Find available room
    $room = Room::where('is_available', true)->first();
    if (!$room) {
        echo "❌ No available rooms found!\n";
        exit(1);
    }
    echo "✅ Found available room: {$room->name} (₱{$room->price})\n\n";

    // Simulate authentication
    Auth::login($guest);
    echo "✅ Guest authenticated: " . Auth::user()->name . " (Role: " . Auth::user()->role . ")\n\n";

    // Test booking creation
    $checkIn = now()->addDays(1);
    $checkOut = now()->addDays(3);
    $nights = $checkIn->diffInDays($checkOut);
    $totalPrice = $room->price * $nights;

    echo "Booking Details:\n";
    echo "- Check-in: {$checkIn->format('Y-m-d')}\n";
    echo "- Check-out: {$checkOut->format('Y-m-d')}\n";
    echo "- Nights: {$nights}\n";
    echo "- Total Price: ₱{$totalPrice}\n\n";

    // Check if room is available for these dates
    $conflictingBooking = $room->bookings()
        ->where(function($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($q) use ($checkIn, $checkOut) {
                      $q->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                  });
        })
        ->where('status', '!=', 'cancelled')
        ->first();

    if ($conflictingBooking) {
        echo "❌ Room has conflicting booking: {$conflictingBooking->id}\n";
        echo "   Existing booking: {$conflictingBooking->check_in} to {$conflictingBooking->check_out}\n";
        exit(1);
    }
    echo "✅ No booking conflicts found\n\n";

    // Create the booking
    $booking = $room->bookings()->create([
        'user_id' => $guest->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'total_price' => $totalPrice,
        'guests' => 2,
        'status' => 'pending'
    ]);

    echo "✅ Booking created successfully!\n";
    echo "   Booking ID: {$booking->id}\n";
    echo "   Status: {$booking->status}\n";
    echo "   Guest: {$booking->user->name}\n";
    echo "   Room: {$booking->room->name}\n\n";

    // Test booking retrieval
    $userBookings = $guest->bookings()->with('room')->get();
    echo "✅ Guest has " . $userBookings->count() . " booking(s)\n";

    foreach ($userBookings as $userBooking) {
        echo "   - Booking #{$userBooking->id}: {$userBooking->room->name} ({$userBooking->status})\n";
    }

    echo "\n🎉 Guest booking flow test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error during booking flow test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
