<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;

echo "Payment Status Check:\n\n";

$completedPayment = Payment::where('status', 'completed')->first();

if ($completedPayment) {
    echo "✓ Found completed payment\n";
    echo "  ID: {$completedPayment->id}\n";
    echo "  Amount: ₱" . number_format($completedPayment->amount, 2) . "\n";
    echo "  Date: {$completedPayment->created_at->format('Y-m-d H:i:s')}\n";
    echo "  Type: ";
    if ($completedPayment->booking_id) echo "Booking";
    elseif ($completedPayment->food_order_id) echo "Food Order";
    elseif ($completedPayment->service_request_id) echo "Service";
    echo "\n\n";
    
    // Check if it's within last 30 days
    $daysDiff = $completedPayment->created_at->diffInDays(now());
    echo "  Days ago: {$daysDiff} days\n";
    
    if ($daysDiff > 30) {
        echo "\n⚠️  This payment is older than 30 days.\n";
        echo "   Dashboard shows last 30 days by default.\n";
        echo "   Change date range to see this revenue.\n";
    }
} else {
    echo "⚠️  No completed payments found in database.\n";
    echo "   Revenue will display when payments are marked as 'completed'.\n";
}
