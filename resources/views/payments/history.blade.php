@extends('layouts.guest')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-green-50">Payment History</h1>
            <p class="text-gray-400 mt-2">View all your payment transactions</p>
        </div>

        <!-- Quick Actions Section -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('invoices.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-invoice mr-2"></i>
                    View All Invoices
                </a>
                <a href="{{ route('guest.bookings') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-calendar mr-2"></i>
                    My Bookings
                </a>
                <a href="{{ route('guest.services.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-concierge-bell mr-2"></i>
                    Services
                </a>
            </div>
        </div>

        @if($payments->isEmpty())
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Payments Yet</h3>
                <p class="text-gray-400 mb-6">You haven't made any payments yet. Make a booking to get started!</p>
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('guest.rooms.browse') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-bed mr-2"></i>
                        Browse Rooms
                    </a>
                    <a href="{{ route('guest.services.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-concierge-bell mr-2"></i>
                        Browse Services
                    </a>
                    <a href="{{ route('invoices.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-file-invoice mr-2"></i>
                        View Invoices
                    </a>
                </div>
            </div>
        @else
            <!-- Payments Grid -->
            <div class="space-y-6">
                @foreach($payments as $payment)
                <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Payment Info -->
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <!-- Payment Type Icon -->
                                <div class="mr-3">
                                    @if($payment->booking_id)
                                        <i class="fas fa-bed text-green-400 text-lg"></i>
                                    @elseif($payment->service_request_id)
                                        <i class="fas fa-concierge-bell text-blue-400 text-lg"></i>
                                    @else
                                        <i class="fas fa-receipt text-gray-400 text-lg"></i>
                                    @endif
                                </div>

                                <h3 class="text-lg font-semibold text-green-50 mr-3">
                                    @if(isset($payment->formatted_amount))
                                        {{ $payment->formatted_amount }}
                                    @else
                                        ₱{{ number_format($payment->amount, 2) }}
                                    @endif
                                </h3>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                    {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 
                                       ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 
                                       ($payment->status === 'failed' ? 'bg-red-500 text-white' : 
                                       ($payment->status === 'refunded' ? 'bg-purple-500 text-white' : 'bg-gray-500 text-white'))) }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-400 block">Payment Reference</span>
                                    <span class="text-green-50 font-medium">{{ $payment->payment_reference ?? 'N/A' }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Payment Method</span>
                                    <span class="text-green-50">
                                        @if(isset($payment->payment_method_display))
                                            {{ $payment->payment_method_display }}
                                        @else
                                            {{ ucfirst($payment->payment_method ?? 'Unknown') }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Reference</span>
                                    <span class="text-green-50">
                                        @if($payment->booking && $payment->booking->booking_reference)
                                            {{ $payment->booking->booking_reference }}
                                        @elseif($payment->serviceRequest)
                                            SR-{{ str_pad($payment->serviceRequest->id, 6, '0', STR_PAD_LEFT) }}
                                        @elseif($payment->service_request_id)
                                            SR-{{ str_pad($payment->service_request_id, 6, '0', STR_PAD_LEFT) }}
                                        @else
                                            {{ $payment->payment_reference ?? 'N/A' }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Date</span>
                                    <span class="text-green-50">
                                        @if($payment->created_at instanceof \Carbon\Carbon)
                                            {{ $payment->created_at->format('M d, Y') }}
                                        @elseif(is_string($payment->created_at))
                                            {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}
                                        @else
                                            {{ date('M d, Y', strtotime($payment->created_at)) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Item Details -->
                            @if($payment->booking && $payment->booking->room)
                                <!-- Room Booking Details -->
                                <div class="mt-3 pt-3 border-t border-gray-600">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-bed text-green-400 mr-2"></i>
                                        <span class="text-gray-400 mr-2">Room:</span>
                                        <span class="text-green-50 font-medium mr-4">{{ $payment->booking->room->name }}</span>
                                        
                                        <i class="fas fa-calendar text-green-400 mr-2"></i>
                                        <span class="text-gray-400 mr-2">Stay:</span>
                                        <span class="text-green-50">
                                            @if($payment->booking->check_in instanceof \Carbon\Carbon)
                                                {{ $payment->booking->check_in->format('M d') }} - 
                                                {{ $payment->booking->check_out->format('M d, Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($payment->booking->check_in)->format('M d') }} - 
                                                {{ \Carbon\Carbon::parse($payment->booking->check_out)->format('M d, Y') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @elseif($payment->serviceRequest)
                                <!-- Service Request Details -->
                                <div class="mt-3 pt-3 border-t border-gray-600">
                                    <div class="flex items-center text-sm flex-wrap">
                                        <i class="fas fa-concierge-bell text-blue-400 mr-2"></i>
                                        <span class="text-gray-400 mr-2">Service:</span>
                                        <span class="text-green-50 font-medium mr-4">
                                            {{ $payment->serviceRequest->service_type ?? 'Service Request' }}
                                        </span>
                                        
                                        @if($payment->serviceRequest->scheduled_date)
                                        <i class="fas fa-clock text-blue-400 mr-2"></i>
                                        <span class="text-gray-400 mr-2">Scheduled:</span>
                                        <span class="text-green-50">
                                            @if($payment->serviceRequest->scheduled_date instanceof \Carbon\Carbon)
                                                {{ $payment->serviceRequest->scheduled_date->format('M d, Y H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($payment->serviceRequest->scheduled_date)->format('M d, Y H:i') }}
                                            @endif
                                        </span>
                                        @endif
                                        
                                        @if($payment->serviceRequest->guests_count)
                                        <i class="fas fa-users text-blue-400 mr-2 ml-4"></i>
                                        <span class="text-gray-400 mr-2">Guests:</span>
                                        <span class="text-green-50">{{ $payment->serviceRequest->guests_count }}</span>
                                        @endif
                                    </div>
                                </div>
                            @elseif($payment->service_request_id)
                                <!-- Service Request Details (without relationship loaded) -->
                                <div class="mt-3 pt-3 border-t border-gray-600">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-concierge-bell text-blue-400 mr-2"></i>
                                        <span class="text-gray-400 mr-2">Type:</span>
                                        <span class="text-green-50">Service Request</span>
                                    </div>
                                </div>
                            @endif
                            
                            @if($payment->notes)
                            <div class="mt-2">
                                <span class="text-gray-400 text-sm">Notes:</span>
                                <p class="text-green-50 text-sm">{{ $payment->notes }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
                            @if(Route::has('payments.show'))
                            <a href="{{ route('payments.show', $payment) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                               title="View Payment Details">
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </a>
                            @endif
                            
                            @if($payment->booking)
                            <a href="{{ route('guest.bookings.show', $payment->booking) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                               title="View Booking">
                                <i class="fas fa-bed mr-2"></i>
                                Booking
                            </a>
                            @elseif($payment->serviceRequest && Route::has('guest.service-requests.show'))
                            <a href="{{ route('guest.service-requests.show', $payment->serviceRequest->id) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                               title="View Service Request">
                                <i class="fas fa-concierge-bell mr-2"></i>
                                Service
                            </a>
                            @endif
                            
                            @if($payment->booking && $payment->booking->invoice && Route::has('invoices.show'))
                            <a href="{{ route('invoices.show', $payment->booking->invoice) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors"
                               title="View Invoice">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Invoice
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
            <div class="mt-8">
                {{ $payments->links() }}
            </div>
            @endif
        @endif

        <!-- Quick Stats with Invoice Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Clickable Invoice Stats Card -->
            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition-colors cursor-pointer" 
                 onclick="window.location.href='{{ route('invoices.index') }}'">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice text-indigo-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">My Invoices</p>
                        <p class="text-lg font-semibold text-green-50">
                            {{ auth()->user()->invoices()->count() ?? 0 }}
                        </p>
                        <p class="text-xs text-indigo-400 mt-1">Click to view all</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-money-bill-wave text-green-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Paid</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-bed text-blue-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Room Bookings</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($payments->whereNotNull('booking_id')->where('status', 'completed')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-concierge-bell text-purple-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Services</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($payments->whereNotNull('service_request_id')->where('status', 'completed')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Back Section with Multiple Options -->
        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('guest.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
            <a href="{{ route('invoices.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-file-invoice mr-2"></i>
                All Invoices
            </a>
        </div>
    </div>
</div>
@endsection