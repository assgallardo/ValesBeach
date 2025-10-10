<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

echo "Testing Guest Room Access Workflow...\n\n";

try {
    // Get guest user
    $guest = User::where('role', 'guest')->first();
    Auth::login($guest);
    echo "✅ Logged in as guest: {$guest->name}\n";
    
    // Test room browsing
    $rooms = Room::with('images')->where('is_available', true)->latest()->paginate(9);
    echo "✅ Found {$rooms->count()} available rooms\n";
    
    // Check the first room details
    $room = $rooms->first();
    if ($room) {
        echo "✅ Sample room: {$room->name} (₱{$room->price})\n";
        echo "   - Type: {$room->type}\n";
        echo "   - Capacity: {$room->capacity}\n";
        echo "   - Available: " . ($room->is_available ? 'Yes' : 'No') . "\n";
        echo "   - Images: {$room->images->count()}\n";
        
        // Test isAvailable calculation in show view
        // This might be where the issue is - let's check how $isAvailable is calculated
        
        echo "\nChecking room show logic...\n";
        
        // Simulate the show method logic - this might reveal the issue
        $isAvailable = $room->is_available; // This is probably the issue!
        
        echo "✅ Room is_available flag: " . ($room->is_available ? 'true' : 'false') . "\n";
        echo "✅ $isAvailable variable: " . ($isAvailable ? 'true' : 'false') . "\n";
        
        if (!$isAvailable) {
            echo "❌ ISSUE FOUND: Room is marked as not available!\n";
            echo "   This would prevent guests from seeing the booking form.\n";
        } else {
            echo "✅ Room shows as available - booking form should be visible\n";
        }
        
        // Check for existing bookings that might conflict
        $activeBookings = $room->bookings()
            ->where('status', '!=', 'cancelled')
            ->whereDate('check_out', '>=', now())
            ->count();
        
        echo "✅ Active bookings for this room: {$activeBookings}\n";
        
        // Test the booking form route
        $bookingFormUrl = route('guest.rooms.book', $room);
        echo "✅ Booking form URL: {$bookingFormUrl}\n";
        
        // Test the booking store route
        $bookingStoreUrl = route('guest.rooms.book.store', $room);
        echo "✅ Booking store URL: {$bookingStoreUrl}\n";
        
    } else {
        echo "❌ No available rooms found!\n";
    }
    
    echo "\n🔍 Summary:\n";
    echo "If guests can't book rooms, check:\n";
    echo "1. Room 'is_available' field in database\n";
    echo "2. Whether guests can access /guest/rooms/browse\n";
    echo "3. Whether the booking form appears in the room show view\n";
    echo "4. Browser console errors when submitting booking form\n";
    echo "5. Laravel logs when booking form is submitted\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
