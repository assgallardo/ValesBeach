@extends('layouts.invoice')

@section('title', 'Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    @php
                        $role = auth()->user()->role;
                        // Go back to customer payment details
                        if (in_array($role, ['admin', 'manager', 'staff'])) {
                            $backUrl = route('admin.payments.customer', ['user' => $invoice->user_id]);
                        } else {
                            $backUrl = route('invoices.index');
                        }
                    @endphp
                    <a href="{{ $backUrl }}" 
                       class="text-gray-400 hover:text-green-400 transition-colors"
                       title="Back to Customer Payments">
                        <i class="fas fa-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-green-50">Invoice {{ $invoice->invoice_number }}</h1>
                        @if($invoice->booking_id && $invoice->booking)
                            <p class="text-gray-400 mt-2">Booking Reference: {{ $invoice->booking->booking_reference }}</p>
                        @else
                            <p class="text-gray-400 mt-2">Customer Invoice</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-medium {{ $invoice->status_badge_class }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    @if($invoice->isOverdue())
                        <span class="inline-block px-3 py-1 rounded-full text-sm bg-red-600 text-white">
                            Overdue
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Content -->
        <div class="bg-white text-gray-900 rounded-lg overflow-hidden" id="invoice-content">
            <!-- Invoice Header -->
            <div class="bg-green-600 text-white p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold">ValesBeach Resort</h2>
                        <p class="mt-1">Premium Beach Resort Experience</p>
                        <div class="mt-3 text-sm">
                            <p>123 Beach Resort Drive</p>
                            <p>Paradise Island, Philippines</p>
                            <p>Phone: +63 123 456 7890</p>
                            <p>Email: billing@valesbeach.com</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <h3 class="text-xl font-bold">INVOICE</h3>
                        <p class="text-sm mt-2">{{ $invoice->invoice_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Bill To -->
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">Bill To:</h4>
                        <div class="text-sm">
                            <p class="font-medium">{{ $invoice->user->name }}</p>
                            <p>{{ $invoice->user->email }}</p>
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div class="text-right">
                        <div class="space-y-2 text-sm">
                            @if($invoice->invoice_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Invoice Date:</span>
                                <span>{{ $invoice->invoice_date->format('M d, Y') }}</span>
                            </div>
                            @elseif($invoice->issue_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Issue Date:</span>
                                <span>{{ $invoice->issue_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Due Date:</span>
                                <span class="{{ $invoice->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </span>
                            </div>
                            @if($invoice->paid_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Paid Date:</span>
                                <span class="text-green-600 font-medium">{{ $invoice->paid_date->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                @if($invoice->booking_id && $invoice->booking)
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-3">Booking Details</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Check-in:</span>
                            <p class="font-medium">{{ $invoice->booking->check_in->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Check-out:</span>
                            <p class="font-medium">{{ $invoice->booking->check_out->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Nights:</span>
                            <p class="font-medium">{{ $invoice->booking->check_in->diffInDays($invoice->booking->check_out) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Guests:</span>
                            <p class="font-medium">{{ $invoice->booking->guests }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Line Items -->
                <div class="mb-8">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                @if($invoice->items)
                                <th class="text-left py-3 text-gray-800">Type</th>
                                <th class="text-left py-3 text-gray-800">Description</th>
                                <th class="text-left py-3 text-gray-800">Reference</th>
                                <th class="text-right py-3 text-gray-800">Amount</th>
                                <th class="text-right py-3 text-gray-800">Paid</th>
                                <th class="text-right py-3 text-gray-800">Balance</th>
                                @else
                                <th class="text-left py-3 text-gray-800">Description</th>
                                <th class="text-center py-3 text-gray-800">Qty</th>
                                <th class="text-right py-3 text-gray-800">Unit Price</th>
                                <th class="text-right py-3 text-gray-800">Total</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($invoice->items)
                                @foreach($invoice->items as $item)
                                <tr class="border-b border-gray-200">
                                    <td class="py-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $item['type'] == 'booking' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $item['type'] == 'service' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $item['type'] == 'food' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $item['type'] == 'extra' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ ucfirst($item['type']) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <p class="font-medium">{{ $item['description'] }}</p>
                                        @if($item['details'])
                                            <p class="text-xs text-gray-600">{{ $item['details'] }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3 text-sm">{{ $item['reference'] ?? '-' }}</td>
                                    <td class="py-3 text-right">₱{{ number_format($item['amount'], 2) }}</td>
                                    <td class="py-3 text-right text-green-600">₱{{ number_format($item['paid'], 2) }}</td>
                                    <td class="py-3 text-right {{ $item['balance'] > 0 ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                        ₱{{ number_format($item['balance'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            @elseif($invoice->line_items)
                                @foreach($invoice->line_items as $item)
                                <tr class="border-b border-gray-200">
                                    <td class="py-3">{{ $item['description'] }}</td>
                                    <td class="py-3 text-center">{{ $item['quantity'] }}</td>
                                    <td class="py-3 text-right">₱{{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="py-3 text-right">₱{{ number_format($item['total'], 2) }}</td>
                                </tr>
                                @endforeach
                            @elseif($invoice->booking)
                            <tr class="border-b border-gray-200">
                                <td class="py-3">{{ $invoice->booking->room->name }} - Room Booking</td>
                                <td class="py-3 text-center">{{ $invoice->booking->check_in->diffInDays($invoice->booking->check_out) }}</td>
                                <td class="py-3 text-right">₱{{ number_format($invoice->booking->room->price, 2) }}</td>
                                <td class="py-3 text-right">₱{{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="space-y-2">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span>{{ $invoice->formatted_subtotal }}</span>
                            </div>
                            @if($invoice->tax_rate > 0)
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">VAT ({{ $invoice->tax_rate }}%):</span>
                                <span>{{ $invoice->formatted_tax_amount }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-3 border-t-2 border-gray-300 font-bold text-lg">
                                <span>Total:</span>
                                <span class="text-green-600">{{ $invoice->formatted_total_amount }}</span>
                            </div>
                            @if($invoice->items)
                                <div class="flex justify-between py-2 text-green-600">
                                    <span>Amount Paid:</span>
                                    <span class="font-medium">₱{{ number_format($invoice->amount_paid ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-t border-gray-300 {{ ($invoice->balance_due ?? 0) > 0 ? 'text-red-600' : 'text-gray-600' }} font-bold">
                                    <span>Balance Due:</span>
                                    <span>₱{{ number_format($invoice->balance_due ?? 0, 2) }}</span>
                                </div>
                            @endif
                            @if(isset($generalPaymentMethod) && $generalPaymentMethod)
                            <div class="flex justify-between py-2 border-t border-gray-300 pt-3 mt-2">
                                <span class="text-gray-600 font-medium">Payment Method:</span>
                                <span class="text-gray-900 font-semibold">{{ ucfirst(str_replace('_', ' ', $generalPaymentMethod)) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>


                <!-- Notes -->
                @if($invoice->notes)
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Notes</h4>
                    <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
                </div>
                @endif

                <!-- Footer -->
                <div class="mt-8 pt-6 border-t border-gray-300 text-center text-sm text-gray-600">
                    <p>Thank you for choosing ValesBeach Resort!</p>
                    <p class="mt-1">For billing inquiries, please contact us at billing@valesbeach.com or +63 123 456 7890</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 no-print">
            @if($invoice->booking_id && $invoice->booking && !$invoice->isPaid() && $invoice->booking->remaining_balance > 0)
            <a 
                href="{{ route('payments.create', $invoice->booking) }}" 
                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-green-700 transition-colors"
            >
                <i class="fas fa-credit-card mr-2"></i>
                Pay Now ({{ $invoice->booking->formatted_remaining_balance }})
            </a>
            @endif

            @if($invoice->items && in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
            <a 
                href="{{ route('admin.invoices.edit', $invoice->id) }}" 
                class="flex-1 bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-yellow-700 transition-colors"
            >
                <i class="fas fa-edit mr-2"></i>
                Edit Invoice
            </a>
            @endif
            
            <a 
                href="{{ route('invoices.download', $invoice) }}" 
                target="_blank"
                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 transition-colors"
            >
                <i class="fas fa-download mr-2"></i>
                Download PDF
            </a>
            
            <button 
                onclick="window.print()" 
                class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors"
            >
                <i class="fas fa-print mr-2"></i>
                Print Invoice
            </button>
            
            @php
                $role = auth()->user()->role;
                if ($role === 'admin') {
                    $dashboardRoute = route('admin.dashboard');
                    // Always go back to specific customer payment details
                    $paymentRoute = route('admin.payments.customer', $invoice->user_id);
                } elseif ($role === 'manager') {
                    $dashboardRoute = route('manager.dashboard');
                    // Always go back to specific customer payment details
                    $paymentRoute = route('admin.payments.customer', $invoice->user_id);
                } elseif ($role === 'staff') {
                    $dashboardRoute = route('staff.dashboard');
                    // Staff also uses customer payment details
                    $paymentRoute = route('admin.payments.customer', $invoice->user_id);
                } else {
                    $dashboardRoute = route('guest.dashboard');
                    $paymentRoute = route('invoices.index');
                }

                if ($invoice->booking_id && $invoice->booking) {
                    if ($role === 'admin') {
                        $bookingRoute = route('admin.bookings.show', $invoice->booking);
                    } elseif ($role === 'manager') {
                        $bookingRoute = route('manager.bookings.show', $invoice->booking);
                    } elseif ($role === 'staff') {
                        $bookingRoute = route('admin.bookings.show', $invoice->booking);
                    } else {
                        $bookingRoute = route('guest.bookings.show', $invoice->booking);
                    }
                }
            @endphp
            
            @if($invoice->booking_id && $invoice->booking)
            <a 
                href="{{ $bookingRoute }}" 
                class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 transition-colors"
            >
                <i class="fas fa-bed mr-2"></i>
                View Booking
            </a>
            @endif
            
            @if(in_array($role, ['admin', 'manager', 'staff']))
            <a 
                href="{{ route('admin.payments.customer', ['user' => $invoice->user_id]) }}" 
                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 transition-colors"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Customer Payments
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body { 
        background: white !important; 
    }
    .bg-gray-900 { 
        background: white !important; 
    }
    .no-print { 
        display: none !important; 
    }
    #invoice-content { 
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
@endsection
