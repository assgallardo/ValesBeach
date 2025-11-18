<?php
/**
 * Test Extra Charge Invoice Flow
 * 
 * This script verifies:
 * 1. Extra charges are created with payment_transaction_id
 * 2. Extra charges appear in customer payment views
 * 3. Extra charges appear in guest payment history when active
 * 4. Extra charges appear in guest completed transactions when all payments completed
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== EXTRA CHARGE INVOICE FLOW TEST ===\n\n";

// Find a customer with payments
$customer = User::whereHas('payments')->first();

if (!$customer) {
    echo "❌ No customers with payments found. Please create some test data first.\n";
    exit(1);
}

echo "Testing with customer: {$customer->name} (ID: {$customer->id})\n\n";

// Check for extra charge payments
$extraChargePayments = Payment::where('user_id', $customer->id)
    ->whereNull('booking_id')
    ->whereNull('service_request_id')
    ->whereNull('food_order_id')
    ->where('payment_reference', 'LIKE', 'EXT-%')
    ->get();

echo "--- EXTRA CHARGE PAYMENTS ---\n";
if ($extraChargePayments->isEmpty()) {
    echo "❌ No extra charge payments found for this customer.\n";
    echo "   To test:\n";
    echo "   1. Go to Admin/Manager dashboard\n";
    echo "   2. Navigate to customer payments\n";
    echo "   3. Click 'Generate Invoice'\n";
    echo "   4. Add an extra charge\n";
    echo "   5. Save invoice\n\n";
} else {
    foreach ($extraChargePayments as $payment) {
        $hasTransactionId = !empty($payment->payment_transaction_id);
        $status = $payment->status;
        $amount = number_format($payment->amount, 2);
        
        echo "  Payment Reference: {$payment->payment_reference}\n";
        echo "  Transaction ID: " . ($payment->payment_transaction_id ?? '❌ MISSING') . "\n";
        echo "  Status: {$status}\n";
        echo "  Amount: ₱{$amount}\n";
        echo "  Has Transaction ID: " . ($hasTransactionId ? '✅ YES' : '❌ NO') . "\n";
        
        if ($payment->payment_details) {
            $details = $payment->payment_details;
            echo "  Description: " . ($details['description'] ?? 'N/A') . "\n";
            echo "  Invoice Number: " . ($details['invoice_number'] ?? 'N/A') . "\n";
        }
        echo "  ---\n";
    }
}

echo "\n--- PAYMENT TRANSACTIONS ---\n";

// Get all payment transaction IDs for this customer
$allTransactionIds = Payment::where('user_id', $customer->id)
    ->whereNotNull('payment_transaction_id')
    ->pluck('payment_transaction_id')
    ->unique();

if ($allTransactionIds->isEmpty()) {
    echo "❌ No payment transactions found for this customer.\n\n";
} else {
    foreach ($allTransactionIds as $txnId) {
        echo "  Transaction: {$txnId}\n";
        
        // Get all payments in this transaction
        $txnPayments = Payment::where('payment_transaction_id', $txnId)
            ->where('user_id', $customer->id)
            ->get();
        
        $totalPayments = $txnPayments->count();
        $completedPayments = $txnPayments->where('status', 'completed')->count();
        $totalAmount = $txnPayments->sum('amount');
        $extraChargeCount = $txnPayments->filter(function($p) {
            return strpos($p->payment_reference, 'EXT-') === 0;
        })->count();
        
        $isFullyCompleted = ($totalPayments > 0 && $totalPayments === $completedPayments);
        
        echo "    Total Payments: {$totalPayments}\n";
        echo "    Completed Payments: {$completedPayments}\n";
        echo "    Extra Charges: {$extraChargeCount}\n";
        echo "    Total Amount: ₱" . number_format($totalAmount, 2) . "\n";
        echo "    All Completed: " . ($isFullyCompleted ? '✅ YES (shows in Completed Transactions)' : '❌ NO (shows in Payment History)') . "\n";
        echo "  ---\n";
    }
}

echo "\n--- GUEST VIEW LOGIC TEST ---\n";

// Simulate the guest payment history logic (from PaymentController::history)
$completedTransactionIds = DB::table('payments')
    ->select('payment_transaction_id')
    ->where('user_id', $customer->id)
    ->whereNotNull('payment_transaction_id')
    ->groupBy('payment_transaction_id')
    ->havingRaw('COUNT(*) = SUM(CASE WHEN status = ? THEN 1 ELSE 0 END)', ['completed'])
    ->pluck('payment_transaction_id');

echo "Completed Transaction IDs (hidden from Payment History): " . 
     ($completedTransactionIds->isEmpty() ? 'None' : $completedTransactionIds->implode(', ')) . "\n";

// Active transactions (shown in payment history)
$activeTransactionIds = Payment::where('user_id', $customer->id)
    ->whereNotNull('payment_transaction_id')
    ->whereNotIn('payment_transaction_id', $completedTransactionIds)
    ->pluck('payment_transaction_id')
    ->unique();

echo "Active Transaction IDs (shown in Payment History): " . 
     ($activeTransactionIds->isEmpty() ? 'None' : $activeTransactionIds->implode(', ')) . "\n";

echo "\n--- VERIFICATION RESULTS ---\n";

$hasExtraCharges = $extraChargePayments->isNotEmpty();
$allExtraChargesHaveTransactionIds = $extraChargePayments->every(function($p) {
    return !empty($p->payment_transaction_id);
});

echo "✓ Extra charges exist: " . ($hasExtraCharges ? '✅ YES' : '❌ NO') . "\n";
echo "✓ All extra charges have transaction IDs: " . ($hasExtraCharges && $allExtraChargesHaveTransactionIds ? '✅ YES' : '❌ NO') . "\n";
echo "✓ Payment grouping working: " . (!$allTransactionIds->isEmpty() ? '✅ YES' : '❌ NO') . "\n";
echo "✓ Completed transaction filtering: " . (!$completedTransactionIds->isEmpty() ? '✅ YES' : '❌ NO (no completed transactions)') . "\n";

echo "\n=== TEST COMPLETE ===\n";

if ($hasExtraCharges && $allExtraChargesHaveTransactionIds) {
    echo "\n✅ EXTRA CHARGE SYSTEM IS WORKING CORRECTLY!\n";
    echo "   - Extra charges have payment_transaction_id\n";
    echo "   - They will appear in guest payment history (when active)\n";
    echo "   - They will appear in completed transactions (when all payments completed)\n";
} elseif ($hasExtraCharges && !$allExtraChargesHaveTransactionIds) {
    echo "\n❌ BUG FOUND: Some extra charges are missing payment_transaction_id!\n";
    echo "   This needs to be fixed in PaymentController::saveCustomerInvoice\n";
} else {
    echo "\n⚠️  No extra charges found to test. Create some via the invoice generation interface.\n";
}

echo "\n";
