<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all payments without transaction IDs
        $paymentsWithoutTxnId = Payment::whereNull('payment_transaction_id')->get();
        
        if ($paymentsWithoutTxnId->isNotEmpty()) {
            echo "Backfilling " . $paymentsWithoutTxnId->count() . " payments without transaction IDs...\n";
            
            // Group payments by user and assign transaction IDs
            $grouped = $paymentsWithoutTxnId->groupBy('user_id');
            
            foreach ($grouped as $userId => $userPayments) {
                // Further group by booking_id for booking payments
                $bookingGroups = $userPayments->groupBy('booking_id');
                
                foreach ($bookingGroups as $bookingId => $bookingPayments) {
                    if ($bookingId) {
                        // All payments for the same booking get the same transaction ID
                        $txnId = 'TXN-' . strtoupper(Str::random(12));
                        
                        foreach ($bookingPayments as $payment) {
                            $payment->update(['payment_transaction_id' => $txnId]);
                        }
                        
                        echo "  - Assigned $txnId to " . $bookingPayments->count() . " payment(s) for booking $bookingId\n";
                    } else {
                        // Non-booking payments (services, food, etc.) - assign individual transaction IDs
                        foreach ($bookingPayments as $payment) {
                            $txnId = 'TXN-' . strtoupper(Str::random(12));
                            $payment->update(['payment_transaction_id' => $txnId]);
                            
                            $type = $payment->service_request_id ? 'service' : ($payment->food_order_id ? 'food' : 'extra charge');
                            echo "  - Assigned $txnId to $type payment ID {$payment->id}\n";
                        }
                    }
                }
            }
            
            echo "Backfill complete!\n";
        } else {
            echo "No payments found without transaction IDs.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear the backfilled transaction IDs
        // (only if you want to be able to reverse this)
        echo "Note: This migration's down() method does not remove transaction IDs.\n";
        echo "If you need to reverse, you'll need to identify which IDs were backfilled.\n";
    }
};
