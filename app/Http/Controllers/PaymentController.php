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
     * Show refund form (admin/manager can view, admin can process)
     */
    public function showRefundForm(Payment $payment)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Only administrators and managers can access refund forms.');
        }

        if (!$payment->canBeRefunded()) {
            return back()->withErrors(['error' => 'This payment cannot be refunded.']);
        }

        return view('admin.payments.refund', compact('payment'));
    }

    /**
     * Process refund (admin only)
     */
    public function processRefund(Request $request, Payment $payment)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can process refunds.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->refundable_amount,
            'refund_reason' => 'required|string|max:500',
            'refund_type' => 'required|in:full,partial'
        ]);

        if (!$payment->canBeRefunded()) {
            return back()->withErrors(['error' => 'This payment cannot be refunded.']);
        }

        DB::beginTransaction();

        try {
            $isFullRefund = $request->refund_type === 'full' || $request->refund_amount == $payment->amount;
            
            // Update payment with refund details
            $payment->update([
                'status' => $isFullRefund ? 'refunded' : 'partially_refunded',
                'refund_amount' => ($payment->refund_amount ?? 0) + $request->refund_amount,
                'refund_reason' => $request->refund_reason,
                'refunded_at' => now(),
                'refunded_by' => Auth::id(),
                'notes' => ($payment->notes ?? '') . "\n\nRefund processed: ₱" . number_format($request->refund_amount, 2) . 
                          " on " . now()->format('Y-m-d H:i:s') . "\nReason: " . $request->refund_reason
            ]);

            // Update booking status if needed
            if ($payment->booking) {
                $booking = $payment->booking;
                if ($isFullRefund || !$booking->isPaid()) {
                    $booking->update(['status' => 'cancelled']);
                }
            }

            // Update service request status if needed
            if ($payment->serviceRequest && $isFullRefund) {
                $payment->serviceRequest->update(['status' => 'cancelled']);
            }

            DB::commit();

            $message = $isFullRefund ? 'Full refund processed successfully.' : 'Partial refund processed successfully.';
            return redirect()->route('admin.payments.show', $payment)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Refund processing failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin payment dashboard
     */
    public function adminIndex(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Only administrators and managers can access this page.');
        }

        $query = Payment::with(['booking', 'user', 'booking.room', 'serviceRequest', 'refundedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by payment reference or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_payments' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->sum('amount'),
            'total_refunds' => Payment::where('refund_amount', '>', 0)->sum('refund_amount'),
            'total_transactions' => Payment::count(),
            'recent_payments' => Payment::completed()->whereDate('created_at', today())->sum('amount'),
            'refundable_payments' => Payment::refundable()->count()
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details (admin/manager)
     */
    public function adminShow(Payment $payment)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Only administrators and managers can access this page.');
        }

        $payment->load(['booking', 'user', 'booking.room', 'serviceRequest', 'refundedBy']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update payment status (admin/manager)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Only administrators and managers can update payment status.');
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
