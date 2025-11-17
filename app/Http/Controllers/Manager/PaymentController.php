<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Manager payment tracking dashboard
     */
    public function index(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can access payment tracking.');
        }

        // Get filter parameters
        $status = $request->get('status');
        $paymentMethod = $request->get('payment_method');
        $paymentType = $request->get('payment_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        // Build query for payments (not grouped, show individual payments)
        $query = Payment::with(['user', 'booking.room', 'serviceRequest.service', 'foodOrder'])
            ->whereIn('status', ['pending', 'confirmed', 'processing', 'overdue', 'failed', 'cancelled', 'refunded']);

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
            } elseif ($paymentType === 'food_order') {
                $query->whereNotNull('food_order_id');
            }
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Apply search (search in user name or email)
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Payment statistics for managers
        $stats = [
            'total_payments' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'today_payments' => Payment::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
            'total_transactions' => Payment::count(),
            'completed_count' => Payment::where('status', 'completed')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
            'failed_count' => Payment::where('status', 'failed')->count(),
            'refunded_count' => Payment::where('status', 'refunded')->count(),
        ];

        // Recent payment activity
        $recent_payments = Payment::with(['booking', 'user', 'serviceRequest'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Payment trends (last 7 days)
        $payment_trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $payment_trends[] = [
                'date' => $date->format('M d'),
                'amount' => Payment::where('status', 'completed')->whereDate('created_at', $date)->sum('amount'),
                'count' => Payment::where('status', 'completed')->whereDate('created_at', $date)->count(),
            ];
        }

        return view('manager.payments.index', compact('payments', 'stats', 'recent_payments', 'payment_trends'));
    }

    /**
     * Show all payments for a specific customer
     */
    public function showCustomerPayments($userId)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        // Get transaction ID from request
        $transactionId = request()->get('transaction_id');
        
        if (!$transactionId) {
            return redirect()->back()->with('error', 'No payment transaction specified.');
        }

        $customer = \App\Models\User::with([
            'payments' => function($q) use ($transactionId) {
                $q->with(['booking.room', 'serviceRequest.service', 'foodOrder.orderItems.menuItem'])
                  ->where('payment_transaction_id', $transactionId)
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

        return view('manager.payments.customer', compact('customer'));
    }

    /**
     * Complete all payments for a specific customer payment transaction
     */
    public function completeAllCustomerPayments($userId)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer = \App\Models\User::findOrFail($userId);
        
        // Get request parameter for transaction ID
        $transactionId = request()->get('transaction_id');
        
        if (!$transactionId) {
            return redirect()->back()->with('error', 'No payment transaction specified.');
        }
        
        // Update all payments in this specific transaction to completed status
        $updatedCount = \App\Models\Payment::where('payment_transaction_id', $transactionId)
            ->where('user_id', $customer->id)
            ->whereIn('status', ['pending', 'confirmed', 'processing'])
            ->update(['status' => 'completed', 'payment_date' => now()]);

        return redirect()->route('manager.payments.completed')->with('success', "Payment transaction completed for {$customer->name}. {$updatedCount} payment(s) updated.");
    }

    /**
     * Revert all completed payments in a transaction back to confirmed status
     */
    public function revertAllCustomerPayments($userId)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            abort(403, 'Unauthorized access.');
        }

        $customer = \App\Models\User::findOrFail($userId);
        
        // Get request parameter for transaction ID
        $transactionId = request()->get('transaction_id');
        
        if (!$transactionId) {
            return redirect()->back()->with('error', 'No payment transaction specified.');
        }
        
        // Update all completed payments in this specific transaction back to confirmed
        $updatedCount = \App\Models\Payment::where('payment_transaction_id', $transactionId)
            ->where('user_id', $customer->id)
            ->where('status', 'completed')
            ->update(['status' => 'confirmed']);

        return redirect()->route('manager.payments.index')->with('success', "Payment transaction reverted for {$customer->name}. {$updatedCount} payment(s) updated to confirmed status.");
    }

    /**
     * Show completed customer payment details (read-only)
     */
    public function showCompletedCustomerPayments($userId)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Unauthorized access.');
        }

        // Get transaction ID from request
        $transactionId = request()->get('transaction_id');
        
        if (!$transactionId) {
            return redirect()->back()->with('error', 'No payment transaction specified.');
        }

        $customer = \App\Models\User::with([
            'payments' => function($q) use ($transactionId) {
                $q->with(['booking.room', 'serviceRequest.service', 'foodOrder.orderItems.menuItem'])
                  ->where('payment_transaction_id', $transactionId)
                  ->where('status', 'completed')
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

        return view('manager.payments.completed-customer', compact('customer'));
    }

    /**
     * Show payment details for manager
     */
    public function show(Payment $payment)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can view payment details.');
        }

        $payment->load(['booking', 'user', 'booking.room', 'serviceRequest', 'refundedBy']);

        // Get related payments for context
        $related_payments = [];
        if ($payment->booking_id) {
            $related_payments = Payment::where('booking_id', $payment->booking_id)
                ->where('id', '!=', $payment->id)
                ->with(['user'])
                ->get();
        }

        return view('manager.payments.show', compact('payment', 'related_payments'));
    }

    /**
     * Update payment status (manager can update certain statuses)
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Only administrators, managers, and staff can update payment status.');
        }

        try {
            // Managers can only update certain statuses
            $allowedStatuses = Auth::user()->role === 'admin' 
                ? ['pending', 'confirmed', 'processing', 'completed', 'overdue', 'failed', 'refunded', 'cancelled']
                : ['pending', 'confirmed', 'processing', 'completed', 'overdue', 'cancelled']; // Managers cannot mark as failed or refunded

            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', $allowedStatuses),
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
            if ($request->status === 'completed' && $payment->booking) {
                // Check if booking is now fully paid
                if ($payment->booking->isPaid()) {
                    // If booking is checked out, mark as completed
                    if ($payment->booking->status === 'checked_out') {
                        $payment->booking->update(['status' => 'completed']);
                    } 
                    // If booking is still pending or in other states, confirm it
                    elseif (in_array($payment->booking->status, ['pending', 'processing'])) {
                        $payment->booking->update(['status' => 'confirmed']);
                    }
                }
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
            \Log::error('Payment status update error (Manager): ' . $e->getMessage(), [
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

    /**
     * Payment analytics for managers
     */
    public function analytics(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can view payment analytics.');
        }

        $period = $request->get('period', '7days');

        // Calculate date range based on period
        switch ($period) {
            case '24hours':
                $start_date = now()->subDay();
                break;
            case '7days':
                $start_date = now()->subWeek();
                break;
            case '30days':
                $start_date = now()->subMonth();
                break;
            case '3months':
                $start_date = now()->subMonths(3);
                break;
            default:
                $start_date = now()->subWeek();
        }

        // Payment statistics
        $analytics = [
            'total_revenue' => Payment::completed()
                ->where('created_at', '>=', $start_date)
                ->sum('amount'),
            'total_transactions' => Payment::where('created_at', '>=', $start_date)->count(),
            'successful_payments' => Payment::completed()
                ->where('created_at', '>=', $start_date)
                ->count(),
            'failed_payments' => Payment::where('status', 'failed')
                ->where('created_at', '>=', $start_date)
                ->count(),
            'pending_payments' => Payment::pending()
                ->where('created_at', '>=', $start_date)
                ->count(),
            'refunded_amount' => Payment::where('refund_amount', '>', 0)
                ->where('created_at', '>=', $start_date)
                ->sum('refund_amount'),
        ];

        // Payment method breakdown
        $payment_methods = Payment::completed()
            ->where('created_at', '>=', $start_date)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Daily payment trends
        $daily_trends = Payment::completed()
            ->where('created_at', '>=', $start_date)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('manager.payments.analytics', compact('analytics', 'payment_methods', 'daily_trends', 'period'));
    }

    /**
     * Export payment data (CSV format - Customer Grouped)
     */
    public function export(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can export payment data.');
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

        $filename = 'customer_payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
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
            ]);

            // CSV data
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

                fputcsv($file, [
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
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display completed transactions for manager
     */
    public function completed()
    {
        // Get customers who have at least one completed payment
        $customers = \App\Models\User::whereHas('payments', function($query) {
            $query->where('status', 'completed');
        })
        ->with(['payments' => function($query) {
            $query->where('status', 'completed');
        }])
        ->withCount(['payments as completed_payments_count' => function($query) {
            $query->where('status', 'completed');
        }])
        ->get()
        ->map(function($customer) {
            $completedPayments = $customer->payments;
            
            $customer->total_amount = $completedPayments->sum('amount');
            $customer->payment_count = $completedPayments->count();
            $customer->latest_payment_date = $completedPayments->max('created_at');
            
            // Count payment types
            $customer->bookings_count = $completedPayments->where('booking_id', '!=', null)->count();
            $customer->services_count = $completedPayments->where('service_request_id', '!=', null)->count();
            $customer->food_orders_count = $completedPayments->where('food_order_id', '!=', null)->count();
            
            return $customer;
        })
        ->sortByDesc('latest_payment_date');

        return view('manager.payments.completed', compact('customers'));
    }
}
