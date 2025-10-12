<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ServiceRequest;
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
        if ($booking->isPaid()) {
            return redirect()->route('guest.bookings.show', $booking)
                ->with('info', 'This booking is already fully paid.');
        }

        $remainingBalance = $booking->remaining_balance;
        
        return view('payments.create', compact('booking', 'remainingBalance'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Booking $booking)
    {
        // Ensure user can access this booking
        if (Auth::user()->role !== 'admin' && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $booking->remaining_balance,
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
                'payment_date' => $request->payment_method === 'cash' ? now() : null,
                'notes' => $request->notes
            ]);

            // If this completes the payment, update booking status
            if ($booking->isPaid()) {
                $booking->update(['status' => 'confirmed']);
                
                // Mark invoice as paid if exists
                if ($booking->invoice) {
                    $booking->invoice->markAsPaid();
                }
            }

            DB::commit();

            return redirect()->route('payments.confirmation', $payment)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Payment processing failed. Please try again.']);
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

        return view('payments.confirmation', compact('payment'));
    }

    /**
     * Display payment history for user
     */
    public function history()
    {
        $payments = auth()->user()->payments()
            ->with([
                'booking.room',
                'booking.invoice', // Make sure invoice is loaded
                'serviceRequest'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }

    /**
     * Display single payment details
     */
    public function show(Payment $payment)
    {
        // Ensure user can access this payment
        if (Auth::user()->role !== 'admin' && $payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Process refund (admin only)
     */
    public function refund(Request $request, Payment $payment)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can process refunds.');
        }

        $request->validate([
            'refund_reason' => 'required|string|max:500'
        ]);

        if ($payment->status !== 'completed') {
            return back()->withErrors(['error' => 'Only completed payments can be refunded.']);
        }

        DB::beginTransaction();

        try {
            // Update payment status
            $payment->update([
                'status' => 'refunded',
                'notes' => ($payment->notes ?? '') . "\n\nRefund Reason: " . $request->refund_reason
            ]);

            // Update booking status if needed
            $booking = $payment->booking;
            if (!$booking->isPaid() && $booking->status === 'confirmed') {
                $booking->update(['status' => 'pending']);
            }

            DB::commit();

            return back()->with('success', 'Payment refunded successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Refund processing failed. Please try again.']);
        }
    }

    /**
     * Admin payment dashboard
     */
    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can access this page.');
        }

        $payments = Payment::with(['booking', 'user', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_payments' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->sum('amount'),
            'total_transactions' => Payment::count(),
            'recent_payments' => Payment::completed()->whereDate('created_at', today())->sum('amount')
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Update payment status (admin only)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can update payment status.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $payment->update([
            'status' => $request->status,
            'transaction_id' => $request->transaction_id,
            'payment_date' => $request->status === 'completed' ? now() : $payment->payment_date,
            'notes' => $request->notes
        ]);

        // Update booking status if payment is completed
        if ($request->status === 'completed' && $payment->booking->isPaid()) {
            $payment->booking->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'Payment status updated successfully.');
    }
}
