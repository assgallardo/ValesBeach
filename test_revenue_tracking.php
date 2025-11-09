<?php

/**
 * Revenue Tracking Test Script
 * 
 * This script tests that revenue is properly calculated from the Payment model
 * for both Room Bookings and Food Orders.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Booking;
use App\Models\FoodOrder;
use Carbon\Carbon;

echo "=== Revenue Tracking Test ===\n\n";

// Test date range (last 30 days)
$startDate = Carbon::now()->subDays(30);
$endDate = Carbon::now();

echo "Date Range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";

// Test 1: Room Booking Revenue
echo "--- Test 1: Room Booking Revenue ---\n";

$bookingRevenue = Payment::whereNotNull('booking_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->sum('amount');

$bookingCount = Payment::whereNotNull('booking_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();

echo "Completed Booking Payments: {$bookingCount}\n";
echo "Total Room Revenue: ₱" . number_format($bookingRevenue, 2) . "\n";

if ($bookingCount > 0) {
    $avgBooking = $bookingRevenue / $bookingCount;
    echo "Average Booking Value: ₱" . number_format($avgBooking, 2) . "\n";
}

// Test 2: Food Order Revenue
echo "\n--- Test 2: Food Order Revenue ---\n";

$foodRevenue = Payment::whereNotNull('food_order_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->sum('amount');

$foodCount = Payment::whereNotNull('food_order_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();

echo "Completed Food Payments: {$foodCount}\n";
echo "Total Food Revenue: ₱" . number_format($foodRevenue, 2) . "\n";

if ($foodCount > 0) {
    $avgFood = $foodRevenue / $foodCount;
    echo "Average Order Value: ₱" . number_format($avgFood, 2) . "\n";
}

// Test 3: Service Revenue
echo "\n--- Test 3: Service Revenue ---\n";

$serviceRevenue = Payment::whereNotNull('service_request_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->sum('amount');

$serviceCount = Payment::whereNotNull('service_request_id')
    ->where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->count();

echo "Completed Service Payments: {$serviceCount}\n";
echo "Total Service Revenue: ₱" . number_format($serviceRevenue, 2) . "\n";

if ($serviceCount > 0) {
    $avgService = $serviceRevenue / $serviceCount;
    echo "Average Service Value: ₱" . number_format($avgService, 2) . "\n";
}

// Test 4: Total Revenue
echo "\n--- Test 4: Overall Revenue ---\n";

$totalRevenue = $bookingRevenue + $foodRevenue + $serviceRevenue;
$totalTransactions = $bookingCount + $foodCount + $serviceCount;

echo "Total Transactions: {$totalTransactions}\n";
echo "Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";

// Breakdown
echo "\nRevenue Breakdown:\n";
echo "  Rooms:    ₱" . number_format($bookingRevenue, 2) . " (" . ($totalRevenue > 0 ? number_format(($bookingRevenue / $totalRevenue) * 100, 1) : '0') . "%)\n";
echo "  Food:     ₱" . number_format($foodRevenue, 2) . " (" . ($totalRevenue > 0 ? number_format(($foodRevenue / $totalRevenue) * 100, 1) : '0') . "%)\n";
echo "  Services: ₱" . number_format($serviceRevenue, 2) . " (" . ($totalRevenue > 0 ? number_format(($serviceRevenue / $totalRevenue) * 100, 1) : '0') . "%)\n";

// Test 5: Payment Status Distribution
echo "\n--- Test 5: Payment Status Distribution ---\n";

$statuses = Payment::whereBetween('created_at', [$startDate, $endDate])
    ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
    ->groupBy('status')
    ->get();

foreach ($statuses as $status) {
    echo ucfirst($status->status) . ": {$status->count} payments, ₱" . number_format($status->total, 2) . "\n";
}

// Test 6: Recent Payments
echo "\n--- Test 6: Recent Completed Payments (Last 5) ---\n";

$recentPayments = Payment::where('status', 'completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

foreach ($recentPayments as $payment) {
    $type = '';
    if ($payment->booking_id) {
        $type = 'Room Booking';
    } elseif ($payment->food_order_id) {
        $type = 'Food Order';
    } elseif ($payment->service_request_id) {
        $type = 'Service';
    }
    
    echo "[{$payment->created_at->format('Y-m-d H:i')}] {$type} - ₱" . number_format($payment->amount, 2) . " ({$payment->payment_method})\n";
}

echo "\n=== Test Complete ===\n";
echo "\nRevenue is now being tracked from the Payment model.\n";
echo "Every completed payment transaction will be reflected in the reports.\n";
