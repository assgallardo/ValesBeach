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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-green-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['total_payments'], 2) }}</p>
                        <p class="text-xs text-green-400">Completed payments</p>
                    </div>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-yellow-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Pending Payments</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['pending_payments'], 2) }}</p>
                        <p class="text-xs text-yellow-400">{{ $stats['pending_count'] }} transactions</p>
                    </div>
                </div>
            </div>

            <!-- Booking Payments -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-blue-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bed text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Room Bookings</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['booking_payments'], 2) }}</p>
                        <p class="text-xs text-blue-400">{{ $stats['booking_count'] }} payments</p>
                    </div>
                </div>
            </div>

            <!-- Service Payments -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-purple-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-concierge-bell text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Services</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['service_payments'], 2) }}</p>
                        <p class="text-xs text-purple-400">{{ $stats['service_count'] }} payments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Refunds -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-red-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-undo text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Refunds</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['total_refunds'], 2) }}</p>
                        <p class="text-xs text-red-400">{{ $stats['refunded_count'] }} refunded</p>
                    </div>
                </div>
            </div>

            <!-- Refundable Payments -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-indigo-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-coins text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Refundable</p>
                        <p class="text-2xl font-bold text-green-50">{{ $stats['refundable_payments'] }}</p>
                        <p class="text-xs text-indigo-400">Can be refunded</p>
                    </div>
                </div>
            </div>

            <!-- Failed Payments -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-gray-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-times-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Failed</p>
                        <p class="text-2xl font-bold text-green-50">₱{{ number_format($stats['failed_payments'], 2) }}</p>
                        <p class="text-xs text-gray-400">Failed payments</p>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-teal-500 transition-colors">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-teal-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Transactions</p>
                        <p class="text-2xl font-bold text-green-50">{{ number_format($stats['total_count']) }}</p>
                        <p class="text-xs text-teal-400">All time</p>
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
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type & Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($payments as $payment)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Guest Info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-green-50">{{ $payment->user->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-400">{{ $payment->user->email ?? 'N/A' }}</div>
                                            @if($payment->user->phone)
                                                <div class="text-xs text-gray-500">{{ $payment->user->phone }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Payment Details -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-blue-400">{{ $payment->payment_reference }}</div>
                                    @if($payment->transaction_id)
                                        <div class="text-sm text-gray-400">TXN: {{ $payment->transaction_id }}</div>
                                    @endif
                                    @if($payment->notes)
                                        <div class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-sticky-note mr-1"></i>{{ Str::limit($payment->notes, 30) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Type & Service -->
                                <td class="px-6 py-4">
                                    @if($payment->booking)
                                        <div class="flex items-center mb-2">
                                            <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center mr-2">
                                                <i class="fas fa-bed text-white text-xs"></i>
                                            </div>
                                            <span class="text-xs bg-blue-600 text-white px-2 py-1 rounded">Room Booking</span>
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-green-50">{{ $payment->booking->room->name ?? 'N/A' }}</div>
                                            <div class="text-gray-400">{{ $payment->booking->booking_reference }}</div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $payment->booking->check_in ? $payment->booking->check_in->format('M d') : 'N/A' }} - 
                                                {{ $payment->booking->check_out ? $payment->booking->check_out->format('M d, Y') : 'N/A' }}
                                            </div>
                                        </div>
                                    @elseif($payment->serviceRequest)
                                        <div class="flex items-center mb-2">
                                            <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center mr-2">
                                                <i class="fas fa-concierge-bell text-white text-xs"></i>
                                            </div>
                                            <span class="text-xs bg-purple-600 text-white px-2 py-1 rounded">Service</span>
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-green-50">{{ $payment->serviceRequest->service->name ?? 'Service Request' }}</div>
                                            <div class="text-gray-400">{{ $payment->serviceRequest->service->category ?? 'N/A' }}</div>
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-gray-600 rounded flex items-center justify-center mr-2">
                                                <i class="fas fa-question-circle text-white text-xs"></i>
                                            </div>
                                            <span class="text-xs bg-gray-600 text-white px-2 py-1 rounded">Other</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-green-400">
                                        ₱{{ number_format($payment->calculated_amount, 2) }}
                                    </div>
                                    
                                    <!-- Show breakdown for bookings -->
                                    @if($payment->booking)
                                        <div class="text-xs text-gray-400 mt-1">
                                            @if($payment->booking->room)
                                                @php
                                                    $checkIn = \Carbon\Carbon::parse($payment->booking->check_in_date);
                                                    $checkOut = \Carbon\Carbon::parse($payment->booking->check_out_date);
                                                    $nights = $checkIn->diffInDays($checkOut);
                                                    $roomCost = $payment->booking->room->price * $nights;
                                                @endphp
                                                Room: ₱{{ number_format($payment->booking->room->price, 2) }} × {{ $nights }} nights
                                                = ₱{{ number_format($roomCost, 2) }}
                                            @endif
                                        </div>
                                        @if($payment->booking->additional_fees > 0)
                                            <div class="text-xs text-blue-400">
                                                + ₱{{ number_format($payment->booking->additional_fees, 2) }} fees
                                            </div>
                                        @endif
                                        @if($payment->booking->discount_amount > 0)
                                            <div class="text-xs text-yellow-400">
                                                - ₱{{ number_format($payment->booking->discount_amount, 2) }} discount
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <!-- Show breakdown for services -->
                                    @if($payment->serviceRequest && $payment->serviceRequest->service)
                                        @php
                                            $service = $payment->serviceRequest->service;
                                            $quantity = $payment->serviceRequest->quantity ?? 1;
                                            $serviceTotal = $service->price * $quantity;
                                        @endphp
                                        <div class="text-xs text-gray-400 mt-1">
                                            Service: {{ $service->name }}
                                        </div>
                                        <div class="text-xs text-blue-400">
                                            ₱{{ number_format($service->price, 2) }}
                                            @if($quantity > 1)
                                                × {{ $quantity }} = ₱{{ number_format($serviceTotal, 2) }}
                                            @endif
                                        </div>
                                        @if($payment->serviceRequest->service->duration)
                                            <div class="text-xs text-gray-500">
                                                Duration: {{ $payment->serviceRequest->service->duration }} min
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <!-- Show refund information -->
                                    @if($payment->refund_amount > 0)
                                        <div class="text-sm text-red-400 mt-1">
                                            <i class="fas fa-minus-circle mr-1"></i>Refunded: ₱{{ number_format($payment->refund_amount, 2) }}
                                        </div>
                                        <div class="text-sm font-medium text-green-400">
                                            Net: ₱{{ number_format($payment->calculated_amount - ($payment->refund_amount ?? 0), 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Method -->
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded border border-gray-600">
                                        {{ $payment->payment_method_display }}
                                    </span>
                                    @if($payment->payment_date)
                                        <div class="text-sm text-gray-400 mt-1">
                                            {{ $payment->payment_date->format('M d, H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = match($payment->status) {
                                            'completed' => ['bg' => 'bg-green-600', 'text' => 'text-white'],
                                            'pending' => ['bg' => 'bg-yellow-600', 'text' => 'text-white'],
                                            'processing' => ['bg' => 'bg-blue-600', 'text' => 'text-white'],
                                            'failed' => ['bg' => 'bg-red-600', 'text' => 'text-white'],
                                            'refunded' => ['bg' => 'bg-gray-600', 'text' => 'text-white'],
                                            default => ['bg' => 'bg-gray-700', 'text' => 'text-gray-300']
                                        };
                                    @endphp
                                    <span class="inline-block px-2 py-1 text-xs {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} rounded">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    @if($payment->isPartiallyRefunded())
                                        <div class="mt-1">
                                            <span class="inline-block px-2 py-1 text-xs bg-yellow-600 text-white rounded">Partial Refund</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-green-50">{{ $payment->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $payment->created_at->format('H:i A') }}</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <!-- View Details - Works for both bookings and services -->
                                        <a href="{{ route('admin.payments.show', $payment) }}" 
                                           class="inline-flex items-center px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" 
                                           title="View Payment Details">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        
                                        <!-- Refund Action - Works for both bookings and services -->
                                        @if($payment->canBeRefunded())
                                            <button onclick="showRefundModal({{ $payment->id }}, {{ $payment->getRemainingRefundableAmount() }})"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors" 
                                                    title="Process Refund">
                                                <i class="fas fa-undo mr-1"></i>Refund
                                            </button>
                                        @endif

                                        <!-- Mark as Complete - Works for both bookings and services -->
                                        @if($payment->status === 'pending')
                                            <button onclick="updatePaymentStatus({{ $payment->id }}, 'completed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors" 
                                                    title="Mark as Completed">
                                                <i class="fas fa-check mr-1"></i>Complete
                                            </button>
                                        @endif

                                        <!-- View Related Record - Booking or Service -->
                                        @if($payment->booking)
                                            <a href="{{ route('admin.bookings.show', $payment->booking) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors" 
                                               title="View Booking Details">
                                                <i class="fas fa-bed mr-1"></i>Booking
                                            </a>
                                        @elseif($payment->serviceRequest)
                                            <a href="{{ route('admin.service-requests.show', $payment->serviceRequest) }}" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors" 
                                               title="View Service Request Details">
                                                <i class="fas fa-concierge-bell mr-1"></i>Service
                                            </a>
                                        @endif

                                        <!-- Additional Actions for Processing Status -->
                                        @if($payment->status === 'processing')
                                            <button onclick="updatePaymentStatus({{ $payment->id }}, 'completed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors" 
                                                    title="Complete Payment">
                                                <i class="fas fa-check-circle mr-1"></i>Complete
                                            </button>
                                            <button onclick="updatePaymentStatus({{ $payment->id }}, 'failed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors" 
                                                    title="Mark as Failed">
                                                <i class="fas fa-times-circle mr-1"></i>Failed
                                            </button>
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
    if (confirm('Are you sure you want to update this payment status?')) {
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
</script>
@endsection
