<?php
/**
 * Test Transaction ID Fix for Extra Charges
 * 
 * This verifies that extra charges added via invoice generation
 * use the SAME payment_transaction_id as the original payments
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\User;

echo "=== TRANSACTION ID FIX VERIFICATION ===\n\n";

// Find Mark Villanueva's transactions
$mark = User::where('email', 'mark.villanueva@gmail.com')->first();

if (!$mark) {
    echo "❌ Mark Villanueva not found\n";
    exit(1);
}

echo "Customer: {$mark->name} (ID: {$mark->id})\n\n";

// Get all Mark's payment transactions
$transactions = Payment::where('user_id', $mark->id)
    ->whereNotNull('payment_transaction_id')
    ->select('payment_transaction_id')
    ->distinct()
    ->get()
    ->pluck('payment_transaction_id');

echo "--- MARK'S PAYMENT TRANSACTIONS ---\n";
echo "Total Transactions: " . $transactions->count() . "\n\n";

foreach ($transactions as $txnId) {
    $payments = Payment::where('payment_transaction_id', $txnId)
        ->where('user_id', $mark->id)
        ->get();
    
    $bookings = $payments->whereNotNull('booking_id')->count();
    $services = $payments->whereNotNull('service_request_id')->count();
    $food = $payments->whereNotNull('food_order_id')->count();
    $extras = $payments->filter(fn($p) => strpos($p->payment_reference, 'EXT-') === 0)->count();
    
    $totalAmount = $payments->sum('amount');
    $completedCount = $payments->where('status', 'completed')->count();
    $totalCount = $payments->count();
    
    $allCompleted = ($totalCount > 0 && $totalCount === $completedCount);
    
    echo "Transaction: {$txnId}\n";
    echo "  Total Payments: {$totalCount}\n";
    echo "  - Bookings: {$bookings}\n";
    echo "  - Services: {$services}\n";
    echo "  - Food Orders: {$food}\n";
    echo "  - Extra Charges: {$extras}\n";
    echo "  Total Amount: ₱" . number_format($totalAmount, 2) . "\n";
    echo "  Status: " . ($allCompleted ? '✅ ALL COMPLETED' : '⏳ ACTIVE') . " ({$completedCount}/{$totalCount} completed)\n";
    
    // Check if this matches the bug scenario
    if ($extras > 0 && ($bookings > 0 || $services > 0 || $food > 0)) {
        echo "  ✅ CORRECT: Extra charge(s) grouped with other payment types\n";
    } elseif ($extras > 0 && $bookings === 0 && $services === 0 && $food === 0) {
        echo "  ❌ BUG: Extra charge(s) ISOLATED (should be grouped with bookings/services)\n";
    }
    
    echo "  Payments:\n";
    foreach ($payments as $payment) {
        $type = 'UNKNOWN';
        if ($payment->booking_id) $type = 'BOOKING';
        elseif ($payment->service_request_id) $type = 'SERVICE';
        elseif ($payment->food_order_id) $type = 'FOOD';
        elseif (strpos($payment->payment_reference, 'EXT-') === 0) {
            $details = $payment->payment_details;
            $desc = $details['description'] ?? 'Extra Charge';
            $type = "EXTRA ({$desc})";
        }
        
        echo "    - {$payment->payment_reference} | {$type} | ₱" . number_format($payment->amount, 2) . " | {$payment->status}\n";
    }
    echo "  ---\n\n";
}

// Check for the specific bug case
$isolatedExtras = Payment::where('user_id', $mark->id)
    ->whereNotNull('payment_transaction_id')
    ->whereNull('booking_id')
    ->whereNull('service_request_id')
    ->whereNull('food_order_id')
    ->where('payment_reference', 'LIKE', 'EXT-%')
    ->get();

echo "--- BUG CHECK ---\n";

$bugFound = false;
foreach ($isolatedExtras as $extra) {
    $txnId = $extra->payment_transaction_id;
    
    // Check if this transaction has ONLY extra charges (the bug)
    $paymentsInTxn = Payment::where('payment_transaction_id', $txnId)
        ->where('user_id', $mark->id)
        ->get();
    
    $hasOtherTypes = $paymentsInTxn->filter(function($p) {
        return !empty($p->booking_id) || !empty($p->service_request_id) || !empty($p->food_order_id);
    })->count() > 0;
    
    if (!$hasOtherTypes && $paymentsInTxn->count() > 0) {
        $details = $extra->payment_details;
        $desc = $details['description'] ?? 'Unknown';
        
        echo "❌ BUG DETECTED:\n";
        echo "   Extra Charge: {$desc}\n";
        echo "   Payment Reference: {$extra->payment_reference}\n";
        echo "   Transaction ID: {$txnId}\n";
        echo "   This transaction contains ONLY extra charge(s) - should be grouped with bookings/services!\n";
        echo "   Amount: ₱" . number_format($extra->amount, 2) . "\n";
        echo "   Status: {$extra->status}\n\n";
        $bugFound = true;
    }
}

if (!$bugFound) {
    echo "✅ NO BUG: All extra charges are properly grouped with other payment types\n";
}

echo "\n=== FIX VERIFICATION ===\n";
echo "After the fix:\n";
echo "1. The form now passes transaction_id as hidden field\n";
echo "2. saveCustomerInvoice() uses existing transaction_id instead of creating new one\n";
echo "3. Extra charges will stay grouped with original bookings/services\n";
echo "4. Completed transactions will show ALL items together\n\n";

echo "To test the fix:\n";
echo "1. Go to a customer's payment details\n";
echo "2. Click 'Generate Invoice' for an existing transaction\n";
echo "3. Add an extra charge\n";
echo "4. Save the invoice\n";
echo "5. Complete all payments\n";
echo "6. Check completed transactions - all items should be in ONE transaction\n\n";

echo "=== TEST COMPLETE ===\n";
