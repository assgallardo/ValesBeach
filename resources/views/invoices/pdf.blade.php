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
                <tr>
                    <td><strong>Issue Date:</strong></td>
                    <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                </tr>
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

    <!-- Line Items -->
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
            @else
            <tr>
                <td>{{ $invoice->booking->room->name }} - Room Booking</td>
                <td class="text-center">{{ $invoice->booking->check_in->diffInDays($invoice->booking->check_out) }}</td>
                <td class="text-right">₱{{ number_format($invoice->booking->room->price, 2) }}</td>
                <td class="text-right">₱{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

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
            </table>
        </div>
    </div>

    <!-- Payment History -->
    @if($invoice->booking->payments->count() > 0)
    <div class="payment-history">
        <h3>Payment History</h3>
        @foreach($invoice->booking->payments->where('status', 'completed') as $payment)
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <span>{{ $payment->created_at->format('M d, Y') }} - {{ $payment->payment_method_display }}</span>
            <span><strong>{{ $payment->formatted_amount }}</strong></span>
        </div>
        @endforeach
        
        <hr style="margin: 15px 0;">
        <div style="display: flex; justify-content: space-between; font-weight: bold;">
            <span>Total Paid:</span>
            <span style="color: #10b981;">{{ $invoice->booking->formatted_total_paid }}</span>
        </div>
        @if($invoice->booking->remaining_balance > 0)
        <div style="display: flex; justify-content: space-between; font-weight: bold; color: #ef4444;">
            <span>Balance Due:</span>
            <span>{{ $invoice->booking->formatted_remaining_balance }}</span>
        </div>
        @endif
    </div>
    @endif

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
</body>
</html>
