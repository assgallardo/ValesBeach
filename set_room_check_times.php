<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;

echo "Setting check-in and check-out times for rooms...\n";
echo "=================================================\n\n";

// Get all rooms with category 'Rooms'
$rooms = Room::where('category', 'Rooms')->get();

echo "Found " . $rooms->count() . " facilities with category 'Rooms'\n\n";

if ($rooms->isEmpty()) {
    echo "No rooms found with category 'Rooms'.\n";
    exit;
}

// Update each room
$updated = 0;
foreach ($rooms as $room) {
    echo "Processing: {$room->name} (ID: {$room->id})\n";
    echo "  Current check-in: " . ($room->check_in_time ?? 'Not set') . "\n";
    echo "  Current check-out: " . ($room->check_out_time ?? 'Not set') . "\n";
    
    $room->check_in_time = '13:00:00';  // 1:00 PM
    $room->check_out_time = '12:00:00'; // 12:00 PM
    $room->save();
    
    echo "  ✓ Updated to check-in: 13:00:00 (1:00 PM), check-out: 12:00:00 (12:00 PM)\n\n";
    $updated++;
}

echo "=================================================\n";
echo "Successfully updated {$updated} room(s)!\n";
echo "All rooms categorized under 'Rooms' now have:\n";
echo "  - Check-in time: 1:00 PM\n";
echo "  - Check-out time: 12:00 PM\n";
