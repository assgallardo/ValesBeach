<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;

echo "Room Availability Status:\n\n";

$rooms = Room::all(['id', 'name', 'is_available']);

foreach ($rooms as $room) {
    $status = $room->is_available ? '✅ Available' : '❌ Not Available';
    echo "ID: {$room->id} | {$room->name} | {$status}\n";
}

echo "\nTotal rooms: " . $rooms->count() . "\n";
echo "Available rooms: " . $rooms->where('is_available', true)->count() . "\n";
echo "Unavailable rooms: " . $rooms->where('is_available', false)->count() . "\n";

// Check if there are any available rooms
$availableRooms = $rooms->where('is_available', true);
if ($availableRooms->count() == 0) {
    echo "\n❌ PROBLEM FOUND: No rooms are marked as available!\n";
    echo "   This is why guests cannot book rooms.\n";
    echo "   Solution: Update room is_available status to true\n";
} else {
    echo "\n✅ Available rooms exist. Issue might be elsewhere.\n";
}
