<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} - ValesBeach Resort</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #059669;
            color: white;
            padding: 30px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details {
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table .text-right {
            text-align: right;
        }
        .table .text-center {
            text-align: center;
        }
        .badge-type {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-booking { background:#dbeafe; color:#1e3a8a; }
        .badge-service { background:#dcfce7; color:#166534; }
        .badge-food { background:#fef9c3; color:#854d0e; }
        .badge-extra { background:#ede9fe; color:#5b21b6; }
        .totals {
            float: right;
            width: 300px;
            margin-bottom: 30px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 8px;
            border: none;
            border-bottom: 1px solid #ddd;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #333;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .payment-history {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .notes {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            color: #666;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #10b981; color: white; }
        .status-sent { background-color: #3b82f6; color: white; }
        .status-draft { background-color: #6b7280; color: white; }
        .status-overdue { background-color: #ef4444; color: white; }
        .status-cancelled { background-color: #6b7280; color: white; }
        .overdue { color: #ef4444; font-weight: bold; }
        .paid { color: #10b981; font-weight: bold; }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h1>ValesBeach Resort</h1>
                <p>Premium Beach Resort Experience</p>
                <div style="margin-top: 15px; font-size: 14px;">
                    <p>123 Beach Resort Drive<br>
                    Paradise Island, Philippines<br>
                    Phone: +63 123 456 7890<br>
                    Email: billing@valesbeach.com</p>
                </div>
            </div>
            <div style="text-align: right;">
                <h2 style="margin: 0; font-size: 24px;">INVOICE</h2>
                <p style="margin: 10px 0 0 0; font-size: 16px;">{{ $invoice->invoice_number }}</p>
                <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                @if($invoice->isOverdue())
                    <br><span class="status-badge status-overdue">Overdue</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Info -->
    <div class="invoice-info">
        <div>
            <h3>Bill To:</h3>
            <p><strong>{{ $invoice->user->name }}</strong><br>
            {{ $invoice->user->email }}</p>
        </div>
        <div class="invoice-details">
            <table style="text-align: right;">
                @if($invoice->invoice_date)
                <tr>
                    <td><strong>Invoice Date:</strong></td>
                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                </tr>
                @elseif($invoice->issue_date)
                <tr>
                    <td><strong>Issue Date:</strong></td>
                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td><strong>Due Date:</strong></td>
                    <td class="{{ $invoice->isOverdue() ? 'overdue' : '' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                    </td>
                </tr>
                @if($invoice->paid_date)
                <tr>
                    <td><strong>Paid Date:</strong></td>
                    <td class="paid">{{ $invoice->paid_date->format('M d, Y') }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Booking Details -->
    @if($invoice->booking_id && $invoice->booking)
    <div class="booking-details">
        <h3>Booking Details</h3>
        <p><strong>Booking Reference:</strong> {{ $invoice->booking->booking_reference }}</p>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="width: 25%;"><strong>Check-in:</strong><br>{{ $invoice->booking->check_in->format('M d, Y') }}</td>
                <td style="width: 25%;"><strong>Check-out:</strong><br>{{ $invoice->booking->check_out->format('M d, Y') }}</td>
                <td style="width: 25%;"><strong>Nights:</strong><br>{{ $invoice->booking->check_in->diffInDays($invoice->booking->check_out) }}</td>
                <td style="width: 25%;"><strong>Guests:</strong><br>{{ $invoice->booking->guests }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Items / Line Items -->
    @if($invoice->items)
    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Reference</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <span class="badge-type
                        {{ $item['type'] == 'booking' ? 'badge-booking' : '' }}
                        {{ $item['type'] == 'service' ? 'badge-service' : '' }}
                        {{ $item['type'] == 'food' ? 'badge-food' : '' }}
                        {{ $item['type'] == 'extra' ? 'badge-extra' : '' }}">
                        {{ ucfirst($item['type']) }}
                    </span>
                </td>
                <td>
                    <strong>{{ $item['description'] }}</strong>
                    @if(!empty($item['details']))
                        <br><small style="color:#555;">{{ $item['details'] }}</small>
                    @endif
                </td>
                <td style="font-size: 12px;">{{ $item['reference'] ?? '-' }}</td>
                <td class="text-right">₱{{ number_format($item['amount'], 2) }}</td>
                <td class="text-right" style="color:#059669;">₱{{ number_format($item['paid'], 2) }}</td>
                <td class="text-right" style="{{ $item['balance'] > 0 ? 'color:#dc2626; font-weight:600;' : 'color:#555;' }}">₱{{ number_format($item['balance'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($invoice->line_items)
                @foreach($invoice->line_items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                    <td class="text-right">₱{{ number_format($item['total'], 2) }}</td>
                </tr>
                @endforeach
            @elseif($invoice->booking)
            <tr>
                <td>{{ $invoice->booking->room->name }} - Room Booking</td>
                <td class="text-center">{{ $invoice->booking->check_in->diffInDays($invoice->booking->check_out) }}</td>
                <td class="text-right">₱{{ number_format($invoice->booking->room->price, 2) }}</td>
                <td class="text-right">₱{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @else
            <tr>
                <td colspan="4" class="text-center">No line items available.</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    <!-- Totals -->
    <div class="clearfix">
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;">{{ $invoice->formatted_subtotal }}</td>
                </tr>
                @if($invoice->tax_rate > 0)
                <tr>
                    <td>VAT ({{ $invoice->tax_rate }}%):</td>
                    <td style="text-align: right;">{{ $invoice->formatted_tax_amount }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total:</td>
                    <td style="text-align: right; color: #059669;">{{ $invoice->formatted_total_amount }}</td>
                </tr>
                @if($invoice->items)
                <tr>
                    <td style="padding-top:10px; font-weight:600;">Amount Paid:</td>
                    <td style="text-align:right; padding-top:10px; color:#059669; font-weight:600;">₱{{ number_format($invoice->amount_paid ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Balance Due:</td>
                    <td style="text-align:right; font-weight:600; {{ ($invoice->balance_due ?? 0) > 0 ? 'color:#dc2626;' : 'color:#555;' }}">₱{{ number_format($invoice->balance_due ?? 0, 2) }}</td>
                </tr>
                @endif
                @if(isset($generalPaymentMethod) && $generalPaymentMethod)
                <tr style="border-top: 1px solid #e5e7eb; padding-top: 10px;">
                    <td style="font-weight: 600; padding-top: 10px;">Payment Method:</td>
                    <td style="text-align: right; font-weight: 600; padding-top: 10px;">{{ ucfirst(str_replace('_', ' ', $generalPaymentMethod)) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>


    <!-- Notes -->
    @if($invoice->notes)
    <div class="notes">
        <h3>Notes</h3>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>Thank you for choosing ValesBeach Resort!</strong></p>
        <p>For billing inquiries, please contact us at billing@valesbeach.com or +63 123 456 7890</p>
        <p style="margin-top: 20px; font-size: 12px;">
            This is a computer-generated invoice. Generated on {{ now()->format('M d, Y g:i A') }}
        </p>
    </div>

    @php
        $role = auth()->user()->role;
        if ($role === 'admin') {
            $paymentRoute = route('admin.payments.customer', $invoice->user_id);
        } elseif ($role === 'manager') {
            $paymentRoute = route('admin.payments.customer', $invoice->user_id);
        } elseif ($role === 'staff') {
            $paymentRoute = route('admin.payments.customer', $invoice->user_id);
        } else {
            $paymentRoute = route('invoices.index');
        }
    @endphp

    <!-- Navigation Buttons (hide on print) -->
    <div style="margin-top: 30px; text-align: center;" class="no-print">
        @if(in_array($role, ['admin', 'manager', 'staff']))
        <a href="{{ $paymentRoute }}" 
           style="display: inline-block; padding: 12px 24px; background-color: #4b5563; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0 5px;">
            ← Back to Payments
        </a>
        @else
        <a href="{{ route('invoices.index') }}" 
           style="display: inline-block; padding: 12px 24px; background-color: #4b5563; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 0 5px;">
            ← Back to Invoices
        </a>
        @endif
        
        <button onclick="window.print()" 
                style="display: inline-block; padding: 12px 24px; background-color: #059669; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; margin: 0 5px;">
            🖨️ Print Invoice
        </button>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</body>
</html>
