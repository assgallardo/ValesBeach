<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Image URLs ===\n";

// Test room images
$room = App\Models\Room::with('images')->first();
if ($room && $room->images->count() > 0) {
    $image = $room->images->first();
    $url = asset('storage/' . $image->image_path);
    echo "Room: " . $room->name . "\n";
    echo "Image URL: " . $url . "\n";
    echo "File exists: " . (file_exists(public_path('storage/' . $image->image_path)) ? 'Yes' : 'No') . "\n";
    echo "Full path: " . public_path('storage/' . $image->image_path) . "\n";
    echo "---\n";
}

// Test service images
$service = App\Models\Service::whereNotNull('image')->first();
if ($service) {
    $url = asset('storage/' . $service->image);
    echo "Service: " . $service->name . "\n";
    echo "Image URL: " . $url . "\n";
    echo "File exists: " . (file_exists(public_path('storage/' . $service->image)) ? 'Yes' : 'No') . "\n";
    echo "Full path: " . public_path('storage/' . $service->image) . "\n";
    echo "---\n";
}

// Test if we can access the storage directory
echo "=== Storage Access Test ===\n";
$storagePath = public_path('storage');
echo "Storage directory: " . $storagePath . "\n";
echo "Directory exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "\n";
echo "Directory readable: " . (is_readable($storagePath) ? 'Yes' : 'No') . "\n";

if (is_dir($storagePath)) {
    $roomsDir = $storagePath . '/rooms';
    echo "Rooms directory: " . $roomsDir . "\n";
    echo "Rooms directory exists: " . (is_dir($roomsDir) ? 'Yes' : 'No') . "\n";
    
    if (is_dir($roomsDir)) {
        $files = scandir($roomsDir);
        $imageFiles = array_filter($files, function($f) { 
            return $f !== '.' && $f !== '..' && preg_match('/\.(jpg|jpeg|png|gif)$/i', $f); 
        });
        echo "Image files in rooms: " . implode(', ', $imageFiles) . "\n";
    }
}

