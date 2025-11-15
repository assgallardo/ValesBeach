<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the payment by reference
$payment = App\Models\Payment::where('payment_reference', 'PAY-691754242C913')->first();

if ($payment) {
    echo "Payment Found: {$payment->payment_reference}\n";
    echo "Stored Amount: {$payment->amount}\n";
    echo "Payment Status: {$payment->status}\n\n";
    
    if ($payment->booking) {
        echo "Booking Found: {$payment->booking->booking_reference}\n";
        echo "Booking ID: {$payment->booking_id}\n";
        
        // Check what attributes exist
        echo "\nBooking Attributes:\n";
        echo "check_in: " . ($payment->booking->check_in ?? 'NULL') . "\n";
        echo "check_out: " . ($payment->booking->check_out ?? 'NULL') . "\n";
        echo "check_in_date accessor: " . ($payment->booking->check_in_date ?? 'NULL') . "\n";
        echo "check_out_date accessor: " . ($payment->booking->check_out_date ?? 'NULL') . "\n";
        
        if ($payment->booking->room) {
            echo "\nRoom: {$payment->booking->room->name}\n";
            echo "Room Price: {$payment->booking->room->price}\n";
        }
        
        // Test the calculation manually
        $checkIn = $payment->booking->check_in_date ?? $payment->booking->check_in;
        $checkOut = $payment->booking->check_out_date ?? $payment->booking->check_out;
        
        if ($checkIn && $checkOut) {
            $checkIn = \Carbon\Carbon::parse($checkIn)->startOfDay();
            $checkOut = \Carbon\Carbon::parse($checkOut)->startOfDay();
            $nights = $checkIn->diffInDays($checkOut);
            
            echo "\nCalculation:\n";
            echo "Check-in: {$checkIn}\n";
            echo "Check-out: {$checkOut}\n";
            echo "Diff in days: {$nights}\n";
            echo "Type of nights: " . gettype($nights) . "\n";
            echo "Is nights === 0? " . ($nights === 0 ? 'YES' : 'NO') . "\n";
            echo "Is nights == 0? " . ($nights == 0 ? 'YES' : 'NO') . "\n";
            
            if ($nights === 0) {
                $nights = 1;
                echo "Adjusted to: {$nights} night(s)\n";
            }
            
            $amount = $payment->booking->room->price * $nights;
            echo "Calculated Amount: {$amount}\n";
        }
    }
    
    echo "\nCalculated Amount Accessor: " . $payment->calculated_amount . "\n";
    echo "Can Be Refunded: " . ($payment->canBeRefunded() ? 'YES' : 'NO') . "\n";
    echo "Remaining Refundable: " . $payment->getRemainingRefundableAmount() . "\n";
} else {
    echo "Payment not found\n";
}
