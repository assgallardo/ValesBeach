<?php
/**
 * Diagnostic: Find Extra Charge Issues
 * 
 * This script finds extra charges that might have issues:
 * 1. Missing payment_transaction_id
 * 2. Orphaned extra charges (not linked to any transaction)
 * 3. Extra charges not appearing in guest views
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== EXTRA CHARGE DIAGNOSTIC ===\n\n";

// Find ALL extra charges in the system
$allExtraCharges = Payment::whereNull('booking_id')
    ->whereNull('service_request_id')
    ->whereNull('food_order_id')
    ->where('payment_reference', 'LIKE', 'EXT-%')
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total Extra Charges in System: " . $allExtraCharges->count() . "\n\n";

if ($allExtraCharges->isEmpty()) {
    echo "ℹ️ No extra charges found in the system.\n";
    echo "   This is normal if you haven't created any extra charges yet.\n";
    exit(0);
}

// Check for issues
$issues = [
    'missing_transaction_id' => [],
    'orphaned' => [],
    'working_correctly' => []
];

foreach ($allExtraCharges as $extraCharge) {
    $issue_found = false;
    
    // Issue 1: Missing payment_transaction_id
    if (empty($extraCharge->payment_transaction_id)) {
        $issues['missing_transaction_id'][] = $extraCharge;
        $issue_found = true;
    }
    
    // Issue 2: Has transaction ID but is the only payment in that transaction (orphaned)
    if (!empty($extraCharge->payment_transaction_id)) {
        $paymentsInTransaction = Payment::where('payment_transaction_id', $extraCharge->payment_transaction_id)
            ->count();
        
        if ($paymentsInTransaction === 1) {
            $issues['orphaned'][] = $extraCharge;
            $issue_found = true;
        }
    }
    
    // If no issues found, it's working correctly
    if (!$issue_found) {
        $issues['working_correctly'][] = $extraCharge;
    }
}

// Report findings
echo "--- DIAGNOSTIC RESULTS ---\n\n";

// Issue 1: Missing Transaction ID
if (count($issues['missing_transaction_id']) > 0) {
    echo "❌ ISSUE 1: Extra Charges Missing payment_transaction_id (" . count($issues['missing_transaction_id']) . ")\n";
    echo "   These extra charges won't appear in guest payment views!\n\n";
    
    foreach ($issues['missing_transaction_id'] as $charge) {
        $customer = User::find($charge->user_id);
        $details = $charge->payment_details;
        $description = $details['description'] ?? 'Unknown';
        
        echo "   - Payment ID: {$charge->id}\n";
        echo "     Reference: {$charge->payment_reference}\n";
        echo "     Customer: " . ($customer ? $customer->name : 'Unknown') . " (ID: {$charge->user_id})\n";
        echo "     Description: {$description}\n";
        echo "     Amount: ₱" . number_format($charge->amount, 2) . "\n";
        echo "     Status: {$charge->status}\n";
        echo "     Created: {$charge->created_at}\n";
        echo "     ⚠️ FIX: This needs a payment_transaction_id assigned!\n";
        echo "     ---\n";
    }
    echo "\n";
} else {
    echo "✅ No extra charges with missing transaction_id\n\n";
}

// Issue 2: Orphaned Extra Charges
if (count($issues['orphaned']) > 0) {
    echo "⚠️ ISSUE 2: Orphaned Extra Charges (" . count($issues['orphaned']) . ")\n";
    echo "   These extra charges are alone in their transaction (no booking/service/food).\n";
    echo "   This is unusual but not necessarily wrong.\n\n";
    
    foreach ($issues['orphaned'] as $charge) {
        $customer = User::find($charge->user_id);
        $details = $charge->payment_details;
        $description = $details['description'] ?? 'Unknown';
        
        echo "   - Payment ID: {$charge->id}\n";
        echo "     Reference: {$charge->payment_reference}\n";
        echo "     Transaction ID: {$charge->payment_transaction_id}\n";
        echo "     Customer: " . ($customer ? $customer->name : 'Unknown') . " (ID: {$charge->user_id})\n";
        echo "     Description: {$description}\n";
        echo "     Amount: ₱" . number_format($charge->amount, 2) . "\n";
        echo "     Status: {$charge->status}\n";
        echo "     ℹ️ This extra charge is the only payment in its transaction.\n";
        echo "     ---\n";
    }
    echo "\n";
} else {
    echo "✅ No orphaned extra charges\n\n";
}

// Working Correctly
if (count($issues['working_correctly']) > 0) {
    echo "✅ WORKING CORRECTLY: Extra Charges Properly Linked (" . count($issues['working_correctly']) . ")\n\n";
    
    foreach ($issues['working_correctly'] as $charge) {
        $customer = User::find($charge->user_id);
        $details = $charge->payment_details;
        $description = $details['description'] ?? 'Unknown';
        
        // Count other payments in transaction
        $otherPayments = Payment::where('payment_transaction_id', $charge->payment_transaction_id)
            ->where('id', '!=', $charge->id)
            ->count();
        
        $transactionComplete = DB::table('payments')
            ->select('payment_transaction_id')
            ->where('payment_transaction_id', $charge->payment_transaction_id)
            ->groupBy('payment_transaction_id')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN status = ? THEN 1 ELSE 0 END)', ['completed'])
            ->exists();
        
        echo "   - Payment ID: {$charge->id}\n";
        echo "     Reference: {$charge->payment_reference}\n";
        echo "     Transaction ID: {$charge->payment_transaction_id}\n";
        echo "     Customer: " . ($customer ? $customer->name : 'Unknown') . " (ID: {$charge->user_id})\n";
        echo "     Description: {$description}\n";
        echo "     Amount: ₱" . number_format($charge->amount, 2) . "\n";
        echo "     Status: {$charge->status}\n";
        echo "     Other payments in transaction: {$otherPayments}\n";
        echo "     Guest View: " . ($transactionComplete ? 'Completed Transactions' : 'Payment History') . "\n";
        echo "     ---\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total Extra Charges: " . $allExtraCharges->count() . "\n";
echo "Missing Transaction ID: " . count($issues['missing_transaction_id']) . " ❌\n";
echo "Orphaned (alone in transaction): " . count($issues['orphaned']) . " ⚠️\n";
echo "Working Correctly: " . count($issues['working_correctly']) . " ✅\n";

if (count($issues['missing_transaction_id']) > 0) {
    echo "\n🔧 RECOMMENDED ACTION:\n";
    echo "   Extra charges without transaction_id need to be fixed.\n";
    echo "   Run a fix script to assign them to appropriate transactions.\n";
}

echo "\n";
