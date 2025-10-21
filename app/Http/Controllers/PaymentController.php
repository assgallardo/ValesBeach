<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display payment form for a booking
     */
    public function create(Booking $booking)
    {
        // Ensure user can access this booking
        if (Auth::user()->role !== 'admin' && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        // Check if booking is already fully paid
        if ($booking->payment_status === 'paid') {
            return redirect()->route('guest.bookings.show', $booking)
                ->with('info', 'This booking is already fully paid.');
        }

        // Calculate minimum payment (50% of total or 50% of remaining)
        $minimumPayment = $booking->amount_paid > 0 
            ? max(($booking->remaining_balance * 0.5), 1) 
            : max(($booking->total_price * 0.5), 1);

        $remainingBalance = $booking->remaining_balance;
        
        return view('payments.create', compact('booking', 'remainingBalance', 'minimumPayment'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Booking $booking)
    {
        // Validation
        $validated = $request->validate([
            'payment_amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($booking) {
                    $minimumPayment = $booking->amount_paid > 0 
                        ? ($booking->remaining_balance * 0.5) 
                        : ($booking->total_price * 0.5);
                    
                    if ($value < $minimumPayment && $value < $booking->remaining_balance) {
                        $fail('Payment amount must be at least 50% of the remaining balance (₱' . number_format($minimumPayment, 2) . ') or the full remaining balance.');
                    }
                    
                    if ($value > $booking->remaining_balance) {
                        $fail('Payment amount cannot exceed the remaining balance of ₱' . number_format($booking->remaining_balance, 2) . '.');
                    }
                },
            ],
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $paymentAmount = round((float) $request->payment_amount, 2);
            
            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'booking_id' => $booking->id,
                'payment_reference' => $this->generatePaymentReference(),
                'amount' => $paymentAmount,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
                'payment_date' => $request->payment_method === 'cash' ? now() : null,
                'notes' => $request->notes,
                'transaction_id' => $request->transaction_id ?? null,
            ]);

            // Update booking payment tracking
            $booking->updatePaymentTracking();

            // If payment is immediately completed (cash), update booking status
            if ($request->payment_method === 'cash') {
                if ($booking->payment_status === 'paid' && $booking->status === 'pending') {
                    $booking->update(['status' => 'confirmed']);
                }
            }

            DB::commit();
            
            return redirect()->route('payments.confirmation', $payment)
                ->with('success', 'Payment of ₱' . number_format($paymentAmount, 2) . ' processed successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment processing failed: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show payment confirmation
     */
    public function confirmation(Payment $payment)
    {
        // Ensure user can access this payment
        if (Auth::user()->role !== 'admin' && $payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        // Reload booking to get latest payment tracking
        if ($payment->booking) {
            $payment->booking->updatePaymentTracking();
            $payment->booking->refresh();
        }

        return view('payments.confirmation', compact('payment'));
    }

    /**
     * Generate payment reference
     */
    private function generatePaymentReference()
    {
        return 'PAY-' . strtoupper(uniqid());
    }
}
