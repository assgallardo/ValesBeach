@extends('layouts.manager')

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
                                <a href="{{ route('manager.payments.index') }}" 
                                   class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-green-400">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Payment Tracking
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
                    <a href="{{ route('manager.payments.index') }}" 
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
                                        $checkIn = $payment->booking->check_in_date ?? $payment->booking->check_in;
                                        $checkOut = $payment->booking->check_out_date ?? $payment->booking->check_out;
                                        
                                        if ($checkIn && $checkOut) {
                                            $checkIn = \Carbon\Carbon::parse($checkIn)->startOfDay();
                                            $checkOut = \Carbon\Carbon::parse($checkOut)->startOfDay();
                                            $nights = $checkIn->diffInDays($checkOut);
                                            
                                            // Same-day bookings count as 1 night/day
                                            if ($nights === 0) {
                                                $nights = 1;
                                            }
                                        } else {
                                            $nights = 1;
                                        }
                                    @endphp
                                    <div class="text-sm text-gray-400 mt-1">
                                        Room: {{ $payment->booking->room->name }} ({{ $nights }} night{{ $nights > 1 ? 's' : '' }})
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
                                    <a href="{{ route('manager.bookings.show', $payment->booking) }}" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        {{ $payment->booking->booking_reference }}
                                    </a>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Room</label>
                                    <div class="text-gray-300">
                                        @if($payment->booking->room)
                                            {{ $payment->booking->room->name }} - Room {{ $payment->booking->room->room_number }}
                                        @else
                                            Not assigned yet
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
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Booking Total</label>
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
                                    <a href="{{ route('manager.service-requests.show', $payment->serviceRequest) }}" 
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

                <!-- Related Food Order -->
                @if($payment->foodOrder)
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Related Food Order</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Order ID</label>
                                    <div class="text-green-400 font-medium">#{{ $payment->foodOrder->id }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium
                                        {{ $payment->foodOrder->status === 'pending' ? 'bg-yellow-600 text-yellow-100' : '' }}
                                        {{ $payment->foodOrder->status === 'preparing' ? 'bg-blue-600 text-blue-100' : '' }}
                                        {{ $payment->foodOrder->status === 'ready' ? 'bg-green-600 text-green-100' : '' }}
                                        {{ $payment->foodOrder->status === 'delivered' ? 'bg-purple-600 text-purple-100' : '' }}
                                        {{ $payment->foodOrder->status === 'cancelled' ? 'bg-red-600 text-red-100' : '' }}
                                    ">
                                        {{ ucfirst($payment->foodOrder->status) }}
                                    </span>
                                </div>
                                
                                @if($payment->foodOrder->orderItems && $payment->foodOrder->orderItems->count() > 0)
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Order Items</label>
                                        <div class="bg-gray-900 rounded border border-gray-600">
                                            <table class="w-full">
                                                <thead class="bg-gray-800">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Item</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Quantity</th>
                                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-400">Price</th>
                                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-400">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-700">
                                                    @foreach($payment->foodOrder->orderItems as $item)
                                                        <tr>
                                                            <td class="px-4 py-3 text-gray-300">{{ $item->menuItem->name ?? 'N/A' }}</td>
                                                            <td class="px-4 py-3 text-gray-300">{{ $item->quantity }}</td>
                                                            <td class="px-4 py-3 text-right text-gray-300">₱{{ number_format($item->unit_price, 2) }}</td>
                                                            <td class="px-4 py-3 text-right text-green-400 font-semibold">₱{{ number_format($item->total_price, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Order Total</label>
                                    <div class="text-xl font-semibold text-green-400">₱{{ number_format($payment->foodOrder->total_amount ?? $payment->amount, 2) }}</div>
                                </div>
                                
                                @if($payment->foodOrder->delivery_address)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Delivery Address</label>
                                        <div class="text-gray-300">{{ $payment->foodOrder->delivery_address }}</div>
                                    </div>
                                @endif
                                
                                @if($payment->foodOrder->created_at)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Order Date</label>
                                        <div class="text-gray-300">{{ $payment->foodOrder->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                @endif
                            </div>
                            
                            @if($payment->foodOrder->special_instructions)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Special Instructions</label>
                                    <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                        <div class="text-gray-300">{{ $payment->foodOrder->special_instructions }}</div>
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
                                <label class="block text-sm font-medium text-gray-400 mb-1">Customer Role</label>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-blue-100">
                                    {{ ucfirst($payment->user->role) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Member Since</label>
                                <div class="text-gray-300">{{ $payment->user->created_at->format('M d, Y') }}</div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Total Payments</label>
                                <div class="text-gray-300">{{ $payment->user->payments->count() }} payments</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Actions -->
                @if(in_array($payment->status, ['pending', 'processing']))
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Update Payment Status</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('manager.payments.updateStatus', $payment) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                                    <select name="status" id="status" required
                                            class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                                        <option value="">Select Status</option>
                                        <option value="processing" {{ $payment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed">Completed</option>
                                        @if(auth()->user()->role === 'admin')
                                            <option value="failed">Failed</option>
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label for="transaction_id" class="block text-sm font-medium text-gray-400 mb-2">Transaction ID (Optional)</label>
                                    <input type="text" name="transaction_id" id="transaction_id" 
                                           value="{{ $payment->transaction_id }}"
                                           placeholder="Enter transaction ID from payment gateway"
                                           class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-400 mb-2">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              placeholder="Add any additional notes about this status update"
                                              class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">{{ $payment->notes }}</textarea>
                                </div>

                                <button type="submit" 
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fas fa-save mr-2"></i>Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

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
                                    <div class="text-xs text-gray-500">By: {{ $payment->user->name }}</div>
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
    document.getElementById('refundForm').action = `/manager/payments/${paymentId}/refund`;
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
