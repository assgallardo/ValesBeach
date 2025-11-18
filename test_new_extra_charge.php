<?php
/**
 * Test New Extra Charge Creation Flow
 * 
 * This test simulates what happens when an admin/manager adds extra charges
 * via the invoice generation page
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\User;

echo "=== NEW EXTRA CHARGE CREATION TEST ===\n\n";

// Find a customer with an active transaction (not all completed)
$customer = User::whereHas('payments', function($q) {
    $q->whereNotNull('payment_transaction_id');
})->first();

if (!$customer) {
    echo "❌ No customers with transactions found.\n";
    exit(1);
}

echo "Customer: {$customer->name} (ID: {$customer->id})\n\n";

// Get an active transaction (not all payments completed)
$activeTransaction = \Illuminate\Support\Facades\DB::table('payments')
    ->select('payment_transaction_id')
    ->where('user_id', $customer->id)
    ->whereNotNull('payment_transaction_id')
    ->groupBy('payment_transaction_id')
    ->havingRaw('COUNT(*) > SUM(CASE WHEN status = ? THEN 1 ELSE 0 END)', ['completed'])
    ->first();

if (!$activeTransaction) {
    echo "⚠️ No active transactions found. All transactions are fully completed.\n";
    echo "   Finding any transaction to use for testing...\n\n";
    
    $anyTransaction = Payment::where('user_id', $customer->id)
        ->whereNotNull('payment_transaction_id')
        ->first();
    
    if (!$anyTransaction) {
        echo "❌ Customer has no transactions at all.\n";
        exit(1);
    }
    
    $transactionId = $anyTransaction->payment_transaction_id;
    echo "Using transaction: {$transactionId}\n\n";
} else {
    $transactionId = $activeTransaction->payment_transaction_id;
    echo "Using active transaction: {$transactionId}\n\n";
}

// Show current state of transaction before adding extra charge
$beforePayments = Payment::where('payment_transaction_id', $transactionId)
    ->where('user_id', $customer->id)
    ->get();

echo "--- TRANSACTION STATE BEFORE ---\n";
echo "Total Payments: " . $beforePayments->count() . "\n";
echo "Completed Payments: " . $beforePayments->where('status', 'completed')->count() . "\n";
echo "Total Amount: ₱" . number_format($beforePayments->sum('amount'), 2) . "\n";
echo "Extra Charges: " . $beforePayments->filter(fn($p) => strpos($p->payment_reference, 'EXT-') === 0)->count() . "\n";

echo "\nPayments in transaction:\n";
foreach ($beforePayments as $payment) {
    $type = 'UNKNOWN';
    if ($payment->booking_id) $type = 'BOOKING';
    elseif ($payment->service_request_id) $type = 'SERVICE';
    elseif ($payment->food_order_id) $type = 'FOOD';
    elseif (strpos($payment->payment_reference, 'EXT-') === 0) $type = 'EXTRA CHARGE';
    
    echo "  - {$payment->payment_reference} ({$type}) - ₱" . number_format($payment->amount, 2) . " - {$payment->status}\n";
}

// Simulate creating a new extra charge payment (what the AJAX endpoint does)
echo "\n--- CREATING NEW EXTRA CHARGE ---\n";

try {
    $newExtraCharge = Payment::create([
        'user_id' => $customer->id,
        'amount' => 250.00,
        'payment_method' => 'cash',
        'payment_reference' => 'EXT-' . strtoupper(uniqid()),
        'payment_transaction_id' => $transactionId, // CRITICAL: must use same transaction ID
        'status' => 'pending',
        'payment_date' => now(),
        'payment_details' => [
            'description' => 'Test Extra Charge - Pool equipment rental',
            'reference' => 'TEST-REF-001',
            'details' => 'Rented pool floaties for 2 hours',
            'type' => 'extra',
            'item_type' => 'extra_charge',
            'saved_individually' => true,
            'created_by' => 'System Test',
            'created_at' => now()->toDateTimeString()
        ]
    ]);
    
    echo "✅ Extra charge created successfully!\n";
    echo "   Payment ID: {$newExtraCharge->id}\n";
    echo "   Payment Reference: {$newExtraCharge->payment_reference}\n";
    echo "   Transaction ID: {$newExtraCharge->payment_transaction_id}\n";
    echo "   Amount: ₱" . number_format($newExtraCharge->amount, 2) . "\n";
    echo "   Status: {$newExtraCharge->status}\n";
    
} catch (\Exception $e) {
    echo "❌ Failed to create extra charge: " . $e->getMessage() . "\n";
    exit(1);
}

// Show transaction state after adding extra charge
$afterPayments = Payment::where('payment_transaction_id', $transactionId)
    ->where('user_id', $customer->id)
    ->get();

echo "\n--- TRANSACTION STATE AFTER ---\n";
echo "Total Payments: " . $afterPayments->count() . "\n";
echo "Completed Payments: " . $afterPayments->where('status', 'completed')->count() . "\n";
echo "Total Amount: ₱" . number_format($afterPayments->sum('amount'), 2) . "\n";
echo "Extra Charges: " . $afterPayments->filter(fn($p) => strpos($p->payment_reference, 'EXT-') === 0)->count() . "\n";

echo "\nPayments in transaction:\n";
foreach ($afterPayments as $payment) {
    $type = 'UNKNOWN';
    if ($payment->booking_id) $type = 'BOOKING';
    elseif ($payment->service_request_id) $type = 'SERVICE';
    elseif ($payment->food_order_id) $type = 'FOOD';
    elseif (strpos($payment->payment_reference, 'EXT-') === 0) $type = 'EXTRA CHARGE';
    
    $isNew = ($payment->id === $newExtraCharge->id) ? ' ⭐ NEW' : '';
    echo "  - {$payment->payment_reference} ({$type}) - ₱" . number_format($payment->amount, 2) . " - {$payment->status}{$isNew}\n";
}

// Check if this transaction is now visible in guest views
echo "\n--- GUEST VIEW VISIBILITY ---\n";

// Check if transaction is completed (all payments completed)
$allCompleted = ($afterPayments->count() > 0 && $afterPayments->count() === $afterPayments->where('status', 'completed')->count());

if ($allCompleted) {
    echo "Transaction status: ✅ FULLY COMPLETED\n";
    echo "Visibility: Shows in 'Completed Transactions' view\n";
    echo "Hidden from: Payment History (active payments)\n";
} else {
    echo "Transaction status: ⏳ ACTIVE (has incomplete payments)\n";
    echo "Visibility: Shows in 'Payment History' view\n";
    echo "Hidden from: Completed Transactions (not all completed)\n";
}

echo "\n--- CLEANUP ---\n";
echo "Do you want to delete the test extra charge? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) === 'y') {
    $newExtraCharge->delete();
    echo "✅ Test extra charge deleted.\n";
} else {
    echo "ℹ️ Test extra charge kept. You can delete it manually later.\n";
    echo "   Payment ID: {$newExtraCharge->id}\n";
}
fclose($handle);

echo "\n=== TEST COMPLETE ===\n";
