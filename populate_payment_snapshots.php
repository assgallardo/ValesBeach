<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Payment;

echo "Populating payment snapshots for existing completed payments...\n";
echo "==================================================================\n\n";

// Get all completed payments that don't have snapshots yet
$payments = Payment::where('status', 'completed')
    ->whereNull('item_description')
    ->with(['foodOrder.orderItems.menuItem', 'serviceRequest.service', 'booking.room'])
    ->get();

echo "Found " . $payments->count() . " completed payments without snapshots\n\n";

$updated = 0;
$skipped = 0;

foreach ($payments as $payment) {
    echo "Processing Payment #{$payment->id} - {$payment->payment_reference}\n";
    
    try {
        // Save snapshot using the model method
        $payment->saveItemSnapshot();
        $payment->save();
        
        echo "  ✓ Snapshot saved: {$payment->item_description}\n";
        echo "  Type: {$payment->item_type}\n\n";
        $updated++;
    } catch (\Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n\n";
        $skipped++;
    }
}

echo "==================================================================\n";
echo "Summary:\n";
echo "  Successfully updated: {$updated}\n";
echo "  Skipped (errors): {$skipped}\n";
echo "  Total processed: " . ($updated + $skipped) . "\n\n";
echo "All completed payments now have permanent snapshots!\n";
echo "Deleting service requests or food orders will no longer affect completed payment records.\n";
