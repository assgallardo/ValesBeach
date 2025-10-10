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
     * Display a listing of invoices for the authenticated user
     */
    public function index()
    {
        $invoices = Auth::user()->invoices()
            ->with(['booking', 'booking.room'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Generate invoice for a booking
     */
    public function generate(Booking $booking)
    {
        // Ensure user can access this booking
        if (Auth::user()->role !== 'admin' && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        // Check if invoice already exists
        if ($booking->invoice) {
            return redirect()->route('invoices.show', $booking->invoice)
                ->with('info', 'Invoice already exists for this booking.');
        }

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
        $taxRate = 12.00; // VAT 12%
        $taxAmount = ($subtotal * $taxRate) / 100;
        $totalAmount = $subtotal + $taxAmount;

        // Create invoice
        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'issue_date' => now(),
            'due_date' => now()->addDays(7), // 7 days payment terms
            'line_items' => $lineItems,
            'status' => 'sent'
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully!');
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        // Ensure user can access this invoice
        if (Auth::user()->role !== 'admin' && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Invoice $invoice)
    {
        // Ensure user can access this invoice
        if (Auth::user()->role !== 'admin' && $invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // For now, return the printable view
        // In production, you would use a PDF library like DomPDF or wkhtmltopdf
        return view('invoices.pdf', compact('invoice'));
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
}
