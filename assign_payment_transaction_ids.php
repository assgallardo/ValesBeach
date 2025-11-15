<?php

/**
 * Assign payment_transaction_id to all existing payments
 * This script groups existing payments by user into a single transaction
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

echo "Starting payment transaction ID assignment...\n\n";

// Get all users who have payments
$usersWithPayments = User::whereHas('payments')->get();

$totalUpdated = 0;

foreach ($usersWithPayments as $user) {
    echo "Processing user: {$user->name} (ID: {$user->id})\n";
    
    // Get all payments for this user
    $payments = Payment::where('user_id', $user->id)
        ->orderBy('created_at', 'asc')
        ->get();
    
    if ($payments->isEmpty()) {
        continue;
    }
    
    // Generate a transaction ID for this user's existing payments
    $transactionId = 'TXN-' . strtoupper(Str::random(12));
    
    // Update all existing payments with this transaction ID
    $updatedCount = Payment::where('user_id', $user->id)
        ->whereNull('payment_transaction_id')
        ->update(['payment_transaction_id' => $transactionId]);
    
    echo "  - Assigned transaction ID: {$transactionId}\n";
    echo "  - Updated {$updatedCount} payments\n\n";
    
    $totalUpdated += $updatedCount;
}

echo "\n✅ Complete! Updated {$totalUpdated} total payments.\n";
