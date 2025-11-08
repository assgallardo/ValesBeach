<?php

/**
 * Comprehensive Booking Systems Test Script
 * Tests all booking types: Rooms, Cottages, Services, Food
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════\n";
echo "  COMPREHENSIVE BOOKING SYSTEMS TEST\n";
echo "═══════════════════════════════════════════════════\n\n";

$errors = [];
$warnings = [];

// Test 1: Check Rooms Table
echo "📋 Test 1: Checking Rooms Table...\n";
try {
    $rooms = DB::table('rooms')->get();
    $cottageTypes = ['Umbrella Cottage', 'Bahay Kubo'];
    $cottageRooms = DB::table('rooms')->whereIn('type', $cottageTypes)->count();
    
    if ($cottageRooms > 0) {
        $errors[] = "Found {$cottageRooms} cottage-type rooms in rooms table (should be in cottages table)";
        echo "   ❌ FAILED: {$cottageRooms} cottage-type rooms found in rooms table\n";
    } else {
        echo "   ✅ PASSED: No cottage-type rooms in rooms table\n";
    }
    
    echo "   📊 Total Rooms: " . count($rooms) . "\n";
    
    // Check for required fields
    foreach ($rooms as $room) {
        if (empty($room->name)) {
            $errors[] = "Room ID {$room->id} has no name";
        }
        if (!isset($room->price) || $room->price <= 0) {
            $errors[] = "Room ID {$room->id} has invalid price: " . ($room->price ?? 'NULL');
        }
        if (!isset($room->status)) {
            $errors[] = "Room ID {$room->id} has no status";
        }
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Rooms table error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 2: Check Cottages Table
echo "📋 Test 2: Checking Cottages Table...\n";
try {
    $cottages = DB::table('cottages')->get();
    echo "   📊 Total Cottages: " . count($cottages) . "\n";
    
    $incorrectPricing = [];
    foreach ($cottages as $cottage) {
        if (empty($cottage->name)) {
            $errors[] = "Cottage ID {$cottage->id} has no name";
        }
        if (!isset($cottage->price_per_day) || $cottage->price_per_day <= 0) {
            $errors[] = "Cottage ID {$cottage->id} has invalid price_per_day: " . ($cottage->price_per_day ?? 'NULL');
            $incorrectPricing[] = $cottage->name;
        }
        if (!isset($cottage->price_per_hour) || $cottage->price_per_hour <= 0) {
            $errors[] = "Cottage ID {$cottage->id} has invalid price_per_hour: " . ($cottage->price_per_hour ?? 'NULL');
        }
        
        // Check specific pricing
        if (stripos($cottage->name, 'umbrella') !== false) {
            if ($cottage->price_per_day != 350) {
                $warnings[] = "Umbrella Cottage '{$cottage->name}' has incorrect day rate: ₱{$cottage->price_per_day} (should be ₱350)";
            }
            if ($cottage->price_per_hour != 50) {
                $warnings[] = "Umbrella Cottage '{$cottage->name}' has incorrect hourly rate: ₱{$cottage->price_per_hour} (should be ₱50)";
            }
        } elseif (stripos($cottage->name, 'bahay') !== false || stripos($cottage->name, 'kubo') !== false) {
            if ($cottage->price_per_day != 200) {
                $warnings[] = "Bahay Kubo '{$cottage->name}' has incorrect day rate: ₱{$cottage->price_per_day} (should be ₱200)";
            }
            if ($cottage->price_per_hour != 30) {
                $warnings[] = "Bahay Kubo '{$cottage->name}' has incorrect hourly rate: ₱{$cottage->price_per_hour} (should be ₱30)";
            }
        }
    }
    
    if (count($cottages) > 0) {
        echo "   ✅ PASSED: Cottages table exists with data\n";
    } else {
        $warnings[] = "No cottages found in database";
        echo "   ⚠️  WARNING: No cottages in database\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Cottages table error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 3: Check Room Bookings
echo "📋 Test 3: Checking Room Bookings System...\n";
try {
    $recentRoomBookings = DB::table('bookings')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();
    
    echo "   📊 Room Bookings (last 30 days): {$recentRoomBookings}\n";
    
    // Check for required fields in bookings
    $invalidBookings = DB::table('bookings')
        ->whereNull('user_id')
        ->orWhereNull('room_id')
        ->orWhereNull('check_in')
        ->orWhereNull('check_out')
        ->count();
    
    if ($invalidBookings > 0) {
        $errors[] = "Found {$invalidBookings} room bookings with missing required fields";
        echo "   ❌ FAILED: {$invalidBookings} invalid bookings\n";
    } else {
        echo "   ✅ PASSED: All room bookings have required fields\n";
    }
    
    // Check for overlapping bookings (potential conflicts)
    $overlapping = DB::select("
        SELECT COUNT(*) as count FROM bookings b1
        INNER JOIN bookings b2 ON b1.room_id = b2.room_id 
        WHERE b1.id < b2.id
        AND b1.status NOT IN ('cancelled')
        AND b2.status NOT IN ('cancelled')
        AND (
            (b1.check_in BETWEEN b2.check_in AND b2.check_out)
            OR (b1.check_out BETWEEN b2.check_in AND b2.check_out)
            OR (b2.check_in BETWEEN b1.check_in AND b1.check_out)
        )
    ");
    
    if ($overlapping[0]->count > 0) {
        $warnings[] = "Found {$overlapping[0]->count} potential overlapping room bookings";
        echo "   ⚠️  WARNING: {$overlapping[0]->count} potential booking conflicts\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Room bookings error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 4: Check Cottage Bookings
echo "📋 Test 4: Checking Cottage Bookings System...\n";
try {
    $cottageBookingsTable = DB::table('cottage_bookings')->count();
    $recentCottageBookings = DB::table('cottage_bookings')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();
    
    echo "   📊 Total Cottage Bookings: {$cottageBookingsTable}\n";
    echo "   📊 Recent (30 days): {$recentCottageBookings}\n";
    
    // Check for required fields
    $invalidCottageBookings = DB::table('cottage_bookings')
        ->whereNull('user_id')
        ->orWhereNull('cottage_id')
        ->orWhereNull('check_in_date')
        ->count();
    
    if ($invalidCottageBookings > 0) {
        $errors[] = "Found {$invalidCottageBookings} cottage bookings with missing required fields";
        echo "   ❌ FAILED: {$invalidCottageBookings} invalid cottage bookings\n";
    } else {
        echo "   ✅ PASSED: All cottage bookings have required fields\n";
    }
    
    // Check booking types
    $bookingTypes = DB::table('cottage_bookings')
        ->select('booking_type', DB::raw('COUNT(*) as count'))
        ->groupBy('booking_type')
        ->get();
    
    echo "   📊 Booking Types:\n";
    foreach ($bookingTypes as $type) {
        echo "      - {$type->booking_type}: {$type->count}\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Cottage bookings error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 5: Check Services
echo "📋 Test 5: Checking Services System...\n";
try {
    $services = DB::table('services')->get();
    echo "   📊 Total Services: " . count($services) . "\n";
    
    $activeServices = DB::table('services')->where('is_available', true)->count();
    echo "   📊 Active Services: {$activeServices}\n";
    
    foreach ($services as $service) {
        if (empty($service->name)) {
            $errors[] = "Service ID {$service->id} has no name";
        }
        if (!isset($service->price) || $service->price < 0) {
            $errors[] = "Service ID {$service->id} has invalid price: " . ($service->price ?? 'NULL');
        }
    }
    
    if (count($services) > 0) {
        echo "   ✅ PASSED: Services table has data\n";
    } else {
        $warnings[] = "No services found in database";
        echo "   ⚠️  WARNING: No services in database\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Services error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 6: Check Service Requests
echo "📋 Test 6: Checking Service Requests System...\n";
try {
    $serviceRequests = DB::table('service_requests')->count();
    $recentRequests = DB::table('service_requests')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();
    
    echo "   📊 Total Service Requests: {$serviceRequests}\n";
    echo "   📊 Recent (30 days): {$recentRequests}\n";
    
    // Check statuses
    $statuses = DB::table('service_requests')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "   📊 Request Statuses:\n";
    foreach ($statuses as $status) {
        echo "      - {$status->status}: {$status->count}\n";
    }
    
    echo "   ✅ PASSED: Service requests system operational\n\n";
} catch (\Exception $e) {
    $errors[] = "Service requests error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 7: Check Food Ordering System
echo "📋 Test 7: Checking Food Ordering System...\n";
try {
    $menuItems = DB::table('menu_items')->count();
    $availableItems = DB::table('menu_items')->where('is_available', true)->count();
    
    echo "   📊 Total Menu Items: {$menuItems}\n";
    echo "   📊 Available Items: {$availableItems}\n";
    
    $foodOrders = DB::table('food_orders')->count();
    $recentOrders = DB::table('food_orders')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();
    
    echo "   📊 Total Food Orders: {$foodOrders}\n";
    echo "   📊 Recent (30 days): {$recentOrders}\n";
    
    // Check order statuses
    $orderStatuses = DB::table('food_orders')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
    
    echo "   📊 Order Statuses:\n";
    foreach ($orderStatuses as $status) {
        echo "      - {$status->status}: {$status->count}\n";
    }
    
    if ($menuItems > 0) {
        echo "   ✅ PASSED: Food ordering system operational\n";
    } else {
        $warnings[] = "No menu items found";
        echo "   ⚠️  WARNING: No menu items\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Food ordering error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 8: Check Routes and Controllers
echo "📋 Test 8: Checking Routes Configuration...\n";
try {
    $routeFiles = [
        'routes/web.php',
    ];
    
    $requiredRoutes = [
        'guest.rooms.browse',
        'guest.rooms.book',
        'guest.cottages.index',
        'guest.cottages.book',
        'guest.services.index',
        'guest.services.request',
        'guest.food-orders.menu',
        'guest.food-orders.checkout',
    ];
    
    $webRoutes = file_get_contents(__DIR__ . '/routes/web.php');
    $missingRoutes = [];
    
    foreach ($requiredRoutes as $route) {
        $routeName = str_replace('guest.', '', $route);
        $routeName = str_replace('.', '-', $routeName);
        
        if (strpos($webRoutes, "name('{$route}')") === false && 
            strpos($webRoutes, "name(\"{$route}\")") === false) {
            $missingRoutes[] = $route;
        }
    }
    
    if (count($missingRoutes) > 0) {
        $errors[] = "Missing routes: " . implode(', ', $missingRoutes);
        echo "   ❌ FAILED: Missing " . count($missingRoutes) . " required routes\n";
        foreach ($missingRoutes as $route) {
            echo "      - {$route}\n";
        }
    } else {
        echo "   ✅ PASSED: All required routes are configured\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "Routes check error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 9: Check View Files
echo "📋 Test 9: Checking View Files...\n";
try {
    $requiredViews = [
        'resources/views/guest/rooms/book.blade.php',
        'resources/views/guest/cottages/book.blade.php',
        'resources/views/guest/services/request.blade.php',
        'resources/views/food-orders/checkout.blade.php',
    ];
    
    $missingViews = [];
    foreach ($requiredViews as $view) {
        if (!file_exists(__DIR__ . '/' . $view)) {
            $missingViews[] = $view;
        }
    }
    
    if (count($missingViews) > 0) {
        $errors[] = "Missing view files: " . count($missingViews);
        echo "   ❌ FAILED: Missing " . count($missingViews) . " view files\n";
        foreach ($missingViews as $view) {
            echo "      - {$view}\n";
        }
    } else {
        echo "   ✅ PASSED: All required view files exist\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    $errors[] = "View files check error: " . $e->getMessage();
    echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Summary
echo "═══════════════════════════════════════════════════\n";
echo "  TEST SUMMARY\n";
echo "═══════════════════════════════════════════════════\n\n";

if (count($errors) === 0 && count($warnings) === 0) {
    echo "🎉 ALL TESTS PASSED! 🎉\n\n";
    echo "✅ All booking systems are working correctly:\n";
    echo "   ✓ Room Bookings\n";
    echo "   ✓ Cottage Bookings\n";
    echo "   ✓ Service Requests\n";
    echo "   ✓ Food Orders\n";
} else {
    if (count($errors) > 0) {
        echo "❌ ERRORS FOUND: " . count($errors) . "\n";
        echo "───────────────────────────────────────────────\n";
        foreach ($errors as $i => $error) {
            echo ($i + 1) . ". {$error}\n";
        }
        echo "\n";
    }
    
    if (count($warnings) > 0) {
        echo "⚠️  WARNINGS: " . count($warnings) . "\n";
        echo "───────────────────────────────────────────────\n";
        foreach ($warnings as $i => $warning) {
            echo ($i + 1) . ". {$warning}\n";
        }
        echo "\n";
    }
}

echo "═══════════════════════════════════════════════════\n";
echo "  Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo "═══════════════════════════════════════════════════\n";
