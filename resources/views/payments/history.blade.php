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

        @if($payments->isEmpty())
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Payments Yet</h3>
                <p class="text-gray-400 mb-6">You haven't made any payments yet. Make a booking to get started!</p>
                <a 
                    href="{{ route('guest.rooms') }}" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-search mr-2"></i>
                    Browse Rooms
                </a>
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
                                <h3 class="text-lg font-semibold text-green-50 mr-3">
                                    {{ $payment->formatted_amount }}
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
                                    <span class="text-green-50 font-medium">{{ $payment->payment_reference }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Payment Method</span>
                                    <span class="text-green-50">{{ $payment->payment_method_display }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Booking</span>
                                    <span class="text-green-50">{{ $payment->booking->booking_reference }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Date</span>
                                    <span class="text-green-50">{{ $payment->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            
                            <!-- Room Info -->
                            <div class="mt-3 pt-3 border-t border-gray-600">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-bed text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Room:</span>
                                    <span class="text-green-50 font-medium mr-4">{{ $payment->booking->room->name }}</span>
                                    
                                    <i class="fas fa-calendar text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Stay:</span>
                                    <span class="text-green-50">
                                        {{ $payment->booking->check_in->format('M d') }} - 
                                        {{ $payment->booking->check_out->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($payment->notes)
                            <div class="mt-2">
                                <span class="text-gray-400 text-sm">Notes:</span>
                                <p class="text-green-50 text-sm">{{ $payment->notes }}</p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center space-x-3 mt-4 lg:mt-0">
                            <a 
                                href="{{ route('payments.show', $payment) }}" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                                title="View Payment Details"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                View
                            </a>
                            
                            <a 
                                href="{{ route('guest.bookings.show', $payment->booking) }}" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                                title="View Booking"
                            >
                                <i class="fas fa-bed mr-2"></i>
                                Booking
                            </a>
                            
                            @if($payment->booking->invoice)
                            <a 
                                href="{{ route('invoices.show', $payment->booking->invoice) }}" 
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors"
                                title="View Invoice"
                            >
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

        <!-- Quick Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
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
                        <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Pending</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-receipt text-blue-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Transactions</p>
                        <p class="text-lg font-semibold text-green-50">{{ $payments->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar text-purple-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">This Month</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($payments->where('created_at', '>=', now()->startOfMonth())->where('status', 'completed')->sum('amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-8 text-center">
            <a 
                href="{{ route('guest.dashboard') }}" 
                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
