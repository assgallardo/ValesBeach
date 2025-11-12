<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;

echo "=== Fixing Same-Day Bookings with Zero Price ===\n\n";

// Find all same-day bookings with total_price = 0
$bookings = DB::table('bookings')
    ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
    ->whereRaw('DATE(bookings.check_in) = DATE(bookings.check_out)')
    ->where('bookings.total_price', 0)
    ->get([
        'bookings.id',
        'bookings.check_in',
        'bookings.check_out',
        'bookings.total_price',
        'rooms.name as room_name',
        'rooms.price as room_price'
    ]);

echo "Found " . count($bookings) . " same-day bookings with ₱0.00 price\n\n";

foreach ($bookings as $booking) {
    $checkIn = Carbon::parse($booking->check_in)->startOfDay();
    $checkOut = Carbon::parse($booking->check_out)->startOfDay();
    $nightsCalculated = $checkIn->diffInDays($checkOut);
    
    // Same-day booking counts as 1 night (1 day stay)
    // Note: diffInDays returns float, so use == not ===
    $nights = ($nightsCalculated == 0) ? 1 : $nightsCalculated;
    
    $correctPrice = (float)$booking->room_price * $nights;
    
    echo "Booking ID: {$booking->id}\n";
    echo "Room: {$booking->room_name} (₱{$booking->room_price}/night)\n";
    echo "Check-in: {$booking->check_in}\n";
    echo "Check-out: {$booking->check_out}\n";
    echo "Nights (calculated): {$nightsCalculated} -> Corrected to: {$nights}\n";
    echo "Current Price: ₱{$booking->total_price}\n";
    echo "Correct Price: ₱{$correctPrice}\n";
    
    // Update the booking
    DB::table('bookings')
        ->where('id', $booking->id)
        ->update(['total_price' => $correctPrice]);
    
    echo "✓ Updated!\n";
    echo "---\n";
}

echo "\nDone! All same-day bookings have been fixed.\n";
