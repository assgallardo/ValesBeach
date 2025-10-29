<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices (role-aware)
     */
    public function index()
    {
        $role = Auth::user()->role;
        
        // Admin, Manager, and Staff see all invoices
        if (in_array($role, ['admin', 'manager', 'staff'])) {
            $invoices = Invoice::with(['booking', 'booking.room', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
                
            // Calculate stats for admin/manager/staff
            $stats = [
                'total_invoiced' => Invoice::sum('total_amount'),
                'paid_invoices' => Invoice::paid()->sum('total_amount'),
                'pending_invoices' => Invoice::whereIn('status', ['draft', 'sent'])->sum('total_amount'),
                'overdue_count' => Invoice::overdue()->count()
            ];
            
            return view('invoices.index', compact('invoices', 'stats'));
        }
        
        // Guests see only their own invoices
        $invoices = Auth::user()->invoices()
            ->with(['booking', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Generate invoice for a booking
     */
    public function generate(Request $request, Booking $booking)
    {
        // Ensure user can access this booking
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'manager' && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        // Load necessary relationships
        $booking->load(['room', 'invoice']);

        // Check if invoice already exists
        if ($booking->invoice) {
            return redirect()->route('invoices.show', $booking->invoice)
                ->with('info', 'Invoice already exists for this booking.');
        }

        // Ensure booking has payment
        if ($booking->amount_paid <= 0) {
            return back()->with('error', 'Cannot generate invoice for booking without payment.');
        }

        // Validate request with defaults
        $validated = $request->validate([
            'due_date' => 'nullable|date|after_or_equal:today',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Set defaults if not provided
        $validated['due_date'] = $validated['due_date'] ?? now()->addDays(7)->format('Y-m-d');
        $validated['tax_rate'] = $validated['tax_rate'] ?? 0;

        // Use database transaction to ensure lock works properly
        $invoice = \DB::transaction(function () use ($booking, $validated) {
            // Calculate line items
            $nights = $booking->check_in->diffInDays($booking->check_out);
            $lineItems = [
                [
                    'description' => 'Room: ' . $booking->room->name,
                    'quantity' => $nights,
                    'unit_price' => $booking->room->price,
                    'total' => $booking->room->price * $nights
                ]
            ];

            $subtotal = $booking->total_price;
            $taxRate = $validated['tax_rate'] ?? 0;
            $taxAmount = ($subtotal * $taxRate) / 100;
            $totalAmount = $subtotal + $taxAmount;

            // Determine invoice status based on payment status
            $invoiceStatus = 'sent';
            if ($booking->payment_status === 'paid' || $booking->remaining_balance <= 0) {
                $invoiceStatus = 'paid';
            }

            // Create invoice
            return Invoice::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'issue_date' => now(),
                'due_date' => $validated['due_date'],
                'line_items' => $lineItems,
                'status' => $invoiceStatus,
                'paid_date' => $invoiceStatus === 'paid' ? now() : null,
                'notes' => $validated['notes'] ?? null
            ]);
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully!');
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        // Ensure user can access this invoice
        // Allow: invoice owner (guest), admin, manager, staff
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff']) && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // Load necessary relationships - including all payments for the user
        $invoice->load([
            'booking.room', 
            'booking.payments', 
            'user',
            'user.payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Get the general payment method (most common one used by the user)
        $userPayments = $invoice->user->payments ?? collect();
        $paymentMethodCounts = $userPayments->groupBy('payment_method')->map->count();
        $generalPaymentMethod = $paymentMethodCounts->isNotEmpty() 
            ? $paymentMethodCounts->sortDesc()->keys()->first() 
            : null;

        return view('invoices.show', compact('invoice', 'generalPaymentMethod'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        // Ensure user can access this invoice
        // Allow: invoice owner (guest), admin, manager, staff
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'staff']) && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // Load necessary relationships
        $invoice->load(['booking.room', 'booking.payments', 'user']);

        // Get the general payment method (most common one used by the user)
        $userPayments = $invoice->user->payments ?? collect();
        $paymentMethodCounts = $userPayments->groupBy('payment_method')->map->count();
        $generalPaymentMethod = $paymentMethodCounts->isNotEmpty() 
            ? $paymentMethodCounts->sortDesc()->keys()->first() 
            : null;

        // For now, return the printable view
        // In production, you would use a PDF library like DomPDF or wkhtmltopdf
        return view('invoices.pdf', compact('invoice', 'generalPaymentMethod'));
    }

    /**
     * Admin invoice dashboard
     */
    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can access this page.');
        }

        $invoices = Invoice::with(['booking', 'user', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_invoiced' => Invoice::sum('total_amount'),
            'paid_invoices' => Invoice::paid()->sum('total_amount'),
            'overdue_invoices' => Invoice::overdue()->sum('total_amount'),
            'draft_invoices' => Invoice::where('status', 'draft')->count()
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Update invoice status (admin only)
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can update invoice status.');
        }

        $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string'
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'paid') {
            $updateData['paid_date'] = now();
        }

        if ($request->notes) {
            $updateData['notes'] = $request->notes;
        }

        $invoice->update($updateData);

        // If marking as paid, update booking status
        if ($request->status === 'paid' && $invoice->booking->isPaid()) {
            $invoice->booking->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'Invoice status updated successfully.');
    }

    /**
     * Send invoice reminder (admin only)
     */
    public function sendReminder(Invoice $invoice)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can send invoice reminders.');
        }

        // Here you would implement email notification
        // For now, just return success message
        
        return back()->with('success', 'Invoice reminder sent successfully.');
    }

    /**
     * Create invoice from existing booking (admin)
     */
    public function create(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only administrators can create invoices.');
        }

        $bookings = Booking::with('room', 'user')
            ->whereDoesntHave('invoice')
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.invoices.create', compact('bookings'));
    }

    /**
     * Generate combined invoice for multiple items (bookings, services, food orders)
     */
    public function generateCombined(Request $request)
    {
        $request->validate([
            'bookings' => 'nullable|array',
            'bookings.*' => 'exists:bookings,id',
            'services' => 'nullable|array',
            'services.*' => 'exists:payments,id',
            'food_orders' => 'nullable|array',
            'food_orders.*' => 'exists:payments,id',
        ]);

        // Ensure at least one item is selected
        if (empty($request->bookings) && empty($request->services) && empty($request->food_orders)) {
            return redirect()->back()->with('error', 'Please select at least one item to include in the invoice.');
        }

        // Collect all items
        $items = [];
        $total = 0;

        // Add bookings
        if ($request->bookings) {
            $bookings = Booking::with('room')->whereIn('id', $request->bookings)->get();
            foreach ($bookings as $booking) {
                // Only include bookings the user owns
                if ($booking->user_id !== Auth::id()) {
                    continue;
                }
                
                $items[] = [
                    'type' => 'booking',
                    'id' => $booking->id,
                    'description' => $booking->room->name,
                    'reference' => $booking->booking_reference,
                    'details' => $booking->check_in->format('M d') . ' - ' . $booking->check_out->format('M d, Y') . ' (' . $booking->check_in->diffInDays($booking->check_out) . ' nights)',
                    'quantity' => 1,
                    'unit_price' => $booking->total_price,
                    'amount' => $booking->total_price,
                    'paid' => $booking->amount_paid,
                    'balance' => $booking->remaining_balance
                ];
                $total += $booking->total_price;
            }
        }

        // Add services
        if ($request->services) {
            $services = \App\Models\Payment::with('serviceRequest.service')
                ->whereIn('id', $request->services)
                ->whereNotNull('service_request_id')
                ->get();
                
            foreach ($services as $payment) {
                // Only include payments the user owns
                if ($payment->user_id !== Auth::id()) {
                    continue;
                }
                
                $items[] = [
                    'type' => 'service',
                    'id' => $payment->id,
                    'description' => $payment->serviceRequest->service->name ?? 'Service Request',
                    'reference' => $payment->payment_reference,
                    'details' => 'Service completed on ' . $payment->created_at->format('M d, Y'),
                    'quantity' => 1,
                    'unit_price' => $payment->amount,
                    'amount' => $payment->amount,
                    'paid' => $payment->status === 'completed' ? $payment->amount : 0,
                    'balance' => $payment->status === 'completed' ? 0 : $payment->amount
                ];
                $total += $payment->amount;
            }
        }

        // Add food orders
        if ($request->food_orders) {
            $foodOrders = \App\Models\Payment::with('foodOrder.orderItems.menuItem')
                ->whereIn('id', $request->food_orders)
                ->whereNotNull('food_order_id')
                ->get();
                
            foreach ($foodOrders as $payment) {
                // Only include payments the user owns
                if ($payment->user_id !== Auth::id()) {
                    continue;
                }
                
                $orderDetails = '';
                if ($payment->foodOrder) {
                    $orderDetails = 'Order #' . $payment->foodOrder->order_number . ' - ' . $payment->foodOrder->orderItems->count() . ' items';
                }
                
                $items[] = [
                    'type' => 'food_order',
                    'id' => $payment->id,
                    'description' => 'Food Order',
                    'reference' => $payment->payment_reference,
                    'details' => $orderDetails,
                    'quantity' => 1,
                    'unit_price' => $payment->amount,
                    'amount' => $payment->amount,
                    'paid' => $payment->status === 'completed' ? $payment->amount : 0,
                    'balance' => $payment->status === 'completed' ? 0 : $payment->amount
                ];
                $total += $payment->amount;
            }
        }

        // Calculate totals
        $totalPaid = collect($items)->sum('paid');
        $totalBalance = collect($items)->sum('balance');

        // Generate invoice number
        $invoiceNumber = 'INV-' . strtoupper(uniqid());

        // Store invoice data in session for display
        session([
            'combined_invoice' => [
                'invoice_number' => $invoiceNumber,
                'items' => $items,
                'subtotal' => $total,
                'total' => $total,
                'total_paid' => $totalPaid,
                'total_balance' => $totalBalance,
                'created_at' => now()
            ]
        ]);

        return redirect()->route('invoices.show-combined');
    }

    /**
     * Show combined invoice
     */
    public function showCombined()
    {
        $invoiceData = session('combined_invoice');
        
        if (!$invoiceData) {
            return redirect()->route('payments.history')->with('error', 'Invoice data not found. Please generate a new invoice.');
        }

        // Load user payments for display in invoice
        $user = Auth::user();
        if ($user) {
            $user->load('payments');
            // Attach payments to invoice data
            $invoiceData['user_payments'] = $user->payments->sortByDesc('created_at')->take(10)->values();
            
            // Get the general payment method (most common one used by the user)
            $paymentMethodCounts = $user->payments->groupBy('payment_method')->map->count();
            $invoiceData['general_payment_method'] = $paymentMethodCounts->isNotEmpty() 
                ? $paymentMethodCounts->sortDesc()->keys()->first() 
                : null;
        }

        return view('invoices.combined', ['invoice' => (object) $invoiceData]);
    }
}
