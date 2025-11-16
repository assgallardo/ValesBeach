<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\User;

echo "=== Testing Cancelled Payment Total Calculations ===\n\n";

// Find a customer with payments
$customer = User::whereHas('payments')->first();

if (!$customer) {
    echo "No customer with payments found. Create some test data first.\n";
    exit;
}

echo "Customer: {$customer->name} (ID: {$customer->id})\n";
echo "Email: {$customer->email}\n\n";

// Get all payments for this customer
$allPayments = $customer->payments;

echo "Total Payments: {$allPayments->count()}\n";
echo "Payment Status Breakdown:\n";

$statusCounts = $allPayments->groupBy('status');
foreach ($statusCounts as $status => $payments) {
    $total = $payments->sum('amount');
    echo "  - {$status}: {$payments->count()} payments, ₱" . number_format($total, 2) . "\n";
}

echo "\n=== TOTAL CALCULATIONS ===\n\n";

// OLD METHOD (includes cancelled)
$oldTotal = $allPayments->sum('amount');
echo "OLD Total (includes cancelled): ₱" . number_format($oldTotal, 2) . "\n";

// NEW METHOD (excludes cancelled)
$newTotal = $allPayments->whereNotIn('status', ['cancelled'])->sum('amount');
echo "NEW Total (excludes cancelled): ₱" . number_format($newTotal, 2) . "\n";

$difference = $oldTotal - $newTotal;
echo "Difference (cancelled amount): ₱" . number_format($difference, 2) . "\n\n";

// Breakdown by type (excluding cancelled)
echo "=== BREAKDOWN BY TYPE (Excluding Cancelled) ===\n\n";

$bookingTotal = $allPayments->where('booking_id', '!=', null)->whereNotIn('status', ['cancelled'])->sum('amount');
$bookingCount = $allPayments->where('booking_id', '!=', null)->whereNotIn('status', ['cancelled'])->count();
echo "Bookings: {$bookingCount} payments, ₱" . number_format($bookingTotal, 2) . "\n";

$serviceTotal = $allPayments->where('service_request_id', '!=', null)->whereNotIn('status', ['cancelled'])->sum('amount');
$serviceCount = $allPayments->where('service_request_id', '!=', null)->whereNotIn('status', ['cancelled'])->count();
echo "Services: {$serviceCount} payments, ₱" . number_format($serviceTotal, 2) . "\n";

$foodTotal = $allPayments->where('food_order_id', '!=', null)->whereNotIn('status', ['cancelled'])->sum('amount');
$foodCount = $allPayments->where('food_order_id', '!=', null)->whereNotIn('status', ['cancelled'])->count();
echo "Food Orders: {$foodCount} payments, ₱" . number_format($foodTotal, 2) . "\n";

$extraTotal = $allPayments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->whereNotIn('status', ['cancelled'])->sum('amount');
$extraCount = $allPayments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->whereNotIn('status', ['cancelled'])->count();
echo "Extra Charges: {$extraCount} payments, ₱" . number_format($extraTotal, 2) . "\n";

$calculatedTotal = $bookingTotal + $serviceTotal + $foodTotal + $extraTotal;
echo "\nCalculated Total: ₱" . number_format($calculatedTotal, 2) . "\n";
echo "Verification: " . ($calculatedTotal == $newTotal ? "✓ MATCH" : "✗ MISMATCH") . "\n\n";

// List cancelled payments
$cancelledPayments = $allPayments->where('status', 'cancelled');
if ($cancelledPayments->count() > 0) {
    echo "=== CANCELLED PAYMENTS (Should be excluded from totals) ===\n\n";
    foreach ($cancelledPayments as $payment) {
        $type = 'Unknown';
        if ($payment->booking_id) $type = 'Booking';
        elseif ($payment->service_request_id) $type = 'Service';
        elseif ($payment->food_order_id) $type = 'Food Order';
        else $type = 'Extra Charge';
        
        echo "Payment #{$payment->id} - {$payment->payment_reference}\n";
        echo "  Type: {$type}\n";
        echo "  Amount: ₱" . number_format($payment->amount, 2) . "\n";
        echo "  Status: {$payment->status}\n\n";
    }
} else {
    echo "No cancelled payments found for this customer.\n\n";
}

echo "=== TEST COMPLETE ===\n";
