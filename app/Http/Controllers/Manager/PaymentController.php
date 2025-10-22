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

        $query = Payment::with(['booking', 'user', 'booking.room', 'booking.invoice', 'serviceRequest', 'refundedBy']);

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

        // Payment statistics for managers
        $stats = [
            'total_payments' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->sum('amount'),
            'today_payments' => Payment::completed()->whereDate('created_at', today())->sum('amount'),
            'total_transactions' => Payment::count(),
            'completed_count' => Payment::completed()->count(),
            'pending_count' => Payment::pending()->count(),
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
                'amount' => Payment::completed()->whereDate('created_at', $date)->sum('amount'),
                'count' => Payment::completed()->whereDate('created_at', $date)->count(),
            ];
        }

        // Get bookings with payments for card display
        $bookings = \App\Models\Booking::with(['room', 'payments', 'invoice'])
            ->whereHas('payments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get service payments separately
        $servicePayments = Payment::whereNotNull('service_request_id')
            ->with(['serviceRequest', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('manager.payments.index', compact('payments', 'stats', 'recent_payments', 'payment_trends', 'bookings', 'servicePayments'));
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
            abort(403, 'Only administrators, managers, and staff can update payment status.');
        }

        // Managers can only update certain statuses
        $allowedStatuses = Auth::user()->role === 'admin' 
            ? ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled']
            : ['pending', 'processing', 'completed', 'cancelled']; // Managers cannot mark as failed or refunded

        $request->validate([
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

        return back()->with('success', 'Payment status updated successfully.');
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
     * Export payment data (CSV format)
     */
    public function export(Request $request)
    {
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff'])) {
            abort(403, 'Only administrators, managers, and staff can export payment data.');
        }

        $query = Payment::with(['booking', 'user', 'serviceRequest']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Payment Reference',
                'Customer Name',
                'Customer Email',
                'Amount',
                'Payment Method',
                'Status',
                'Type',
                'Created Date',
                'Payment Date',
                'Notes'
            ]);

            // CSV data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->payment_reference,
                    $payment->user->name,
                    $payment->user->email,
                    $payment->amount,
                    $payment->payment_method_display,
                    ucfirst($payment->status),
                    $payment->payment_category,
                    $payment->created_at->format('Y-m-d H:i:s'),
                    $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : '',
                    $payment->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
