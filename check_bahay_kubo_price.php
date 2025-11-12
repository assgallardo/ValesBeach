<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Bahay Kubo Rooms ===\n\n";

$rooms = DB::table('rooms')
    ->where('name', 'like', '%Bahay Kubo%')
    ->get(['id', 'name', 'type', 'price', 'capacity']);

foreach ($rooms as $room) {
    echo "ID: {$room->id}\n";
    echo "Name: {$room->name}\n";
    echo "Type: {$room->type}\n";
    echo "Price: " . ($room->price ?? 'NULL') . "\n";
    echo "Capacity: {$room->capacity}\n";
    echo "---\n";
}

echo "\n=== Checking Recent Same-Day Booking ===\n\n";

$booking = DB::table('bookings')
    ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
    ->where('rooms.name', 'like', '%Bahay Kubo%')
    ->whereRaw('DATE(bookings.check_in) = DATE(bookings.check_out)')
    ->orderBy('bookings.created_at', 'desc')
    ->first([
        'bookings.id as booking_id',
        'bookings.check_in',
        'bookings.check_out',
        'bookings.total_price',
        'rooms.name as room_name',
        'rooms.price as room_price'
    ]);

if ($booking) {
    echo "Booking ID: {$booking->booking_id}\n";
    echo "Room: {$booking->room_name}\n";
    echo "Check-in: {$booking->check_in}\n";
    echo "Check-out: {$booking->check_out}\n";
    echo "Room Price: " . ($booking->room_price ?? 'NULL') . "\n";
    echo "Total Price Saved: {$booking->total_price}\n";
} else {
    echo "No same-day bookings found for Bahay Kubo.\n";
}
