<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

echo "Testing Booking Total Prices\n";
echo "============================\n";

$bookings = Booking::with('room')->take(5)->get();

foreach ($bookings as $booking) {
    $nights = Carbon::parse($booking->check_in)->diffInDays(Carbon::parse($booking->check_out));
    $expected = $booking->room->price * $nights;
    
    echo "Booking ID: {$booking->id}\n";
    echo "Room Price: ₱{$booking->room->price}\n";
    echo "Nights: {$nights}\n";
    echo "Expected Total: ₱{$expected}\n";
    echo "Stored Total: ₱{$booking->total_price}\n";
    echo "Formatted: {$booking->formatted_total_price}\n";
    echo "Match: " . ($booking->total_price == $expected ? 'YES' : 'NO') . "\n";
    echo "------------------------\n";
}
