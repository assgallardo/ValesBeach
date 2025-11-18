<?php
/**
 * Fix Isolated Extra Charges
 * 
 * This script fixes extra charges that were created with wrong transaction_id
 * by merging them back into their original transaction
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

echo "=== FIX ISOLATED EXTRA CHARGES ===\n\n";

// Find all isolated extra charges (transaction with ONLY extra charges)
$allExtraCharges = Payment::whereNull('booking_id')
    ->whereNull('service_request_id')
    ->whereNull('food_order_id')
    ->where('payment_reference', 'LIKE', 'EXT-%')
    ->whereNotNull('payment_transaction_id')
    ->get();

echo "Total Extra Charges: " . $allExtraCharges->count() . "\n\n";

$isolatedExtras = [];
$properExtras = [];

foreach ($allExtraCharges as $extra) {
    $txnId = $extra->payment_transaction_id;
    
    // Check if transaction has other payment types
    $paymentsInTxn = Payment::where('payment_transaction_id', $txnId)
        ->get();
    
    $hasOtherTypes = $paymentsInTxn->filter(function($p) {
        return !empty($p->booking_id) || !empty($p->service_request_id) || !empty($p->food_order_id);
    })->count() > 0;
    
    if (!$hasOtherTypes) {
        $isolatedExtras[] = $extra;
    } else {
        $properExtras[] = $extra;
    }
}

echo "Properly Grouped: " . count($properExtras) . " ✅\n";
echo "Isolated (Bug): " . count($isolatedExtras) . " ❌\n\n";

if (empty($isolatedExtras)) {
    echo "✅ No isolated extra charges found! All are properly grouped.\n";
    exit(0);
}

echo "--- ISOLATED EXTRA CHARGES TO FIX ---\n\n";

foreach ($isolatedExtras as $extra) {
    $details = $extra->payment_details;
    $description = $details['description'] ?? 'Unknown';
    $invoiceNumber = $details['invoice_number'] ?? null;
    
    echo "Extra Charge: {$description}\n";
    echo "  Payment ID: {$extra->id}\n";
    echo "  Reference: {$extra->payment_reference}\n";
    echo "  Current Transaction ID: {$extra->payment_transaction_id}\n";
    echo "  Amount: ₱" . number_format($extra->amount, 2) . "\n";
    echo "  Status: {$extra->status}\n";
    echo "  Customer ID: {$extra->user_id}\n";
    echo "  Invoice: {$invoiceNumber}\n";
    
    // Try to find the correct transaction to merge into
    $correctTxnId = null;
    
    // Method 1: Check invoice for the correct transaction ID
    if ($invoiceNumber) {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if ($invoice && !empty($invoice->payment_transaction_id)) {
            // Check if this transaction has other payments
            $txnPayments = Payment::where('payment_transaction_id', $invoice->payment_transaction_id)
                ->where('user_id', $extra->user_id)
                ->get();
            
            $hasOtherTypes = $txnPayments->filter(function($p) {
                return !empty($p->booking_id) || !empty($p->service_request_id) || !empty($p->food_order_id);
            })->count() > 0;
            
            if ($hasOtherTypes) {
                $correctTxnId = $invoice->payment_transaction_id;
                echo "  ✅ Found correct transaction from invoice: {$correctTxnId}\n";
            }
        }
    }
    
    // Method 2: Find the most recent transaction for this user with same completion status
    if (!$correctTxnId) {
        $recentTxn = Payment::where('user_id', $extra->user_id)
            ->whereNotNull('payment_transaction_id')
            ->where('payment_transaction_id', '!=', $extra->payment_transaction_id)
            ->where('status', $extra->status)
            ->whereNotNull('booking_id') // Must have booking
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($recentTxn) {
            $correctTxnId = $recentTxn->payment_transaction_id;
            echo "  ⚠️  Guessed transaction from recent booking: {$correctTxnId}\n";
        }
    }
    
    if ($correctTxnId) {
        echo "  → Will merge into transaction: {$correctTxnId}\n";
    } else {
        echo "  ❌ Cannot determine correct transaction - SKIP\n";
    }
    
    echo "  ---\n\n";
}

echo "\n=== APPLY FIX? ===\n";
echo "This will update " . count($isolatedExtras) . " extra charge payment(s).\n";
echo "Type 'yes' to continue, anything else to cancel: ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));
fclose($handle);

if ($line !== 'yes') {
    echo "\n❌ Cancelled. No changes made.\n";
    exit(0);
}

echo "\n--- APPLYING FIX ---\n\n";

$fixed = 0;
$skipped = 0;

foreach ($isolatedExtras as $extra) {
    $details = $extra->payment_details;
    $description = $details['description'] ?? 'Unknown';
    $invoiceNumber = $details['invoice_number'] ?? null;
    
    $correctTxnId = null;
    
    // Method 1: From invoice
    if ($invoiceNumber) {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if ($invoice && !empty($invoice->payment_transaction_id)) {
            $txnPayments = Payment::where('payment_transaction_id', $invoice->payment_transaction_id)
                ->where('user_id', $extra->user_id)
                ->get();
            
            $hasOtherTypes = $txnPayments->filter(function($p) {
                return !empty($p->booking_id) || !empty($p->service_request_id) || !empty($p->food_order_id);
            })->count() > 0;
            
            if ($hasOtherTypes) {
                $correctTxnId = $invoice->payment_transaction_id;
            }
        }
    }
    
    // Method 2: From recent booking
    if (!$correctTxnId) {
        $recentTxn = Payment::where('user_id', $extra->user_id)
            ->whereNotNull('payment_transaction_id')
            ->where('payment_transaction_id', '!=', $extra->payment_transaction_id)
            ->where('status', $extra->status)
            ->whereNotNull('booking_id')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($recentTxn) {
            $correctTxnId = $recentTxn->payment_transaction_id;
        }
    }
    
    if ($correctTxnId) {
        $oldTxnId = $extra->payment_transaction_id;
        $extra->payment_transaction_id = $correctTxnId;
        $extra->save();
        
        echo "✅ Fixed: {$description} ({$extra->payment_reference})\n";
        echo "   Changed transaction: {$oldTxnId} → {$correctTxnId}\n";
        $fixed++;
    } else {
        echo "⚠️  Skipped: {$description} ({$extra->payment_reference}) - cannot determine correct transaction\n";
        $skipped++;
    }
}

echo "\n=== FIX COMPLETE ===\n";
echo "Fixed: {$fixed}\n";
echo "Skipped: {$skipped}\n\n";

if ($fixed > 0) {
    echo "✅ Extra charges have been merged back into their original transactions!\n";
    echo "   Completed transactions should now show all items together.\n\n";
    echo "Verify by running: php verify_transaction_id_fix.php\n";
}

echo "\n";
