<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Room Images Check ===\n";
$images = App\Models\RoomImage::all();
echo "Total room images: " . $images->count() . "\n\n";

foreach($images as $img) {
    echo "Room ID: " . $img->room_id . "\n";
    echo "Path: " . $img->image_path . "\n";
    echo "Featured: " . ($img->is_featured ? 'Yes' : 'No') . "\n";
    echo "Full URL: " . $img->image_url . "\n";
    echo "File exists: " . (file_exists(public_path('storage/' . $img->image_path)) ? 'Yes' : 'No') . "\n";
    echo "---\n";
}

echo "\n=== Services Check ===\n";
$services = App\Models\Service::all();
echo "Total services: " . $services->count() . "\n\n";

foreach($services as $service) {
    echo "Service: " . $service->name . "\n";
    echo "Image field: " . ($service->image ?? 'NULL') . "\n";
    echo "---\n";
}

echo "\n=== Room Images Relationship Test ===\n";
$room = App\Models\Room::with('images')->first();
if ($room) {
    echo "Room: " . $room->name . "\n";
    echo "Images count: " . $room->images->count() . "\n";
    echo "Images relationship loaded: " . ($room->relationLoaded('images') ? 'Yes' : 'No') . "\n";
    foreach($room->images as $img) {
        echo "Image: " . $img->image_path . " (exists: " . (file_exists(public_path('storage/' . $img->image_path)) ? 'Yes' : 'No') . ")\n";
    }
}

echo "\n=== Storage Directory Check ===\n";
$storagePath = storage_path('app/public');
echo "Storage path: " . $storagePath . "\n";
echo "Storage exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "\n";

$publicStoragePath = public_path('storage');
echo "Public storage path: " . $publicStoragePath . "\n";
echo "Public storage exists: " . (is_dir($publicStoragePath) ? 'Yes' : 'No') . "\n";
echo "Public storage is link: " . (is_link($publicStoragePath) ? 'Yes' : 'No') . "\n";

if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    echo "Files in storage: " . implode(', ', array_filter($files, function($f) { return $f !== '.' && $f !== '..'; })) . "\n";
}
