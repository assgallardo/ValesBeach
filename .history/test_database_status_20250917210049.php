<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;

echo "Database Status Check\n";
echo "====================\n";

$guestUsers = User::where('role', 'guest')->count();
$allUsers = User::count();
$rooms = Room::count();
$bookings = Booking::count();

echo "Guest Users: {$guestUsers}\n";
echo "All Users: {$allUsers}\n"; 
echo "Rooms: {$rooms}\n";
echo "Bookings: {$bookings}\n";

if ($guestUsers == 0) {
    echo "\nERROR: No guest users found! BookingSeeder will skip creating bookings.\n";
}

if ($rooms == 0) {
    echo "\nERROR: No rooms found! BookingSeeder will skip creating bookings.\n";
}
