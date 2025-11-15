<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Room;

echo "Starting key number updates...\n\n";

// Update Bahay Kubo facilities
echo "Updating Bahay Kubo facilities...\n";
for ($i = 1; $i <= 8; $i++) {
    $room = Room::where('name', "Bahay Kubo $i")->first();
    if ($room) {
        $keyNumber = 'BK-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $room->key_number = $keyNumber;
        $room->save();
        echo "✓ {$room->name} -> {$keyNumber}\n";
    } else {
        echo "✗ Bahay Kubo $i not found\n";
    }
}

echo "\nUpdating Umbrella Cottages...\n";
for ($i = 1; $i <= 10; $i++) {
    $room = Room::where('name', "Umbrella Cottage $i")->first();
    if ($room) {
        $keyNumber = 'UC-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $room->key_number = $keyNumber;
        $room->save();
        echo "✓ {$room->name} -> {$keyNumber}\n";
    } else {
        echo "✗ Umbrella Cottage $i not found\n";
    }
}

echo "\nUpdating Standard Rooms...\n";
$standardRooms = ['Room 101', 'Room 102', 'Room 103'];
foreach ($standardRooms as $roomName) {
    $room = Room::where('name', $roomName)->first();
    if ($room) {
        // Extract room number from name (e.g., "Room 101" -> "101")
        preg_match('/\d+/', $roomName, $matches);
        $roomNumber = $matches[0];
        $keyNumber = 'R-' . $roomNumber;
        $room->key_number = $keyNumber;
        $room->save();
        echo "✓ {$room->name} -> {$keyNumber}\n";
    } else {
        echo "✗ {$roomName} not found\n";
    }
}

echo "\nUpdating Matrimonial Rooms...\n";
$matrimonialRooms = ['Room 201', 'Room 202', 'Room 203'];
foreach ($matrimonialRooms as $roomName) {
    $room = Room::where('name', $roomName)->first();
    if ($room) {
        // Extract room number from name
        preg_match('/\d+/', $roomName, $matches);
        $roomNumber = $matches[0];
        $keyNumber = 'R-' . $roomNumber;
        $room->key_number = $keyNumber;
        $room->save();
        echo "✓ {$room->name} -> {$keyNumber}\n";
    } else {
        echo "✗ {$roomName} not found\n";
    }
}

echo "\nUpdating Executive Cottage...\n";
$room = Room::where('name', 'Executive Cottage')->first();
if ($room) {
    $keyNumber = 'EC-001';
    $room->key_number = $keyNumber;
    $room->save();
    echo "✓ {$room->name} -> {$keyNumber}\n";
} else {
    echo "✗ Executive Cottage not found\n";
}

echo "\nUpdating Beer Garden...\n";
$room = Room::where('name', 'Beer Garden')->first();
if ($room) {
    $keyNumber = 'BG-001';
    $room->key_number = $keyNumber;
    $room->save();
    echo "✓ {$room->name} -> {$keyNumber}\n";
} else {
    echo "✗ Beer Garden not found\n";
}

echo "\nUpdating Dining Hall...\n";
$room = Room::where('name', 'Dining Hall')->first();
if ($room) {
    $keyNumber = 'DH-001';
    $room->key_number = $keyNumber;
    $room->save();
    echo "✓ {$room->name} -> {$keyNumber}\n";
} else {
    echo "✗ Dining Hall not found\n";
}

echo "\nUpdating Function Halls...\n";
$room = Room::where('name', 'Function Hall (Airconditioned)')->first();
if ($room) {
    $keyNumber = 'FH-AC';
    $room->key_number = $keyNumber;
    $room->save();
    echo "✓ {$room->name} -> {$keyNumber}\n";
} else {
    echo "✗ Function Hall (Airconditioned) not found\n";
}

$room = Room::where('name', 'Function Hall (Non-Airconditioned)')->first();
if ($room) {
    $keyNumber = 'FH-NAC';
    $room->key_number = $keyNumber;
    $room->save();
    echo "✓ {$room->name} -> {$keyNumber}\n";
} else {
    echo "✗ Function Hall (Non-Airconditioned) not found\n";
}

echo "\n✅ Key number update completed!\n";
echo "\nSummary:\n";
echo "- Bahay Kubo: BK-001 to BK-008\n";
echo "- Umbrella Cottage: UC-001 to UC-010\n";
echo "- Standard Rooms: R-101 to R-103\n";
echo "- Matrimonial Rooms: R-201 to R-203\n";
echo "- Executive Cottage: EC-001\n";
echo "- Beer Garden: BG-001\n";
echo "- Dining Hall: DH-001\n";
echo "- Function Halls: FH-AC, FH-NAC\n";
