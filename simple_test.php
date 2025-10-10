<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

echo "Simple Guest Booking Test...\n\n";

try {
    // Find guest user
    $guest = User::where('role', 'guest')->first();
    echo "Guest user: {$guest->email}\n";
    
    // Login guest
    Auth::login($guest);
    echo "Authenticated as: " . Auth::user()->name . "\n";
    
    // Find room
    $room = Room::where('is_available', true)->first();
    echo "Available room: {$room->name}\n";
    
    // Test route generation
    echo "\nRoute URLs:\n";
    echo "- Browse: " . route('guest.rooms.browse') . "\n";
    echo "- Show: " . route('guest.rooms.show', $room->id) . "\n";
    echo "- Book: " . route('guest.rooms.book', $room->id) . "\n";
    echo "- Store: " . route('guest.rooms.book.store', $room->id) . "\n";
    
    // Test URL pattern matching
    echo "\nURL Analysis:\n";
    $storeUrl = route('guest.rooms.book.store', $room->id);
    echo "Store URL: {$storeUrl}\n";
    
    // Check what the form should submit to
    echo "Expected form action: " . route('guest.rooms.book.store', $room->id) . "\n";
    
    echo "\n✅ All routes generate correctly!\n";
    echo "\nTo test the issue:\n";
    echo "1. Visit: http://127.0.0.1:8000/login\n";
    echo "2. Login as: {$guest->email} / password123\n";
    echo "3. Navigate to: http://127.0.0.1:8000/guest/rooms\n";
    echo "4. Try to book a room and check for errors\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
