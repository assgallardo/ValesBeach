<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    /**
     * Cancel a refund and restore the original payment amount
     */
    public function cancelRefund(Request $request, Payment $payment)
    {
        // Only allow if payment has a refund
        if ($payment->refund_amount > 0) {
            $payment->refund_amount = 0;
            $payment->refund_reason = null;
            $payment->refunded_at = null;
            $payment->refunded_by = null;
            // Optionally, reset status if it was set to refunded
            if ($payment->status === 'refunded') {
                $payment->status = 'completed';
            }
            $payment->save();
            return back()->with('success', 'Refund cancelled and original payment restored.');
        }
        return back()->with('error', 'No refund to cancel for this payment.');
    }
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

        // Calculate actual remaining balance (total price - amount already paid)
        $alreadyPaid = $booking->payments()->where('status', 'completed')->sum('amount') ?? 0;
        $remainingBalance = $booking->total_price - $alreadyPaid;
        
        // Minimum payment is 50% of total price OR the remaining balance (whichever is smaller)
        $minimumPayment = min(
            max(1, floor($booking->total_price * 0.5)),
            $remainingBalance
        );
        
        return view('payments.create', compact('booking', 'remainingBalance', 'minimumPayment'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Booking $booking)
    {
        // Calculate actual remaining balance (total price - amount already paid)
        $alreadyPaid = $booking->payments()->where('status', 'completed')->sum('amount') ?? 0;
        $remainingBalance = $booking->total_price - $alreadyPaid;
        
        // Minimum payment is 50% of total price OR the remaining balance (whichever is smaller)
        $minimumPayment = min(
            max(1, floor($booking->total_price * 0.5)),
            $remainingBalance
        );
        
        $request->validate([
            'payment_amount' => "required|numeric|min:{$minimumPayment}|max:{$remainingBalance}",
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $paymentAmount = $request->payment_amount;
            
            // Auto-complete cash payments, others remain pending for verification
            $paymentStatus = ($request->payment_method === 'cash') ? 'completed' : 'pending';
            
            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'booking_id' => $booking->id,
                'payment_reference' => $this->generatePaymentReference(),
                'amount' => $paymentAmount,
                'payment_method' => $request->payment_method,
                'status' => $paymentStatus, // Auto-complete cash, others pending
                'payment_date' => now(),
                'notes' => $request->notes,
                'transaction_id' => $request->transaction_id ?? null,
            ]);

            // Update booking payment tracking (calculates amount_paid, remaining_balance, payment_status)
            $booking->updatePaymentTracking();
            
            // Refresh to get the updated values from updatePaymentTracking
            $booking->refresh();
            
            // Debug logging
            \Log::info('Payment Tracking Updated', [
                'booking_id' => $booking->id,
                'total_price' => $booking->total_price,
                'amount_paid' => $booking->amount_paid,
                'remaining_balance' => $booking->remaining_balance,
                'payment_status' => $booking->payment_status,
                'payment_amount' => $paymentAmount
            ]);
            
            // NOTE: Booking status remains unchanged after payment
            // Only admin/manager/staff can change booking status through reservations management
            
            // Reload the payment with fresh booking data
            $payment->load('booking');

            DB::commit();
            return redirect()->route('payments.confirmation', $payment);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment processing failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Payment processing failed: ' . $e->getMessage());
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

        // Load booking relationship with fresh data from database
        $payment->load(['booking.room']);
        
        // Refresh the booking to ensure we have the latest payment tracking data
        if ($payment->booking) {
            $payment->booking->refresh();
        }

        return view('payments.confirmation', compact('payment'));
    }

    /**
     * Show edit payment form
     */
    public function edit(Payment $payment)
    {
        // Ensure user can access this payment
        if (Auth::user()->role !== 'admin' && $payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        // Only allow editing of pending or recently completed payments (within 5 minutes)
        if ($payment->status === 'refunded' || $payment->status === 'failed') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'This payment cannot be edited.');
        }

        // Check if payment was made more than 5 minutes ago
        if ($payment->created_at->diffInMinutes(now()) > 5) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Payment can only be edited within 5 minutes of creation.');
        }

        $booking = $payment->booking;
        
        // Calculate remaining balance excluding this payment
        $otherPayments = $booking->payments()
            ->where('id', '!=', $payment->id)
            ->where('status', 'completed')
            ->sum('amount');
        $remainingBalance = $booking->total_price - $otherPayments;
        
        // Minimum payment is 50% of total OR the remaining balance (whichever is smaller)
        $minimumPayment = min(
            max(1, floor($booking->total_price * 0.5)),
            $remainingBalance
        );

        return view('payments.edit', compact('payment', 'booking', 'remainingBalance', 'minimumPayment'));
    }

    /**
     * Update payment details
     */
    public function update(Request $request, Payment $payment)
    {
        // Ensure user can access this payment
        if (Auth::user()->role !== 'admin' && $payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        // Only allow editing of pending or recently completed payments
        if ($payment->status === 'refunded' || $payment->status === 'failed') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'This payment cannot be edited.');
        }

        // Check if payment was made more than 5 minutes ago
        if ($payment->created_at->diffInMinutes(now()) > 5) {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Payment can only be edited within 5 minutes of creation.');
        }

        $booking = $payment->booking;
        
        // Calculate remaining balance excluding this payment
        $otherPayments = $booking->payments()
            ->where('id', '!=', $payment->id)
            ->where('status', 'completed')
            ->sum('amount');
        $remainingBalance = $booking->total_price - $otherPayments;
        
        $minimumPayment = min(
            max(1, floor($booking->total_price * 0.5)),
            $remainingBalance
        );

        $request->validate([
            'payment_amount' => "required|numeric|min:{$minimumPayment}|max:{$remainingBalance}",
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $payment->amount;
            $newAmount = $request->payment_amount;

            // Update payment details
            $payment->update([
                'amount' => $newAmount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Update booking payment tracking
            $booking->updatePaymentTracking();
            $booking->refresh();

            // NOTE: Booking status remains unchanged after payment update
            // Only admin/manager/staff can change booking status through reservations management
            $payment->load('booking');

            \Log::info('Payment updated', [
                'payment_id' => $payment->id,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'payment_method' => $request->payment_method
            ]);

            DB::commit();
            
            return redirect()->route('payments.confirmation', $payment)
                ->with('success', 'Payment updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment update failed: ' . $e->getMessage());
            return back()->with('error', 'Payment update failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display payment history for user - grouped by booking
     */
    public function history()
    {
        // Get all bookings with their payments for this user
        // Show ALL bookings so guests can see what needs to be paid
        $bookings = \App\Models\Booking::where('user_id', auth()->id())
            ->with(['room', 'invoice', 'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->where('status', '!=', 'cancelled') // Exclude cancelled bookings
            ->orderBy('created_at', 'desc')
            ->get();

        // Get service payments (not related to bookings)
        $servicePayments = auth()->user()->payments()
            ->whereNotNull('service_request_id')
            ->with('serviceRequest.service')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get food order payments
        $foodOrderPayments = auth()->user()->payments()
            ->whereNotNull('food_order_id')
            ->with('foodOrder.orderItems.menuItem')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get extra charge payments (payments without booking_id, service_request_id, or food_order_id)
        // These are typically extra charges added via invoice generation
        $extraChargePayments = auth()->user()->payments()
            ->whereNull('booking_id')
            ->whereNull('service_request_id')
            ->whereNull('food_order_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the general payment method (most common one used by the user)
        $allPayments = auth()->user()->payments;
        $paymentMethodCounts = $allPayments->groupBy('payment_method')->map->count();
        $generalPaymentMethod = $paymentMethodCounts->isNotEmpty() 
            ? $paymentMethodCounts->sortDesc()->keys()->first() 
            : null;

        return view('payments.history', compact('bookings', 'servicePayments', 'foodOrderPayments', 'extraChargePayments', 'generalPaymentMethod'));
    }

    /**
     * Display single payment details
     */
    public function show(Payment $payment)
    {
        // Load all necessary relationships
        $payment->load([
            'user',
            'booking.room',
            'serviceRequest.service',
            'foodOrder.orderItems.menuItem',
            'refundedBy'
        ]);
        
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $routePrefix = request()->route()->getPrefix();
        
        if ($routePrefix === 'manager') {
            return view('manager.payments.show', compact('payment'));
        } else {
            return view('admin.payments.show', compact('payment'));
        }
    }

    /**
     * Export payments data to CSV (Customer Grouped Format)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        // Get the same filtered query as the index method
        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $query = \App\Models\User::whereHas('payments')
            ->with(['payments' => function($query) use ($status, $paymentMethod, $dateFrom, $dateTo) {
                if ($status) {
                    $query->where('status', $status);
                }
                if ($paymentMethod) {
                    $query->where('payment_method', $paymentMethod);
                }
                if ($dateFrom) {
                    $query->whereDate('created_at', '>=', $dateFrom);
                }
                if ($dateTo) {
                    $query->whereDate('created_at', '<=', $dateTo);
                }
            }]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('payments', function($paymentQuery) use ($search) {
                      $paymentQuery->where('payment_reference', 'like', "%{$search}%")
                                   ->orWhere('transaction_id', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->get();

        // Create CSV content
        $csvData = [];
        $csvData[] = [
            'Guest Name',
            'Email',
            'Role',
            'Total Bookings',
            'Total Services',
            'Total Food Orders',
            'Total Amount',
            'Total Payments',
            'Completed',
            'Confirmed',
            'Pending',
            'Overdue',
            'Refunded',
            'Latest Payment Date',
            'Member Since'
        ];

        foreach ($customers as $customer) {
            $bookingCount = $customer->payments->where('booking_id', '!=', null)->count();
            $serviceCount = $customer->payments->where('service_request_id', '!=', null)->count();
            $foodCount = $customer->payments->where('food_order_id', '!=', null)->count();
            $totalAmount = $customer->payments->sum('amount');
            $totalPayments = $customer->payments->count();
            
            $completedCount = $customer->payments->where('status', 'completed')->count();
            $confirmedCount = $customer->payments->where('status', 'confirmed')->count();
            $pendingCount = $customer->payments->where('status', 'pending')->count();
            $overdueCount = $customer->payments->where('status', 'overdue')->count();
            $refundedCount = $customer->payments->where('status', 'refunded')->count();
            
            $latestPayment = $customer->payments->sortByDesc('created_at')->first();

            $csvData[] = [
                $customer->name,
                $customer->email,
                ucfirst($customer->role),
                $bookingCount,
                $serviceCount,
                $foodCount,
                number_format($totalAmount, 2),
                $totalPayments,
                $completedCount,
                $confirmedCount,
                $pendingCount,
                $overdueCount,
                $refundedCount,
                $latestPayment ? $latestPayment->created_at->format('Y-m-d H:i:s') : 'N/A',
                $customer->created_at->format('Y-m-d')
            ];
        }

        // Generate CSV file
        $filename = 'customer_payments_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Show refund form
     */
    public function showRefundForm(Payment $payment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        if (!$payment->canBeRefunded()) {
            return redirect()->back()->with('error', 'This payment cannot be refunded.');
        }

        return view('admin.payments.refund', compact('payment'));
    }

    /**
     * Process refund (admin only)
     */
    public function processRefund(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->getRemainingRefundableAmount(),
            'refund_reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $refundAmount = $request->refund_amount;
            $refundReason = $request->refund_reason ?? null;
            $totalRefunded = ($payment->refund_amount ?? 0) + $refundAmount;

            // Update payment with refund information
            $payment->update([
                'refund_amount' => $totalRefunded,
                'refund_reason' => $refundReason,
                'refunded_at' => now(),
                'refunded_by' => auth()->id(),
                'status' => $totalRefunded >= $payment->calculated_amount ? 'refunded' : $payment->status,
            ]);

            // Handle service-specific refund logic
            if ($payment->serviceRequest) {
                // Update service request status if fully refunded
                if ($totalRefunded >= $payment->calculated_amount) {
                    $payment->serviceRequest->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => 'Fully refunded' . ($refundReason ? (': ' . $refundReason) : '')
                    ]);
                }
            }

            // Handle booking-specific refund logic
            if ($payment->booking) {
                // Update booking status if fully refunded
                if ($totalRefunded >= $payment->calculated_amount) {
                    $payment->booking->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => 'Fully refunded' . ($refundReason ? (': ' . $refundReason) : '')
                    ]);
                }
            }

            DB::commit();
            
            // Determine redirect route based on user role
            $routePrefix = auth()->user()->role === 'admin' ? 'admin' : 'manager';
            
            return redirect()->route($routePrefix . '.payments.index')
                            ->with('success', 'Refund processed successfully. Amount: ₱' . number_format($refundAmount, 2));
                            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to process refund. Please try again.');
        }
    }

    /**
     * Admin payment dashboard
     */
    public function adminIndex(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can access this page.');
        }

        $query = Payment::with(['booking', 'user', 'booking.room', 'booking.invoice', 'serviceRequest', 'foodOrder.orderItems.menuItem', 'refundedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type === 'booking') {
                $query->whereNotNull('booking_id');
            } elseif ($request->type === 'service') {
                $query->whereNotNull('service_request_id');
            } elseif ($request->type === 'food') {
                $query->whereNotNull('food_order_id');
            }
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

        // Get all bookings grouped by user (for complete guest data)
        $userIds = $payments->pluck('user_id')->unique();
        
        if ($userIds->isNotEmpty()) {
            $allBookings = Booking::with(['room', 'payments', 'invoice'])
                ->whereIn('user_id', $userIds)
                ->where('status', '!=', 'cancelled')
                ->get()
                ->groupBy('user_id');
        } else {
            $allBookings = collect();
        }

        $stats = [
            'total_payments' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->sum('amount'),
            'total_refunds' => Payment::where('refund_amount', '>', 0)->sum('refund_amount'),
            'total_transactions' => Payment::count(),
            'recent_payments' => Payment::completed()->whereDate('created_at', today())->sum('amount'),
            'refundable_payments' => Payment::refundable()->count(),
            'booking_payments' => Payment::whereNotNull('booking_id')->where('status', 'completed')->sum('amount'),
            'service_payments' => Payment::whereNotNull('service_request_id')->where('status', 'completed')->sum('amount'),
            'food_order_payments' => Payment::whereNotNull('food_order_id')->where('status', 'completed')->sum('amount'),
            'booking_count' => Payment::whereNotNull('booking_id')->count(),
            'service_count' => Payment::whereNotNull('service_request_id')->count(),
            'food_order_count' => Payment::whereNotNull('food_order_id')->count(),
            'total_count' => Payment::count(),
            'failed_payments' => Payment::where('status', 'failed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats', 'allBookings'));
    }

    /**
     * Show all payments for a specific guest
     */
    public function guestPayments(User $user)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can access this page.');
        }

        // Get all bookings for this guest (including those without payments)
        $bookings = Booking::where('user_id', $user->id)
            ->with(['room', 'payments'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all payments for this guest
        $payments = Payment::where('user_id', $user->id)
            ->with(['booking.room', 'serviceRequest.service', 'foodOrder.orderItems.menuItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Separate payments by type
        $bookingPayments = $payments->filter(fn($p) => $p->booking_id)->values();
        $servicePayments = $payments->filter(fn($p) => $p->service_request_id)->values();
        $foodPayments = $payments->filter(fn($p) => $p->food_order_id)->values();

        // Calculate totals
        $totalAmount = $payments->sum('amount');
        $totalPaid = $payments->where('status', 'completed')->sum('amount');
        $totalPending = $payments->whereIn('status', ['pending', 'processing'])->sum('amount');
        
        // Add booking totals
        $bookingTotalAmount = $bookings->sum('total_price');
        $bookingTotalPaid = $bookings->sum('amount_paid');
        $bookingTotalPending = $bookings->sum('remaining_balance');

        return view('admin.payments.guest', compact(
            'user',
            'bookings',
            'payments',
            'bookingPayments',
            'servicePayments',
            'foodPayments',
            'totalAmount',
            'totalPaid',
            'totalPending',
            'bookingTotalAmount',
            'bookingTotalPaid',
            'bookingTotalPending'
        ));
    }

    /**
     * Show payment details (admin/manager)
     */
    public function adminShow(Payment $payment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load(['booking', 'user', 'booking.room', 'serviceRequest.service', 'foodOrder.orderItems.menuItem', 'refundedBy']);

        $routePrefix = request()->route()->getPrefix();
    
        if ($routePrefix === 'manager') {
            return view('manager.payments.show', compact('payment'));
        } else {
            return view('admin.payments.show', compact('payment'));
        }
    }

    /**
     * Update payment status (admin/manager)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,completed,overdue,processing,failed,refunded,cancelled',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $payment->update([
            'status' => $request->status,
            'transaction_id' => $request->transaction_id,
            'payment_date' => $request->status === 'completed' ? now() : $payment->payment_date,
            'notes' => $request->notes
        ]);

        // Update booking payment tracking if payment is completed
        if ($request->status === 'completed' && $payment->booking) {
            // Update booking payment tracking (amount_paid, remaining_balance, payment_status)
            $payment->booking->updatePaymentTracking();
            
            // NOTE: Booking status is NOT automatically changed
            // Admin/Manager/Staff must manually update booking status through Reservations Management
        }
        
        // Update service request status if payment is completed
        if ($request->status === 'completed' && $payment->serviceRequest) {
            if (in_array($payment->serviceRequest->status, ['pending', 'confirmed'])) {
                $payment->serviceRequest->update(['status' => 'in_progress']);
            }
        }

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment status updated successfully.',
                    'status' => $payment->status
                ]);
            }

        return back()->with('success', 'Payment status updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Payment status update error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'status' => $request->status,
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'An error occurred while updating payment status.');
        }
    }

    public function index(Request $request)
    {
        // Check if user has admin, manager, or staff access
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can access this page.');
        }

        // Get the current route prefix to determine which view to return
        $routePrefix = request()->route()->getPrefix();
        
        // Get filter parameters
        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        $paymentType = $request->get('payment_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        // Build query for customer payments (grouped by user)
        $query = \App\Models\User::whereHas('payments')
            ->with(['payments' => function($q) use ($status, $paymentMethod, $paymentType, $dateFrom, $dateTo) {
                // Apply filters to payments
        if ($status) {
                    $q->where('status', $status);
        }
        if ($paymentMethod) {
                    $q->where('payment_method', $paymentMethod);
        }
        if ($paymentType) {
            if ($paymentType === 'booking') {
                        $q->whereNotNull('booking_id');
            } elseif ($paymentType === 'service') {
                        $q->whereNotNull('service_request_id');
            } elseif ($paymentType === 'food_order') {
                        $q->whereNotNull('food_order_id');
            }
        }
        if ($dateFrom) {
                    $q->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
                    $q->whereDate('created_at', '<=', $dateTo);
        }

                $q->with(['booking.room', 'serviceRequest.service', 'foodOrder'])
                  ->orderBy('created_at', 'desc');
            }]);

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate comprehensive statistics
        $stats = [
            'total_payments' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->sum('amount'),
            'total_refunds' => Payment::whereNotNull('refund_amount')->sum('refund_amount'),
            'total_transactions' => Payment::count(),
            'booking_payments' => Payment::whereNotNull('booking_id')->where('status', 'completed')->sum('amount'),
            'service_payments' => Payment::whereNotNull('service_request_id')->where('status', 'completed')->sum('amount'),
            'food_order_payments' => Payment::whereNotNull('food_order_id')->where('status', 'completed')->sum('amount'),
            'total_count' => Payment::count(),
            'completed_count' => Payment::where('status', 'completed')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
            'refunded_count' => Payment::where('status', 'refunded')->count(),
            'refundable_payments' => Payment::where('status', 'completed')
                ->where(function($query) {
                    $query->whereNull('refund_amount')
                          ->orWhereColumn('refund_amount', '<', 'amount');
                })
                ->count(),
            'booking_count' => Payment::whereNotNull('booking_id')->count(),
            'service_count' => Payment::whereNotNull('service_request_id')->count(),
            'food_order_count' => Payment::whereNotNull('food_order_id')->count(),
        ];

        // Return appropriate view based on route prefix
        if ($routePrefix === 'manager') {
            return view('manager.payments.index', compact('customers', 'stats'));
        } else {
            return view('admin.payments.index', compact('customers', 'stats'));
        }
    }
    
    /**
     * Show all payments for a specific customer
     */
    public function showCustomerPayments($userId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer = \App\Models\User::with([
            'payments' => function($q) {
                $q->with(['booking.room', 'serviceRequest.service', 'foodOrder.orderItems.menuItem'])
                  ->orderBy('created_at', 'desc');
            },
            'bookings' => function($q) {
                $q->with(['room', 'payments']);
            },
            'serviceRequests' => function($q) {
                $q->with(['service', 'payment']);
            },
            'foodOrders' => function($q) {
                $q->with(['orderItems.menuItem', 'payment']);
            }
        ])->findOrFail($userId);

        $routePrefix = request()->route()->getPrefix();
        
        if ($routePrefix === 'manager') {
            return view('manager.payments.customer', compact('customer'));
        } else {
            return view('admin.payments.customer', compact('customer'));
        }
    }

    /**
     * Show invoice preview/edit form for customer
     */
    public function generateCustomerInvoice($userId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer = \App\Models\User::with([
            'bookings.room',
            'bookings.payments',
            'serviceRequests.service',
            'serviceRequests.payment',
            'foodOrders.orderItems.menuItem',
            'foodOrders.payment'
        ])->findOrFail($userId);

        // Build invoice data
        $items = [];
        $totalAmount = 0;
        $totalPaid = 0;

        // Add bookings
        foreach ($customer->bookings as $booking) {
            if ($booking->total_price > 0) {
                // Sum net amounts for all payments (amount - refund_amount)
                $paid = $booking->payments->sum(function($p) { return $p->amount - ($p->refund_amount ?? 0); });
                $balance = $booking->total_price - $paid;
                $items[] = [
                    'type' => 'booking',
                    'description' => $booking->room->name ?? 'Room Booking',
                    'reference' => $booking->booking_reference ?? 'N/A',
                    'details' => \Carbon\Carbon::parse($booking->check_in_date)->format('M d') . ' - ' . 
                                \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y'),
                    'amount' => $booking->total_price,
                    'paid' => $paid,
                    'balance' => $balance
                ];
                $totalAmount += $booking->total_price;
                $totalPaid += $paid;
            }
        }

        // Add services
        foreach ($customer->serviceRequests as $serviceRequest) {
            $servicePrice = $serviceRequest->service->price ?? 0;
            $paid = $serviceRequest->payment ? ($serviceRequest->payment->amount - ($serviceRequest->payment->refund_amount ?? 0)) : 0;
            if ($paid > 0 && $servicePrice == 0) {
                $servicePrice = $paid;
            }
            if ($servicePrice > 0) {
                $balance = $servicePrice - $paid;
                $items[] = [
                    'type' => 'service',
                    'description' => $serviceRequest->service->name ?? 'Service Request',
                    'reference' => 'SR-' . $serviceRequest->id,
                    'details' => $serviceRequest->special_requests ?? 'Service request',
                    'amount' => $servicePrice,
                    'paid' => $paid,
                    'balance' => $balance
                ];
                $totalAmount += $servicePrice;
                $totalPaid += $paid;
            }
        }

        // Add food orders
        foreach ($customer->foodOrders as $foodOrder) {
            if ($foodOrder->total_amount > 0) {
                $paid = $foodOrder->payment ? ($foodOrder->payment->amount - ($foodOrder->payment->refund_amount ?? 0)) : 0;
                $balance = $foodOrder->total_amount - $paid;
                $itemsList = $foodOrder->orderItems->map(function($item) {
                    return $item->menuItem->name ?? 'Item';
                })->take(3)->implode(', ');
                if ($foodOrder->orderItems->count() > 3) {
                    $itemsList .= '...';
                }
                $items[] = [
                    'type' => 'food',
                    'description' => 'Food Order #' . $foodOrder->order_number,
                    'reference' => $foodOrder->order_number,
                    'details' => $itemsList,
                    'amount' => $foodOrder->total_amount,
                    'paid' => $paid,
                    'balance' => $balance
                ];
                $totalAmount += $foodOrder->total_amount;
                $totalPaid += $paid;
            }
        }

        // Add extra charges (payments with payment_reference starting with 'EXT-')
        $extraChargePayments = \App\Models\Payment::where('user_id', $customer->id)
            ->whereNull('booking_id')
            ->whereNull('service_request_id')
            ->whereNull('food_order_id')
            ->where('payment_reference', 'LIKE', 'EXT-%')
            ->orderBy('created_at', 'desc')
            ->get();

        $loadedPaymentIds = [];
        foreach ($extraChargePayments as $extraPayment) {
            $paymentDetails = $extraPayment->payment_details ?? [];
            $description = $paymentDetails['description'] ?? 'Extra Charge';
            $reference = $paymentDetails['reference'] ?? ($extraPayment->payment_reference ?? 'N/A');
            $details = $paymentDetails['details'] ?? '';
            $invoiceNumber = $paymentDetails['invoice_number'] ?? '';
            $amount = $extraPayment->amount ?? 0;
            $paid = ($extraPayment->status === 'completed') ? ($amount - ($extraPayment->refund_amount ?? 0)) : 0;
            $balance = $amount - $paid;
            $items[] = [
                'type' => 'extra',
                'description' => $description,
                'reference' => $reference,
                'details' => $details,
                'amount' => $amount,
                'paid' => $paid,
                'balance' => $balance,
                'payment_id' => $extraPayment->id, // Store payment ID for deletion
                'payment_reference' => $extraPayment->payment_reference,
                'invoice_number' => $invoiceNumber
            ];
            $loadedPaymentIds[] = $extraPayment->id;
            $totalAmount += $amount;
            $totalPaid += $paid;
        }
        
        // Store loaded payment IDs in session so we know which ones to delete if removed
        session(['generated_invoice_payment_ids' => $loadedPaymentIds]);

        $totalBalance = $totalAmount - $totalPaid;

        return view('invoices.customer-invoice-edit', compact('customer', 'items', 'totalAmount', 'totalPaid', 'totalBalance'));
    }

    /**
     * Save and generate final customer invoice
     */
    public function saveCustomerInvoice(Request $request, $userId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer = \App\Models\User::findOrFail($userId);

        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.reference' => 'nullable|string',
            'items.*.details' => 'nullable|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.paid' => 'required|numeric|min:0',
            'items.*.payment_id' => 'nullable|integer|exists:payments,id',
            'items.*.payment_reference' => 'nullable|string',
            'notes' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date'
        ]);

        // If no items provided, return with error
        if (empty($validated['items'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please add at least one item to the invoice.');
        }

        // Calculate totals - first pass
        $totalAmount = 0;
        $totalPaid = 0;

        foreach ($validated['items'] as $item) {
            $totalAmount += floatval($item['amount']);
            $totalPaid += floatval($item['paid']);
        }

        $totalBalance = $totalAmount - $totalPaid;

        // Create invoice first to get invoice number
        $invoice = \App\Models\Invoice::create([
            'user_id' => $customer->id,
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'invoice_date' => now(),
            'due_date' => $validated['due_date'] ?? now()->addDays(7),
            'subtotal' => $totalAmount,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'amount_paid' => $totalPaid,
            'balance_due' => $totalBalance,
            'status' => $totalBalance <= 0 ? 'paid' : 'sent',
            'notes' => $validated['notes'] ?? '',
            'items' => [], // Will be updated after creating payments
            'created_by' => auth()->id()
        ]);

        // Get all existing extra charge payment IDs that were loaded into the form
        // This includes payments that were shown in the invoice items table
        $existingExtraPaymentIds = [];
        $submittedItems = $request->input('items', []);
        if (!empty($submittedItems)) {
            foreach ($submittedItems as $submittedItem) {
                if (isset($submittedItem['payment_id']) && !empty($submittedItem['payment_id'])) {
                    $existingExtraPaymentIds[] = $submittedItem['payment_id'];
                }
            }
        }
        
        // Also get all current extra charge payments from database for comparison
        $allCurrentExtraPaymentIds = \App\Models\Payment::where('user_id', $customer->id)
            ->whereNull('booking_id')
            ->whereNull('service_request_id')
            ->whereNull('food_order_id')
            ->where('payment_reference', 'LIKE', 'EXT-%')
            ->pluck('id')
            ->toArray();
        
        $newExtraPaymentIds = [];
        $items = [];
        
        // Process all items and create Payment records for extra charges
        foreach ($validated['items'] as $item) {
            $amount = floatval($item['amount']);
            $paid = floatval($item['paid']);
            $balance = $amount - $paid;
            
            \Log::info('Processing item in saveCustomerInvoice', [
                'type' => $item['type'],
                'description' => $item['description'],
                'amount' => $amount,
                'paid' => $paid,
                'has_payment_id' => !empty($item['payment_id'])
            ]);
            
            $itemData = [
                'type' => $item['type'],
                'description' => $item['description'],
                'reference' => $item['reference'] ?? '',
                'details' => $item['details'] ?? '',
                'amount' => $amount,
                'paid' => $paid,
                'balance' => $balance
            ];
            
            \Log::info('Before checking extra condition', [
                'item_type' => $item['type'],
                'item_type_is_extra' => $item['type'] === 'extra',
                'amount' => $amount,
                'amount_greater_than_zero' => $amount > 0,
                'will_create_payment' => ($item['type'] === 'extra' && $amount > 0)
            ]);
            
            // Create Payment record for extra charges
            if ($item['type'] === 'extra' && $amount > 0) {
                \Log::info('Creating/updating extra charge payment', [
                    'has_existing_payment_id' => !empty($item['payment_id']),
                    'payment_id' => $item['payment_id'] ?? null
                ]);
                
                // If payment_id exists, update it; otherwise create new
                if (!empty($item['payment_id'])) {
                    $payment = Payment::find($item['payment_id']);
                    if ($payment && $payment->user_id === $customer->id) {
                        $payment->update([
                            'amount' => $amount,
                            'status' => $paid >= $amount ? 'completed' : 'pending',
                            'payment_date' => $paid > 0 ? now() : null,
                            'notes' => sprintf(
                                'Invoice #%s - Extra Charge: %s%s%s',
                                $invoice->invoice_number,
                                $item['description'],
                                $item['reference'] ? ' (Ref: ' . $item['reference'] . ')' : '',
                                $item['details'] ? ' - ' . $item['details'] : ''
                            ),
                            'payment_details' => [
                                'invoice_id' => $invoice->id,
                                'invoice_number' => $invoice->invoice_number,
                                'extra_charge' => true,
                                'description' => $item['description'],
                                'reference' => $item['reference'] ?? '',
                                'details' => $item['details'] ?? ''
                            ]
                        ]);
                        $newExtraPaymentIds[] = $payment->id;
                        $itemData['payment_id'] = $payment->id;
                        $itemData['payment_reference'] = $payment->payment_reference;
                    }
                } else {
                    // Create payment record for extra charge
                    \Log::info('Creating NEW extra charge payment record');
                    
                    $payment = Payment::create([
                        'user_id' => $customer->id,
                        'booking_id' => null,
                        'service_request_id' => null,
                        'food_order_id' => null,
                        'payment_reference' => 'EXT-' . strtoupper(uniqid()),
                        'amount' => $amount,
                        'payment_method' => 'cash', // Default, can be updated later
                        'status' => $paid >= $amount ? 'completed' : 'pending',
                        'payment_date' => $paid > 0 ? now() : null,
                        'notes' => sprintf(
                            'Invoice #%s - Extra Charge: %s%s%s',
                            $invoice->invoice_number,
                            $item['description'],
                            $item['reference'] ? ' (Ref: ' . $item['reference'] . ')' : '',
                            $item['details'] ? ' - ' . $item['details'] : ''
                        ),
                        'payment_details' => [
                            'invoice_id' => $invoice->id,
                            'invoice_number' => $invoice->invoice_number,
                            'extra_charge' => true,
                            'description' => $item['description'],
                            'reference' => $item['reference'] ?? '',
                            'details' => $item['details'] ?? ''
                        ]
                    ]);
                    
                    \Log::info('Created extra charge payment', [
                        'payment_id' => $payment->id,
                        'payment_reference' => $payment->payment_reference,
                        'amount' => $payment->amount
                    ]);
                    
                    $newExtraPaymentIds[] = $payment->id;
                    $itemData['payment_id'] = $payment->id;
                    $itemData['payment_reference'] = $payment->payment_reference;
                }
            }
            
            $items[] = $itemData;
        }
        
        // Update invoice with items that now include payment_id for extra charges
        $invoice->update(['items' => $items]);
        
        // Delete extra charge payments that were removed from invoice
        // Get payments that were loaded in the generateCustomerInvoice page but are not in submitted items
        $loadedExtraPaymentIds = [];
        if (session()->has('generated_invoice_payment_ids')) {
            $loadedExtraPaymentIds = session('generated_invoice_payment_ids');
        }
        
        // Delete payments that were loaded but not submitted (removed by user from the form)
        // First check: payments that were loaded but are missing from submission
        $paymentIdsToDelete = [];
        
        if (!empty($loadedExtraPaymentIds)) {
            // Only delete payments that were explicitly loaded and shown in the form
            $paymentIdsToDelete = array_diff($loadedExtraPaymentIds, $newExtraPaymentIds);
        }
        // No fallback deletion for new invoices - only delete if we have session data
        
        if (!empty($paymentIdsToDelete)) {
            \Log::info('Deleting extra charge payments from saveCustomerInvoice', [
                'payment_ids' => $paymentIdsToDelete,
                'customer_id' => $customer->id,
                'loaded_ids' => $loadedExtraPaymentIds,
                'new_ids' => $newExtraPaymentIds
            ]);
            
            $deletedCount = Payment::whereIn('id', $paymentIdsToDelete)
                ->where('user_id', $customer->id)
                ->whereNull('booking_id')
                ->whereNull('service_request_id')
                ->whereNull('food_order_id')
                ->where('payment_reference', 'LIKE', 'EXT-%')
                ->delete();
            
            \Log::info('Deleted extra charge payments count from saveCustomerInvoice', [
                'deleted_count' => $deletedCount,
                'requested_to_delete' => count($paymentIdsToDelete)
            ]);
        }
        
        // Clear the session after invoice is saved
        session()->forget('generated_invoice_payment_ids');

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice generated successfully! Extra charges have been added to payment records.');
    }

    /**
     * Show edit form for existing customer invoice
     */
    public function editCustomerInvoice($invoiceId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $invoice = \App\Models\Invoice::with('user')->findOrFail($invoiceId);
        
        // Check if this is a customer combined invoice (has items array)
        if (!$invoice->items) {
            return redirect()->back()->with('error', 'This invoice type cannot be edited.');
        }

        $customer = $invoice->user;
        $items = $invoice->items ?? [];
        
        $loadedPaymentIds = [];
        
        // Enhance extra charge items with payment_id if they exist
        foreach ($items as &$item) {
            if ($item['type'] === 'extra') {
                // First check if payment_id is already stored in invoice items
                if (isset($item['payment_id']) && !empty($item['payment_id'])) {
                    $loadedPaymentIds[] = $item['payment_id'];
                    continue;
                }
                
                // Otherwise try to find the payment by reference or description
                if (isset($item['reference'])) {
                    $extraPayment = \App\Models\Payment::where('user_id', $customer->id)
                        ->whereNull('booking_id')
                        ->whereNull('service_request_id')
                        ->whereNull('food_order_id')
                        ->where(function($query) use ($item) {
                            $query->where('payment_reference', 'LIKE', 'EXT-%')
                                  ->where(function($q) use ($item) {
                                      $q->where('payment_reference', $item['reference'])
                                        ->orWhereJsonContains('payment_details->reference', $item['reference']);
                                  });
                        })
                        ->first();
                    
                    if ($extraPayment) {
                        $item['payment_id'] = $extraPayment->id;
                        $item['payment_reference'] = $extraPayment->payment_reference;
                        $loadedPaymentIds[] = $extraPayment->id;
                    }
                }
            }
        }
        unset($item); // Break reference
        
        // Store loaded payment IDs in session for deletion tracking when editing
        session(['generated_invoice_payment_ids' => $loadedPaymentIds]);
        
        $totalAmount = $invoice->total_amount;
        $totalPaid = $invoice->amount_paid;
        $totalBalance = $invoice->balance_due;

        return view('invoices.customer-invoice-edit', compact('customer', 'items', 'totalAmount', 'totalPaid', 'totalBalance', 'invoice'));
    }

    /**
     * Update existing customer invoice
     */
    public function updateCustomerInvoice(Request $request, $invoiceId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $invoice = \App\Models\Invoice::with('user')->findOrFail($invoiceId);
        $customer = $invoice->user;

        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.type' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.reference' => 'nullable|string',
            'items.*.details' => 'nullable|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date'
        ]);

        // If no items provided, return with error
        if (empty($validated['items'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please add at least one item to the invoice.');
        }

        // Calculate totals
        $totalAmount = 0;
        $totalPaid = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $amount = floatval($item['amount']);
            $paid = floatval($item['paid']);
            $balance = $amount - $paid;

            $itemData = [
                'type' => $item['type'],
                'description' => $item['description'],
                'reference' => $item['reference'] ?? '',
                'details' => $item['details'] ?? '',
                'amount' => $amount,
                'paid' => $paid,
                'balance' => $balance
            ];
            
            // Include payment_id for extra charges so we can track them
            if ($item['type'] === 'extra' && !empty($item['payment_id'])) {
                $itemData['payment_id'] = $item['payment_id'];
                if (!empty($item['payment_reference'])) {
                    $itemData['payment_reference'] = $item['payment_reference'];
                }
            }
            
            $items[] = $itemData;

            $totalAmount += $amount;
            $totalPaid += $paid;
        }

        $totalBalance = $totalAmount - $totalPaid;

        // Get existing invoice items to compare (not needed for deletion logic, but keep for reference)
        $existingItems = $invoice->items ?? [];
        $existingExtraPaymentIds = [];
        foreach ($existingItems as $existingItem) {
            if ($existingItem['type'] === 'extra' && isset($existingItem['payment_id'])) {
                $existingExtraPaymentIds[] = $existingItem['payment_id'];
            }
        }
        
        // Get all current extra charge payment IDs
        $allCurrentExtraPaymentIds = \App\Models\Payment::where('user_id', $customer->id)
            ->whereNull('booking_id')
            ->whereNull('service_request_id')
            ->whereNull('food_order_id')
            ->where('payment_reference', 'LIKE', 'EXT-%')
            ->pluck('id')
            ->toArray();
        
        // Calculate totals
        $totalAmount = 0;
        $totalPaid = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $amount = floatval($item['amount']);
            $paid = floatval($item['paid']);
            $balance = $amount - $paid;

            $itemData = [
                'type' => $item['type'],
                'description' => $item['description'],
                'reference' => $item['reference'] ?? '',
                'details' => $item['details'] ?? '',
                'amount' => $amount,
                'paid' => $paid,
                'balance' => $balance
            ];
            
            // Include payment_id for extra charges so we can track them
            if ($item['type'] === 'extra' && !empty($item['payment_id'])) {
                $itemData['payment_id'] = $item['payment_id'];
                if (!empty($item['payment_reference'])) {
                    $itemData['payment_reference'] = $item['payment_reference'];
                }
            }
            
            $items[] = $itemData;

            $totalAmount += $amount;
            $totalPaid += $paid;
        }

        $totalBalance = $totalAmount - $totalPaid;

        // Get existing invoice items to compare (not needed for deletion logic, but keep for reference)
        $existingItems = $invoice->items ?? [];
        $existingExtraPaymentIds = [];
        foreach ($existingItems as $existingItem) {
            if ($existingItem['type'] === 'extra' && isset($existingItem['payment_id'])) {
                $existingExtraPaymentIds[] = $existingItem['payment_id'];
            }
        }
        
        // Get all current extra charge payment IDs
        $allCurrentExtraPaymentIds = \App\Models\Payment::where('user_id', $customer->id)
            ->whereNull('booking_id')
            ->whereNull('service_request_id')
            ->whereNull('food_order_id')
            ->where('payment_reference', 'LIKE', 'EXT-%')
            ->pluck('id')
            ->toArray();
        
        $newExtraPaymentIds = [];

        // Update invoice
        $invoice->update([
            'due_date' => $validated['due_date'] ?? $invoice->due_date,
            'subtotal' => $totalAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $totalPaid,
            'balance_due' => $totalBalance,
            'status' => $totalBalance <= 0 ? 'paid' : 'sent',
            'notes' => $validated['notes'] ?? '',
            'items' => $items
        ]);

        // Create/Update Payment records for extra charges
        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'extra' && floatval($item['amount']) > 0) {
                $amount = floatval($item['amount']);
                
                // If payment_id exists, update it; otherwise create new
                if (!empty($item['payment_id'])) {
                    $payment = Payment::find($item['payment_id']);
                    if ($payment && $payment->user_id === $customer->id) {
                        $payment->update([
                            'amount' => $amount,
                            'status' => floatval($item['paid']) >= $amount ? 'completed' : 'pending',
                            'payment_date' => floatval($item['paid']) > 0 ? now() : null,
                            'notes' => sprintf(
                                'Invoice #%s - Extra Charge: %s%s%s',
                                $invoice->invoice_number,
                                $item['description'],
                                $item['reference'] ? ' (Ref: ' . $item['reference'] . ')' : '',
                                $item['details'] ? ' - ' . $item['details'] : ''
                            ),
                            'payment_details' => [
                                'invoice_id' => $invoice->id,
                                'invoice_number' => $invoice->invoice_number,
                                'extra_charge' => true,
                                'description' => $item['description'],
                                'reference' => $item['reference'] ?? '',
                                'details' => $item['details'] ?? ''
                            ]
                        ]);
                        $newExtraPaymentIds[] = $payment->id;
                        continue;
                    }
                }
                
                // Create new payment record for extra charge
                $paid = floatval($item['paid']);
                
                // Create payment record for new extra charge
                $payment = Payment::create([
                    'user_id' => $invoice->user_id,
                    'booking_id' => null,
                    'service_request_id' => null,
                    'food_order_id' => null,
                    'payment_reference' => 'EXT-' . strtoupper(uniqid()),
                    'amount' => $amount,
                    'payment_method' => 'cash', // Default, can be updated later
                    'status' => $paid >= $amount ? 'completed' : 'pending',
                    'payment_date' => $paid > 0 ? now() : null,
                    'notes' => sprintf(
                        'Invoice #%s - Extra Charge: %s%s%s',
                        $invoice->invoice_number,
                        $item['description'],
                        $item['reference'] ? ' (Ref: ' . $item['reference'] . ')' : '',
                        $item['details'] ? ' - ' . $item['details'] : ''
                    ),
                    'payment_details' => [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'extra_charge' => true,
                        'description' => $item['description'],
                        'reference' => $item['reference'] ?? '',
                        'details' => $item['details'] ?? ''
                    ]
                ]);
                $newExtraPaymentIds[] = $payment->id;
            }
        }
        
        // Delete extra charge payments that were removed from invoice
        // Get payments that were loaded in the edit page (from invoice items or session)
        $loadedExtraPaymentIds = [];
        if (session()->has('generated_invoice_payment_ids')) {
            $loadedExtraPaymentIds = session('generated_invoice_payment_ids');
        }
        
        // Also check existing invoice items for payment_ids
        foreach ($existingItems as $existingItem) {
            if ($existingItem['type'] === 'extra' && isset($existingItem['payment_id'])) {
                if (!in_array($existingItem['payment_id'], $loadedExtraPaymentIds)) {
                    $loadedExtraPaymentIds[] = $existingItem['payment_id'];
                }
            }
        }
        
        // Delete payments that were loaded but not submitted (removed by user from the form)
        $paymentIdsToDelete = [];
        if (!empty($loadedExtraPaymentIds)) {
            // Only delete payments that were explicitly loaded into the form
            $paymentIdsToDelete = array_diff($loadedExtraPaymentIds, $newExtraPaymentIds);
        } else {
            // Fallback: compare all current extra charge payments with submitted ones
            $paymentIdsToDelete = array_diff($allCurrentExtraPaymentIds, $newExtraPaymentIds);
        }
        
        if (!empty($paymentIdsToDelete)) {
            \Log::info('Deleting extra charge payments from updateCustomerInvoice', [
                'payment_ids' => $paymentIdsToDelete,
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'loaded_ids' => $loadedExtraPaymentIds,
                'new_ids' => $newExtraPaymentIds,
                'all_current_ids' => $allCurrentExtraPaymentIds
            ]);
            
            $deletedCount = Payment::whereIn('id', $paymentIdsToDelete)
                ->where('user_id', $customer->id)
                ->whereNull('booking_id')
                ->whereNull('service_request_id')
                ->whereNull('food_order_id')
                ->where('payment_reference', 'LIKE', 'EXT-%')
                ->delete();
            
            \Log::info('Deleted extra charge payments count from updateCustomerInvoice', [
                'deleted_count' => $deletedCount,
                'requested_to_delete' => count($paymentIdsToDelete)
            ]);
        }
        
        // Clear the session after invoice is updated
        session()->forget('generated_invoice_payment_ids');

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Update payment method for a payment (Guest accessible)
     */
    public function updatePaymentMethod(Request $request, Payment $payment)
    {
        // Ensure the payment belongs to the authenticated user
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this payment.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
        ]);

        $payment->update([
            'payment_method' => $validated['payment_method'],
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully.',
                'payment_method' => $payment->payment_method,
                'payment_method_display' => $payment->payment_method_display
            ]);
        }

        return back()->with('success', 'Payment method updated successfully.');
    }

    /**
     * Delete an extra charge payment
     */
    public function deleteExtraCharge(Payment $payment)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        // Verify this is an extra charge payment
        $isExtraCharge = $payment->booking_id === null 
            && $payment->service_request_id === null 
            && $payment->food_order_id === null
            && strpos($payment->payment_reference, 'EXT-') === 0;

        if (!$isExtraCharge) {
            return response()->json([
                'success' => false,
                'message' => 'This is not an extra charge payment and cannot be deleted.'
            ], 400);
        }

        // Verify payment belongs to a customer (safety check)
        if (!$payment->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment record.'
            ], 400);
        }

        try {
            $paymentId = $payment->id;
            $customerId = $payment->user_id;
            
            // Delete the payment
            $payment->delete();

            \Log::info('Extra charge payment deleted', [
                'payment_id' => $paymentId,
                'customer_id' => $customerId,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Extra charge deleted successfully.',
                'payment_id' => $paymentId
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Failed to delete extra charge payment: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete extra charge: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update payment method for all user's payments (Guest accessible)
     */
    public function bulkUpdatePaymentMethod(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            ]);

            // Get authenticated user
            $userId = auth()->id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            // Update all payments belonging to the authenticated user
            $updatedCount = Payment::where('user_id', $userId)
                ->update(['payment_method' => $validated['payment_method']]);

            // Always return JSON for this endpoint
            return response()->json([
                'success' => true,
                'message' => "Payment method updated for {$updatedCount} payment(s) successfully.",
                'updated_count' => $updatedCount
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $errors->all()),
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Bulk update payment method error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'payment_method' => $request->input('payment_method'),
                'request_all' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique payment reference
     */
    private function generatePaymentReference()
    {
        return 'PAY-' . strtoupper(uniqid());
    }

    /**
     * Calculate the amount for a booking based on its details
     */
    private function calculateBookingAmount(Booking $booking)
    {
        $baseAmount = 0;
        
        // Calculate room cost
        if ($booking->room) {
            $checkIn = \Carbon\Carbon::parse($booking->check_in_date);
            $checkOut = \Carbon\Carbon::parse($booking->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            $baseAmount += $booking->room->price * $nights;
        }
        
        // Add service costs if any
        if ($booking->services && $booking->services->count() > 0) {
            foreach ($booking->services as $service) {
                $baseAmount += $service->price * ($service->pivot->quantity ?? 1);
            }
        }
        
        // Add any additional fees
        $baseAmount += $booking->additional_fees ?? 0;
        
        // Apply discounts if any
        $baseAmount -= $booking->discount_amount ?? 0;
        
        return max(0, $baseAmount); // Ensure amount is not negative
    }

    /**
     * Process payment for a service request
     */
    public function storeServicePayment(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Calculate exact service amount
            $amount = $this->calculateServiceAmount($serviceRequest);
            
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'service_request_id' => $serviceRequest->id,
                'payment_reference' => $this->generatePaymentReference(),
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending', // Default status is pending, admin/manager must update status
                'payment_date' => now(),
                'notes' => $request->notes,
            ]);

            // Update service request total
            $serviceRequest->update(['total_amount' => $amount]);

            DB::commit();
            return redirect()->route('payments.confirmation', $payment);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Service payment processing failed. Please try again.');
        }
    }

    /**
     * Calculate the amount for a service request based on its details
     */
    private function calculateServiceAmount(ServiceRequest $serviceRequest)
    {
        $amount = 0;
        
        if ($serviceRequest->service) {
            // Base service price
            $amount += $serviceRequest->service->price;
            
            // Multiply by quantity if applicable
            $amount *= $serviceRequest->quantity ?? 1;
            
            // Add duration-based pricing if applicable
            if ($serviceRequest->service->duration && $serviceRequest->duration) {
                $hourlyRate = $serviceRequest->service->price; // Assuming price is per hour
                $amount = $hourlyRate * $serviceRequest->duration;
            }
            
            // Add any additional service fees
            $amount += $serviceRequest->additional_fees ?? 0;
        }
        
        return max(0, $amount);
    }
}
