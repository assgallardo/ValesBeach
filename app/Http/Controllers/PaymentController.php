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
use Illuminate\Support\Facades\Response;

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
        $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,gcash,paymaya,online',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Calculate the exact amount based on booking details
            $amount = $this->calculateBookingAmount($booking);
            
            // Create payment record
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'booking_id' => $booking->id,
                'payment_reference' => $this->generatePaymentReference(),
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
                'payment_date' => $request->payment_method === 'cash' ? now() : null,
                'notes' => $request->notes,
                'transaction_id' => $request->transaction_id ?? null,
            ]);

            // Update booking total if needed
            $booking->update(['total_amount' => $amount]);

            DB::commit();
            return redirect()->route('payments.confirmation', $payment);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Payment processing failed. Please try again.');
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
        // Load all necessary relationships
        $payment->load([
            'user',
            'booking.room',
            'serviceRequest.service',
            'refundedBy'
        ]);
        
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
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
     * Export payments data to CSV
     */
    public function export(Request $request)
    {
        // Get the same filtered query as the index method
        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $query = Payment::with(['booking.room', 'user', 'refundedBy']);

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

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        // Create CSV content
        $csvData = [];
        $csvData[] = [
            'Payment Reference',
            'User Name',
            'User Email',
            'Payment Type',
            'Amount',
            'Refund Amount',
            'Net Amount',
            'Payment Method',
            'Status',
            'Transaction ID',
            'Payment Date',
            'Created At',
            'Refund Reason',
            'Refunded By',
            'Refunded At',
            'Notes'
        ];

        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->payment_reference,
                $payment->user->name ?? 'N/A',
                $payment->user->email ?? 'N/A',
                $payment->payment_category ?? 'N/A',
                number_format($payment->amount, 2),
                number_format($payment->refund_amount ?? 0, 2),
                number_format($payment->amount - ($payment->refund_amount ?? 0), 2),
                ucfirst($payment->payment_method),
                ucfirst($payment->status),
                $payment->transaction_id ?? 'N/A',
                $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : 'N/A',
                $payment->created_at->format('Y-m-d H:i:s'),
                $payment->refund_reason ?? 'N/A',
                $payment->refundedBy->name ?? 'N/A',
                $payment->refunded_at ? $payment->refunded_at->format('Y-m-d H:i:s') : 'N/A',
                $payment->notes ?? 'N/A'
            ];
        }

        // Generate CSV file
        $filename = 'payments_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
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
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'refund_amount' => 'required|numeric|min:0.01|max:' . $payment->getRemainingRefundableAmount(),
            'refund_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $refundAmount = $request->refund_amount;
            $totalRefunded = ($payment->refund_amount ?? 0) + $refundAmount;

            // Update payment with refund information
            $payment->update([
                'refund_amount' => $totalRefunded,
                'refund_reason' => $request->refund_reason,
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
                        'cancellation_reason' => 'Fully refunded: ' . $request->refund_reason
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
                        'cancellation_reason' => 'Fully refunded: ' . $request->refund_reason
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
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load(['booking', 'user', 'booking.room', 'serviceRequest', 'refundedBy']);

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
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,failed,refunded',
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

    public function index(Request $request)
    {
        // Check if user has admin or manager access
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'manager'])) {
            abort(403, 'Only administrators and managers can access this page.');
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

        // Build query with relationships
        $query = Payment::with([
            'booking.room', 
            'serviceRequest.service', 
            'user'
        ]);

        // Apply filters
        if ($status) {
            $query->where('status', $status);
        }

        if ($paymentMethod) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($paymentType) {
            if ($paymentType === 'booking') {
                $query->whereNotNull('booking_id');
            } elseif ($paymentType === 'service') {
                $query->whereNotNull('service_request_id');
            }
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhere('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('booking', function($bookingQuery) use ($search) {
                      $bookingQuery->where('booking_reference', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate comprehensive statistics
        $stats = [
            'total_payments' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'failed_payments' => Payment::where('status', 'failed')->sum('amount'),
            'total_refunds' => Payment::whereNotNull('refund_amount')->sum('refund_amount'),
            'booking_payments' => Payment::whereNotNull('booking_id')->where('status', 'completed')->sum('amount'),
            'service_payments' => Payment::whereNotNull('service_request_id')->where('status', 'completed')->sum('amount'),
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
        ];

        // Return appropriate view based on route prefix
        if ($routePrefix === 'manager') {
            return view('manager.payments.index', compact('payments', 'stats'));
        } else {
            return view('admin.payments.index', compact('payments', 'stats'));
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
                'status' => $request->payment_method === 'cash' ? 'completed' : 'pending',
                'payment_date' => $request->payment_method === 'cash' ? now() : null,
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
