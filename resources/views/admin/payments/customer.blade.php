@extends('layouts.admin')

@section('content')
<div class="py-12 min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.payments.index') }}" 
                       class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-green-50">Customer Payment Details</h1>
                </div>
                <a href="{{ route('admin.payments.customer.invoice', $customer->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                   target="_blank">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Generate Invoice
                </a>
            </div>
            <p class="text-gray-400">View all payment transactions for this customer</p>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-green-50 mb-2">{{ $customer->name }}</h2>
                    <p class="text-gray-400 mb-4">{{ $customer->email }}</p>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-600 text-white">
                            {{ ucfirst($customer->role) }}
                        </span>
                        <span class="text-sm text-gray-400">
                            Member since {{ $customer->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400 mb-1">Total Payments</div>
                    <div class="text-3xl font-bold text-green-400">
                        ₱{{ number_format($customer->payments->sum('amount'), 2) }}
                    </div>
                    <div class="text-sm text-gray-400 mt-1">
                        {{ $customer->payments->count() }} transaction{{ $customer->payments->count() !== 1 ? 's' : '' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Bookings -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-blue-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-bed text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-50">Bookings</h3>
                        <p class="text-sm text-gray-400">Room reservations</p>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('booking_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('booking_id', '!=', null)->count() }} payment{{ $customer->payments->where('booking_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Services -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-purple-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-concierge-bell text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-50">Services</h3>
                        <p class="text-sm text-gray-400">Additional services</p>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('service_request_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('service_request_id', '!=', null)->count() }} payment{{ $customer->payments->where('service_request_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Food Orders -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-orange-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-utensils text-orange-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-50">Food Orders</h3>
                        <p class="text-sm text-gray-400">Restaurant orders</p>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('food_order_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('food_order_id', '!=', null)->count() }} order{{ $customer->payments->where('food_order_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Extra Charges -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-yellow-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-receipt text-yellow-400 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-50">Extra Charges</h3>
                        <p class="text-sm text-gray-400">Additional charges</p>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->count() }} charge{{ $customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        <!-- All Payments Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-50">All Payment Transactions</h3>
            </div>

            @if($customer->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Payment Ref</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Type & Details</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($customer->payments as $payment)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Payment Reference -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-blue-400 font-mono">
                                        {{ $payment->payment_reference }}
                                    </div>
                                </td>

                                <!-- Type & Details -->
                                <td class="px-4 py-3">
                                    @if($payment->booking)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-bed text-blue-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Booking</div>
                                                <div class="text-xs text-gray-400">{{ $payment->booking->room->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $payment->booking->check_in_date ? \Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d') : '' }} - 
                                                    {{ $payment->booking->check_out_date ? \Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y') : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($payment->serviceRequest)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-concierge-bell text-purple-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Service</div>
                                                <div class="text-xs text-gray-400">{{ $payment->serviceRequest->service->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @elseif($payment->foodOrder)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-utensils text-orange-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Food Order</div>
                                                <div class="text-xs text-gray-400">Order #{{ $payment->foodOrder->order_number }}</div>
                                                @if($payment->foodOrder->orderItems->count() > 0)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $payment->foodOrder->orderItems->count() }} item{{ $payment->foodOrder->orderItems->count() > 1 ? 's' : '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(!$payment->booking && !$payment->serviceRequest && !$payment->foodOrder)
                                        @php
                                            $paymentDetails = $payment->payment_details ?? [];
                                            $isExtraCharge = isset($paymentDetails['extra_charge']) && $paymentDetails['extra_charge'];
                                            $description = $paymentDetails['description'] ?? 'Extra Charge';
                                            $reference = $paymentDetails['reference'] ?? '';
                                            $details = $paymentDetails['details'] ?? '';
                                        @endphp
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-receipt text-yellow-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Extra Charge</div>
                                                <div class="text-xs text-gray-400">{{ $description }}</div>
                                                @if($reference)
                                                    <div class="text-xs text-gray-500">Ref: {{ $reference }}</div>
                                                @endif
                                                @if($details)
                                                    <div class="text-xs text-gray-500">{{ $details }}</div>
                                                @endif
                                                @if(isset($paymentDetails['invoice_number']))
                                                    <div class="text-xs text-blue-400">Invoice: {{ $paymentDetails['invoice_number'] }}</div>
                                                @endif
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
                                    <div class="text-sm text-gray-300">
                                        {{ $payment->payment_method_display }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <select onchange="updatePaymentStatus({{ $payment->id }}, this.value)" 
                                            class="w-full text-xs rounded px-2 py-1 border-0 focus:ring-2 focus:ring-green-500
                                            @if($payment->status === 'completed') bg-green-600 text-white
                                            @elseif($payment->status === 'pending') bg-yellow-600 text-white
                                            @elseif($payment->status === 'confirmed') bg-blue-600 text-white
                                            @elseif($payment->status === 'overdue') bg-orange-600 text-white
                                            @elseif($payment->status === 'processing') bg-indigo-600 text-white
                                            @elseif($payment->status === 'failed') bg-red-600 text-white
                                            @elseif($payment->status === 'refunded') bg-gray-600 text-white
                                            @elseif($payment->status === 'cancelled') bg-gray-700 text-gray-300
                                            @else bg-gray-700 text-gray-300
                                            @endif">
                                        <option value="pending" {{ $payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $payment->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="completed" {{ $payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="overdue" {{ $payment->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="processing" {{ $payment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="failed" {{ $payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ $payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                        <option value="cancelled" {{ $payment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
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
                                    <div class="flex gap-1">
                                        <a href="{{ route('admin.payments.show', $payment) }}" 
                                           class="inline-flex items-center px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors border border-gray-600" 
                                           title="View Details">
                                            <i class="fas fa-eye text-blue-400"></i>
                                        </a>
                                        
                                        @php
                                            $remainingAmount = $payment->amount - ($payment->refund_amount ?? 0);
                                            $canRefund = in_array($payment->status, ['completed', 'confirmed']) && $remainingAmount > 0;
                                        @endphp
                                        
                                        @if($canRefund)
                                            <button onclick="showRefundModal({{ $payment->id }}, {{ $remainingAmount }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors" 
                                                    title="Process Refund">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @elseif($payment->status === 'refunded' || ($payment->refund_amount > 0 && $remainingAmount <= 0))
                                            <span class="inline-flex items-center px-2 py-1 text-xs bg-gray-600 text-gray-400 rounded" 
                                                  title="Fully Refunded">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @elseif($payment->amount <= 0)
                                            <span class="inline-flex items-center px-2 py-1 text-xs bg-red-600 text-red-200 rounded" 
                                                  title="No Amount to Refund">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </span>
                                        @endif
                                        
                                        @if(!$payment->booking && !$payment->serviceRequest && !$payment->foodOrder && strpos($payment->payment_reference, 'EXT-') === 0)
                                            <button onclick="deleteExtraCharge({{ $payment->id }}, this)" 
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors" 
                                                    title="Delete Extra Charge">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-50 mb-2">No payments found</h3>
                    <p class="text-gray-400">This customer has no payment transactions yet.</p>
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
                <input type="hidden" name="payment_id" id="refund_payment_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Refund Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400">₱</span>
                        <input type="number" 
                               name="refund_amount" 
                               id="refund_amount"
                               step="0.01" 
                               min="0"
                               class="w-full pl-8 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">
                        Maximum refundable: <span id="max_refund_amount" class="text-green-400 font-semibold"></span>
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Reason (Optional)</label>
                    <textarea name="refund_reason" 
                              rows="3"
                              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Enter reason for refund..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeRefundModal()"
                            class="flex-1 px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePaymentStatus(paymentId, status) {
    if (!confirm(`Are you sure you want to update this payment status to "${status}"?`)) {
        return;
    }

    fetch(`/admin/payments/${paymentId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Payment status updated successfully!');
            location.reload();
        } else {
            alert('Error updating payment status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = error.message || 'An error occurred while updating the payment status.';
        alert(errorMessage);
    });
}

function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refund_payment_id').value = paymentId;
    document.getElementById('refund_amount').max = maxAmount;
    document.getElementById('max_refund_amount').textContent = '₱' + parseFloat(maxAmount).toFixed(2);
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundForm').reset();
}

function deleteExtraCharge(paymentId, button) {
    if (!confirm('Are you sure you want to delete this extra charge? This action cannot be undone and it will also disappear from the Generate Invoice table.')) {
        return;
    }
    
    const row = button.closest('tr');
    const originalButton = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const url = `{{ route('admin.payments.extraCharge.delete', ':id') }}`.replace(':id', paymentId);
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        return response.text().then(text => {
            console.log('Response status:', response.status);
            console.log('Response text:', text);
            
            try {
                const data = JSON.parse(text);
                
                if (!response.ok) {
                    const errorMsg = data.message || data.error || `Server error: ${response.status}`;
                    throw new Error(errorMsg);
                }
                
                return data;
            } catch (parseError) {
                if (!response.ok) {
                    throw new Error(`Server error (${response.status}): ${text.substring(0, 100)}`);
                }
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            // Remove the row from the table with animation
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                
                // Reload page to update summary cards and totals
                location.reload();
            }, 300);
        } else {
            throw new Error(data.message || 'Failed to delete extra charge');
        }
    })
    .catch(error => {
        console.error('Error details:', error);
        alert('Error: ' + (error.message || 'An unexpected error occurred. Please try again.'));
        button.disabled = false;
        button.innerHTML = originalButton;
    });
}
</script>
@endsection

