<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Room View Data ===\n";

// Simulate what the controller does
$rooms = App\Models\Room::with('images')->where('is_available', true)->get();

echo "Total rooms: " . $rooms->count() . "\n";

foreach($rooms as $room) {
    echo "\nRoom: " . $room->name . "\n";
    echo "Images count: " . $room->images->count() . "\n";
    echo "Has images: " . ($room->images->isNotEmpty() ? 'Yes' : 'No') . "\n";
    
    if ($room->images->isNotEmpty()) {
        $firstImage = $room->images->first();
        echo "First image path: " . $firstImage->image_path . "\n";
        echo "Image URL: " . asset('storage/' . $firstImage->image_path) . "\n";
        echo "File exists: " . (file_exists(public_path('storage/' . $firstImage->image_path)) ? 'Yes' : 'No') . "\n";
    } else {
        echo "No images for this room\n";
    }
    echo "---\n";
}

echo "\n=== Testing Service View Data ===\n";
$services = App\Models\Service::all();

foreach($services as $service) {
    echo "\nService: " . $service->name . "\n";
    echo "Has image: " . (!empty($service->image) ? 'Yes' : 'No') . "\n";
    
    if (!empty($service->image)) {
        echo "Image path: " . $service->image . "\n";
        echo "Image URL: " . asset('storage/' . $service->image) . "\n";
        echo "File exists: " . (file_exists(public_path('storage/' . $service->image)) ? 'Yes' : 'No') . "\n";
    } else {
        echo "No image for this service\n";
    }
    echo "---\n";
}

