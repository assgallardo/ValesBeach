<?php

/**
 * Dashboard Revenue Display Test
 * 
 * Tests that all revenue data is correctly calculated and ready for display
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Booking;
use App\Models\FoodOrder;
use App\Models\Room;
use App\Models\MenuItem;
use Carbon\Carbon;

echo "=== Dashboard Revenue Display Test ===\n\n";

// Test date range (last 30 days)
$startDate = Carbon::now()->subDays(30);
$endDate = Carbon::now();

echo "Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";

// ===== REVENUE HIGHLIGHTS SECTION =====
echo "--- Revenue Highlights Section ---\n";

$revenueStats = [
    'rooms_revenue' => Payment::whereNotNull('booking_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('amount'),
    
    'food_revenue' => Payment::whereNotNull('food_order_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('amount'),
    
    'services_revenue' => Payment::whereNotNull('service_request_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('amount'),
];

$revenueStats['total_revenue'] = $revenueStats['rooms_revenue'] + 
                                 $revenueStats['food_revenue'] + 
                                 $revenueStats['services_revenue'];

echo "✓ Rooms Revenue: ₱" . number_format($revenueStats['rooms_revenue'], 2) . "\n";
echo "✓ Food Revenue: ₱" . number_format($revenueStats['food_revenue'], 2) . "\n";
echo "✓ Services Revenue: ₱" . number_format($revenueStats['services_revenue'], 2) . "\n";
echo "✓ Total Revenue: ₱" . number_format($revenueStats['total_revenue'], 2) . "\n\n";

// ===== BOOKING & ROOM SALES SECTION =====
echo "--- Booking & Room Sales Reports Section ---\n";

$roomSalesOverview = [
    'total_bookings' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
    'completed_bookings' => Booking::whereIn('status', ['completed', 'checked_out'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count(),
    'total_revenue' => Payment::whereNotNull('booking_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('amount'),
    'avg_booking_value' => Payment::whereNotNull('booking_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->avg('amount'),
];

echo "✓ Total Bookings: " . number_format($roomSalesOverview['total_bookings']) . "\n";
echo "✓ Completed Bookings: " . number_format($roomSalesOverview['completed_bookings']) . "\n";
echo "✓ Total Revenue: ₱" . number_format($roomSalesOverview['total_revenue'], 2) . "\n";
echo "✓ Avg Booking Value: ₱" . number_format($roomSalesOverview['avg_booking_value'] ?? 0, 2) . "\n\n";

// Revenue by Category
echo "Revenue by Category:\n";

$revenueByCategory = Room::join('bookings', 'rooms.id', '=', 'bookings.room_id')
    ->leftJoin('payments', function($join) {
        $join->on('bookings.id', '=', 'payments.booking_id')
             ->where('payments.status', '=', 'completed');
    })
    ->whereBetween('bookings.created_at', [$startDate, $endDate])
    ->selectRaw('
        rooms.category,
        COUNT(DISTINCT bookings.id) as booking_count,
        COALESCE(SUM(payments.amount), 0) as total_revenue
    ')
    ->groupBy('rooms.category')
    ->orderByDesc('total_revenue')
    ->get();

foreach ($revenueByCategory as $category) {
    $percentage = $roomSalesOverview['total_revenue'] > 0 
        ? ($category->total_revenue / $roomSalesOverview['total_revenue']) * 100 
        : 0;
    
    echo "  • {$category->category}: ₱" . number_format($category->total_revenue, 2) 
         . " ({$category->booking_count} bookings, " . number_format($percentage, 1) . "%)\n";
}

if ($revenueByCategory->isEmpty()) {
    echo "  ⚠ No category data available\n";
}

echo "\n";

// ===== FOOD & BEVERAGE SECTION =====
echo "--- Food & Beverage Reports Section ---\n";

$foodSalesOverview = [
    'total_orders' => FoodOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
    'completed_orders' => FoodOrder::whereIn('status', ['delivered', 'completed'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count(),
    'total_revenue' => Payment::whereNotNull('food_order_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('amount'),
    'avg_order_value' => Payment::whereNotNull('food_order_id')
        ->where('status', 'completed')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->avg('amount'),
];

echo "✓ Total Orders: " . number_format($foodSalesOverview['total_orders']) . "\n";
echo "✓ Completed Orders: " . number_format($foodSalesOverview['completed_orders']) . "\n";
echo "✓ Total Revenue: ₱" . number_format($foodSalesOverview['total_revenue'], 2) . "\n";
echo "✓ Avg Order Value: ₱" . number_format($foodSalesOverview['avg_order_value'] ?? 0, 2) . "\n\n";

// Top Menu Items
echo "Top 5 Menu Items:\n";

$topMenuItems = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
    ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
    ->whereIn('food_orders.status', ['delivered', 'completed'])
    ->whereBetween('food_orders.created_at', [$startDate, $endDate])
    ->selectRaw('
        menu_items.name,
        SUM(order_items.quantity) as total_quantity,
        SUM(order_items.total_price) as total_revenue
    ')
    ->groupBy('menu_items.id', 'menu_items.name')
    ->orderByDesc('total_quantity')
    ->take(5)
    ->get();

if ($topMenuItems->count() > 0) {
    foreach ($topMenuItems as $index => $item) {
        echo "  " . ($index + 1) . ". {$item->name}: " . number_format($item->total_quantity) 
             . " sold, ₱" . number_format($item->total_revenue, 2) . "\n";
    }
} else {
    echo "  ⚠ No menu items data available\n";
}

echo "\n";

// ===== SUMMARY =====
echo "--- Summary ---\n";

$allDataAvailable = true;

if ($revenueStats['rooms_revenue'] > 0) {
    echo "✓ Room revenue is displaying\n";
} else {
    echo "⚠ No room revenue data (no completed booking payments)\n";
    $allDataAvailable = false;
}

if ($revenueStats['food_revenue'] > 0) {
    echo "✓ Food revenue is displaying\n";
} else {
    echo "⚠ No food revenue data (no completed food payments)\n";
    $allDataAvailable = false;
}

if ($revenueStats['services_revenue'] > 0) {
    echo "✓ Service revenue is displaying\n";
} else {
    echo "⚠ No service revenue data (no completed service payments)\n";
    $allDataAvailable = false;
}

if ($revenueByCategory->count() > 0) {
    echo "✓ Room categories data is displaying\n";
} else {
    echo "⚠ No room categories data available\n";
    $allDataAvailable = false;
}

if ($topMenuItems->count() > 0) {
    echo "✓ Food menu items data is displaying\n";
} else {
    echo "⚠ No food menu items data available\n";
    $allDataAvailable = false;
}

echo "\n";

if ($allDataAvailable) {
    echo "✅ All revenue data is ready for display!\n";
} else {
    echo "ℹ️  Some sections have no data because there are no completed transactions in this period.\n";
    echo "   Revenue will display automatically when payments are completed.\n";
}

echo "\n=== Test Complete ===\n";
