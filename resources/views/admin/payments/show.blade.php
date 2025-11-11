@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.payments.index') }}" 
                                   class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-green-400">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Payment Management
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-600 mx-2"></i>
                                    <span class="text-sm font-medium text-gray-300">{{ $payment->payment_reference }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-green-100">Payment Details</h1>
                    <p class="text-gray-400 mt-1">View and manage payment information</p>
                </div>
                
                <div class="flex space-x-3">
                    @if($payment->canBeRefunded())
                        <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->getRemainingRefundableAmount() }})"
                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Process Refund
                        </button>
                    @endif
                    <a href="{{ route('admin.payments.index') }}" 
                       class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Information Card -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-green-100">Payment Information</h3>
                            @php
                                $statusColors = [
                                    'completed' => 'bg-green-600 text-green-100',
                                    'pending' => 'bg-yellow-600 text-yellow-100',
                                    'processing' => 'bg-blue-600 text-blue-100',
                                    'failed' => 'bg-red-600 text-red-100',
                                    'refunded' => 'bg-red-600 text-red-100',
                                    'partially_refunded' => 'bg-yellow-600 text-yellow-100'
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$payment->status] ?? 'bg-gray-600 text-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Reference</label>
                                <div class="bg-gray-900 px-3 py-2 rounded border border-gray-600">
                                    <code class="text-green-400 font-mono">{{ $payment->payment_reference }}</code>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Date</label>
                                <div class="text-gray-300">
                                    {{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not processed yet' }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Amount</label>
                                <div class="text-3xl font-bold text-green-400">
                                    ₱{{ number_format($payment->calculated_amount ?? $payment->amount, 2) }}
                                </div>
                                
                                <!-- Amount breakdown -->
                                @if($payment->serviceRequest && $payment->serviceRequest->service)
                                    @php
                                        $service = $payment->serviceRequest->service;
                                        $quantity = $payment->serviceRequest->quantity ?? 1;
                                    @endphp
                                    <div class="text-sm text-gray-400 mt-1">
                                        Service: {{ $service->name }}
                                        @if($quantity > 1)
                                            (₱{{ number_format($service->price, 2) }} × {{ $quantity }})
                                        @endif
                                    </div>
                                @elseif($payment->booking && $payment->booking->room)
                                    @php
                                        $checkIn = \Carbon\Carbon::parse($payment->booking->check_in_date);
                                        $checkOut = \Carbon\Carbon::parse($payment->booking->check_out_date);
                                        $nights = $checkIn->diffInDays($checkOut);
                                    @endphp
                                    <div class="text-sm text-gray-400 mt-1">
                                        Room: {{ $payment->booking->room->name }} ({{ $nights }} nights)
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Method</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-700 text-gray-300">
                                    {{ $payment->payment_method_display ?? ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </div>
                            
                            @if($payment->transaction_id)
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Transaction ID</label>
                                    <div class="bg-gray-900 px-3 py-2 rounded border border-gray-600">
                                        <code class="text-green-400 font-mono">{{ $payment->transaction_id }}</code>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Refund Information -->
                        @if($payment->refund_amount > 0)
                            <div class="mt-6 p-4 bg-yellow-900/30 border border-yellow-600/30 rounded-lg">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                                    <h4 class="text-lg font-semibold text-yellow-100">Refund Information</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-400">Refund Amount:</span>
                                        <div class="text-lg font-semibold text-red-400">₱{{ number_format($payment->refund_amount, 2) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-400">Refunded Date:</span>
                                        <div class="text-gray-300">{{ $payment->refunded_at ? $payment->refunded_at->format('M d, Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                                @if($payment->refundedBy)
                                    <div class="mt-3">
                                        <span class="text-sm text-gray-400">Processed by:</span>
                                        <span class="text-gray-300">{{ $payment->refundedBy->name }}</span>
                                    </div>
                                @endif
                                @if($payment->refund_reason)
                                    <div class="mt-3">
                                        <span class="text-sm text-gray-400">Reason:</span>
                                        <div class="text-gray-300 mt-1">{{ $payment->refund_reason }}</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        @if($payment->notes)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-400 mb-2">Notes</label>
                                <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                    <div class="text-gray-300">{!! nl2br(e($payment->notes)) !!}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Booking Information -->
                @if($payment->booking)
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Related Booking</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Booking Reference</label>
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        {{ $payment->booking->booking_reference }}
                                    </a>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Room</label>
                                    <div class="text-gray-300">
                                        @if($payment->booking->room)
                                            {{ $payment->booking->room->name }} ({{ $payment->booking->room->room_number }})
                                        @else
                                            Not assigned
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Check-in Date</label>
                                    <div class="text-gray-300">{{ \Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Check-out Date</label>
                                    <div class="text-gray-300">{{ \Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Total Amount</label>
                                    <div class="text-xl font-semibold text-green-400">₱{{ number_format($payment->booking->total_amount, 2) }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Booking Status</label>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $payment->booking->status === 'confirmed' ? 'bg-green-600 text-green-100' : 'bg-yellow-600 text-yellow-100' }}">
                                        {{ ucfirst($payment->booking->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Related Service Request -->
                @if($payment->serviceRequest)
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Related Service Request</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Service Request ID</label>
                                    <a href="{{ route('admin.service-requests.show', $payment->serviceRequest) }}" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        #{{ $payment->serviceRequest->id }}
                                    </a>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-blue-100">
                                        {{ ucfirst($payment->serviceRequest->status) }}
                                    </span>
                                </div>
                                
                                @if($payment->serviceRequest->service)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Name</label>
                                        <div class="text-gray-300">{{ $payment->serviceRequest->service->name }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Category</label>
                                        <div class="text-gray-300">{{ $payment->serviceRequest->service->category ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Price</label>
                                        <div class="text-xl font-semibold text-green-400">₱{{ number_format($payment->serviceRequest->service->price, 2) }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Quantity</label>
                                        <div class="text-gray-300">{{ $payment->serviceRequest->quantity ?? 1 }}</div>
                                    </div>
                                    
                                    @if($payment->serviceRequest->service->duration)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Duration</label>
                                            <div class="text-gray-300">{{ $payment->serviceRequest->service->duration }} minutes</div>
                                        </div>
                                    @endif
                                @endif
                                
                                @if($payment->serviceRequest->scheduled_date)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Scheduled Date</label>
                                        <div class="text-gray-300">{{ \Carbon\Carbon::parse($payment->serviceRequest->scheduled_date)->format('M d, Y h:i A') }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            @if($payment->serviceRequest->description)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                                    <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                        <div class="text-gray-300">{{ $payment->serviceRequest->description }}</div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($payment->serviceRequest->special_requests)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Special Requests</label>
                                    <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                        <div class="text-gray-300">{{ $payment->serviceRequest->special_requests }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Customer Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user text-2xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-green-100">{{ $payment->user->name }}</h4>
                            <p class="text-gray-400">{{ $payment->user->email }}</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">User Role</label>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-blue-100">
                                    {{ ucfirst($payment->user->role) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Member Since</label>
                                <div class="text-gray-300">{{ $payment->user->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">

                        @if($payment->canBeRefunded())
                            <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->getRemainingRefundableAmount() }})"
                                    class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                <i class="fas fa-undo mr-2"></i>Process Refund
                            </button>
                        @endif
                        @if($payment->refund_amount > 0)
                            <form method="POST" action="{{ route('admin.payments.cancelRefund', $payment) }}" class="w-full mt-2">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200" onclick="return confirm('Are you sure you want to cancel this refund and restore the original payment amount?')">
                                    <i class="fas fa-undo-alt mr-2"></i>Cancel Refund
                                </button>
                            </form>
                        @endif

                        @if($payment->status === 'pending')
                            <form method="POST" action="{{ route('admin.payments.updateStatus', $payment) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" 
                                        onclick="return confirm('Mark this payment as completed?')"
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fas fa-check mr-2"></i>Mark Completed
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.payments.updateStatus', $payment) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="failed">
                                <button type="submit" 
                                        onclick="return confirm('Mark this payment as failed?')"
                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>Mark Failed
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('admin.payments.index') }}" 
                           class="w-full bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 inline-flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                        </a>
                    </div>
                </div>

                <!-- Payment Timeline -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Payment Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-plus text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-green-100">Payment Created</div>
                                    <div class="text-xs text-gray-400">{{ $payment->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>

                            @if($payment->payment_date)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-green-100">Payment Processed</div>
                                        <div class="text-xs text-gray-400">{{ $payment->payment_date->format('M d, Y h:i A') }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($payment->refunded_at)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-undo text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-green-100">Refund Processed</div>
                                        <div class="text-xs text-gray-400">{{ $payment->refunded_at->format('M d, Y h:i A') }}</div>
                                        @if($payment->refundedBy)
                                            <div class="text-xs text-gray-500">By: {{ $payment->refundedBy->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg border border-gray-700 w-full max-w-md">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-green-100">Process Refund</h3>
                    <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="refundForm" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Refund Amount</label>
                        <input type="number" name="refund_amount" step="0.01" required
                               class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Maximum refundable: <span id="maxRefund"></span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Refund Reason</label>
                        <textarea name="refund_reason" rows="3" required
                                  placeholder="Please provide a reason for the refund..."
                                  class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500"></textarea>
                    </div>
                    
                    <div class="p-3 bg-yellow-900/30 border border-yellow-600/30 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <div class="text-sm text-yellow-100">
                                <strong>Warning:</strong> This action cannot be undone. Please ensure the refund amount and reason are correct.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-750 px-6 py-4 border-t border-gray-700 flex justify-end space-x-3">
                    <button type="button" onclick="closeRefundModal()"
                            class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show refund modal function
function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('maxRefund').textContent = `₱${maxAmount.toFixed(2)}`;
    document.querySelector('input[name="refund_amount"]').max = maxAmount;
    document.getElementById('refundModal').classList.remove('hidden');
}

// Close refund modal function
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
</script>
@endsection
