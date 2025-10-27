@extends('layouts.admin')

@section('title', 'Payment Management')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-green-50">Payment Management</h1>
                <p class="text-gray-400 mt-2">Manage all guest payments for bookings and services</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button type="button" 
                        onclick="openGenerateInvoiceModal()" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Generate Invoice
                </button>
                <button type="button" 
                        onclick="toggleFilterPanel()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filters
                </button>
                <a href="{{ route('admin.payments.export', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </a>
            </div>
        </div>

        <!-- Statistics Cards - Clean & Simple -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Revenue -->
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Total Revenue</p>
                        <p class="text-xl font-bold text-green-50">₱{{ number_format($stats['total_payments'], 2) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-400"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Pending Payments</p>
                        <p class="text-xl font-bold text-green-50">₱{{ number_format($stats['pending_payments'], 2) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-400"></i>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Total Transactions</p>
                        <p class="text-xl font-bold text-green-50">{{ number_format($stats['total_count']) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Total Refunds -->
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Total Refunds</p>
                        <p class="text-xl font-bold text-green-50">₱{{ number_format($stats['total_refunds'], 2) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-undo text-red-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div id="filterPanel" class="bg-gray-800 rounded-lg p-6 mb-8 border border-gray-700 hidden">
            <form method="GET" action="{{ route('admin.payments.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="gcash" {{ request('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                        <option value="paymaya" {{ request('payment_method') === 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                        <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online Payment</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Payment Type</label>
                    <select name="payment_type" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Types</option>
                        <option value="booking" {{ request('payment_type') === 'booking' ? 'selected' : '' }}>Room Bookings</option>
                        <option value="service" {{ request('payment_type') === 'service' ? 'selected' : '' }}>Services</option>
                        <option value="food_order" {{ request('payment_type') === 'food_order' ? 'selected' : '' }}>Food & Beverage</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex flex-col justify-end">
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Apply
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center">
                            Clear
                        </a>
                    </div>
                </div>
            </form>

            <!-- Search Bar -->
            <div class="mt-4">
                <form method="GET" action="{{ route('admin.payments.index') }}">
                    @foreach(request()->except('search') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search by payment reference, guest name, or email..." 
                               value="{{ request('search') }}"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-green-50">Payment Transactions</h3>
                @if($payments->count() > 0)
                    <p class="text-sm text-gray-400 mt-2 sm:mt-0">
                        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                    </p>
                @endif
            </div>

            @if($payments->count() > 0)
                <div>
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[16%]">Guest</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[12%]">Payment Ref</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[18%]">Type & Service</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[11%]">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[10%]">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[9%]">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[10%]">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[14%]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Guest Info -->
                                <td class="px-4 py-3">
                                    <div class="font-medium text-green-50 text-sm truncate" title="{{ $payment->user->name ?? 'N/A' }}">
                                        {{ $payment->user->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-400 truncate" title="{{ $payment->user->email ?? 'N/A' }}">
                                        {{ $payment->user->email ?? 'N/A' }}
                                    </div>
                                </td>

                                <!-- Payment Details -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-blue-400 truncate" title="{{ $payment->payment_reference }}">
                                        {{ $payment->payment_reference }}
                                    </div>
                                </td>

                                <!-- Type & Service -->
                                <td class="px-4 py-3">
                                    @if($payment->booking)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-bed text-blue-400 mt-0.5"></i>
                                            <div class="text-sm overflow-hidden">
                                                <div class="font-medium text-green-50 truncate">Booking</div>
                                                <div class="text-xs text-gray-400 truncate" title="{{ $payment->booking->room->name ?? 'N/A' }}">
                                                    {{ $payment->booking->room->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($payment->serviceRequest)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-concierge-bell text-purple-400 mt-0.5"></i>
                                            <div class="text-sm overflow-hidden">
                                                <div class="font-medium text-green-50 truncate">Service</div>
                                                <div class="text-xs text-gray-400 truncate" title="{{ $payment->serviceRequest->service->name ?? 'N/A' }}">
                                                    {{ Str::limit($payment->serviceRequest->service->name ?? 'Service Request', 20) }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($payment->foodOrder)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-utensils text-orange-400 mt-0.5"></i>
                                            <div class="text-sm overflow-hidden">
                                                <div class="font-medium text-green-50 truncate">Food</div>
                                                <div class="text-xs text-gray-400 truncate">Order #{{ $payment->foodOrder->order_number }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-question-circle text-gray-400"></i>
                                            <span class="text-sm text-gray-400">Other</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-bold text-green-400">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </div>
                                    @if($payment->refund_amount > 0)
                                        <div class="text-xs text-red-400">
                                            -₱{{ number_format($payment->refund_amount, 2) }}
                                        </div>
                                        <div class="text-xs font-medium text-gray-300">
                                            Net: ₱{{ number_format($payment->amount - $payment->refund_amount, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Method -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-300 truncate">
                                        {{ $payment->payment_method_display }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    @php
                                        $statusConfig = match($payment->status) {
                                            'completed' => ['bg' => 'bg-green-600', 'text' => 'text-white'],
                                            'pending' => ['bg' => 'bg-yellow-600', 'text' => 'text-white'],
                                            'processing' => ['bg' => 'bg-blue-600', 'text' => 'text-white'],
                                            'failed' => ['bg' => 'bg-red-600', 'text' => 'text-white'],
                                            'refunded' => ['bg' => 'bg-gray-600', 'text' => 'text-white'],
                                            'cancelled' => ['bg' => 'bg-gray-700', 'text' => 'text-gray-300'],
                                            default => ['bg' => 'bg-gray-700', 'text' => 'text-gray-300']
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-xs {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} rounded">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-green-50">
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $payment->created_at->format('h:i A') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <a href="{{ route('admin.payments.show', $payment) }}" 
                                           class="inline-flex items-center px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors border border-gray-600" 
                                           title="View">
                                            <i class="fas fa-eye text-blue-400"></i>
                                        </a>
                                        
                                        @if($payment->status === 'pending')
                                            <button onclick="updatePaymentStatus({{ $payment->id }}, 'completed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors border border-gray-600" 
                                                    title="Complete">
                                                <i class="fas fa-check text-green-400"></i>
                                            </button>
                                        @endif

                                        @if($payment->canBeRefunded())
                                            <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->getRemainingRefundableAmount() }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors border border-gray-600" 
                                                    title="Refund">
                                                <i class="fas fa-undo text-yellow-400"></i>
                                            </button>
                                        @endif

                                        @if($payment->booking && $payment->booking->invoice)
                                            <a href="{{ route('invoices.show', $payment->booking->invoice) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors border border-gray-600" 
                                               title="Invoice">
                                                <i class="fas fa-file-invoice text-purple-400"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between">
                    <div class="text-sm text-gray-400 mb-4 sm:mb-0">
                        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments
                    </div>
                    <div class="flex-1 flex justify-end">
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-50 mb-2">No payments found</h3>
                    <p class="text-gray-400">Try adjusting your search filters or check back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-green-50">Process Refund</h3>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="refundForm" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Refund Amount</label>
                        <input type="number" name="refund_amount" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500" 
                               step="0.01" required>
                        <p class="text-xs text-gray-400 mt-1">Maximum: <span id="maxRefund"></span></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Refund Reason</label>
                        <textarea name="refund_reason" rows="3" 
                                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                  placeholder="Please provide a reason for the refund..." required></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeRefundModal()" 
                            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleFilterPanel() {
    const panel = document.getElementById('filterPanel');
    panel.classList.toggle('hidden');
}

function updatePaymentStatus(paymentId, status) {
    if (!status || !['pending', 'completed', 'failed', 'refunded', 'cancelled'].includes(status)) {
        console.error('Invalid payment status:', status);
        alert('Invalid payment status selected');
        return;
    }

    if (confirm('Are you sure you want to update this payment status to ' + status + '?')) {
        console.log('Updating payment status:', {
            paymentId: paymentId,
            status: status,
            action: `/admin/payments/${paymentId}/status`
        });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payments/${paymentId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('maxRefund').textContent = `₱${maxAmount.toFixed(2)}`;
    document.querySelector('input[name="refund_amount"]').max = maxAmount;
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundForm').reset();
}

// Close modal when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});

// Generate Invoice Modal Functions
function openGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.remove('hidden');
}

function closeGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.add('hidden');
    document.getElementById('generateInvoiceForm').reset();
    document.getElementById('generateInvoiceForm').action = '';
    updateButtonState();
}

function updateFormAction() {
    const bookingSelect = document.getElementById('booking_id');
    const form = document.getElementById('generateInvoiceForm');
    
    if (bookingSelect.value) {
        form.action = `/bookings/${bookingSelect.value}/invoice/generate`;
    } else {
        form.action = '';
    }
    updateButtonState();
}

function updateButtonState() {
    const bookingSelect = document.getElementById('booking_id');
    const button = document.getElementById('generateButton');
    
    if (bookingSelect.value) {
        button.disabled = false;
        button.className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    } else {
        button.disabled = true;
        button.className = 'flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed';
    }
}

// Close modal when clicking outside
document.getElementById('generateInvoiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateInvoiceModal();
    }
});
</script>

<!-- Generate Invoice Modal -->
<div id="generateInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-green-50">Generate Invoice</h3>
                <button onclick="closeGenerateInvoiceModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="generateInvoiceForm" method="POST">
                @csrf
                
                <!-- Booking Selection -->
                <div class="mb-4">
                    <label for="booking_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Select Booking
                    </label>
                    <select 
                        name="booking_id" 
                        id="booking_id" 
                        required 
                        onchange="updateFormAction()"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">Select a booking...</option>
                        @foreach(\App\Models\Booking::with('room', 'user')->whereDoesntHave('invoice')->whereHas('payments')->orderBy('created_at', 'desc')->get() as $booking)
                        <option value="{{ $booking->id }}">
                            {{ $booking->booking_reference }} - {{ $booking->room->name }} - {{ $booking->user->name }}
                            ({{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Only bookings with payments and without invoices are shown</p>
                </div>
                
                <!-- Due Date -->
                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-300 mb-2">
                        Due Date
                    </label>
                    <input 
                        type="date" 
                        name="due_date" 
                        id="due_date" 
                        value="{{ now()->addDays(7)->format('Y-m-d') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        required 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                </div>
                
                <!-- Tax Rate -->
                <div class="mb-4">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-300 mb-2">
                        Tax Rate (%)
                    </label>
                    <input 
                        type="number" 
                        name="tax_rate" 
                        id="tax_rate" 
                        value="0" 
                        min="0" 
                        max="100" 
                        step="0.01"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                    <p class="text-xs text-gray-400 mt-1">Enter tax rate (e.g., 12 for 12% VAT)</p>
                </div>
                
                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Additional notes for this invoice..."
                    ></textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeGenerateInvoiceModal()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        id="generateButton"
                        disabled
                        class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed"
                    >
                        <i class="fas fa-file-invoice mr-2"></i>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
