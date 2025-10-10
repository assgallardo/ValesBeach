@extends('layouts.guest')

@section('title', 'Payment Details')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-green-50">Payment Details</h1>
                    <p class="text-gray-400 mt-2">Payment Reference: {{ $payment->payment_reference }}</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-medium
                        {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 
                           ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 
                           ($payment->status === 'failed' ? 'bg-red-500 text-white' : 
                           ($payment->status === 'refunded' ? 'bg-purple-500 text-white' : 'bg-gray-500 text-white'))) }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Information -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Information</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Amount:</span>
                        <span class="text-green-400 font-bold text-xl">{{ $payment->formatted_amount }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Payment Method:</span>
                        <span class="text-green-50 font-medium">{{ $payment->payment_method_display }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Payment Date:</span>
                        <span class="text-green-50">
                            @if($payment->payment_date)
                                {{ $payment->payment_date->format('M d, Y - g:i A') }}
                            @else
                                <span class="text-gray-500">Pending</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Created:</span>
                        <span class="text-green-50">{{ $payment->created_at->format('M d, Y - g:i A') }}</span>
                    </div>
                    
                    @if($payment->transaction_id)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Transaction ID:</span>
                        <span class="text-green-50 font-mono text-sm">{{ $payment->transaction_id }}</span>
                    </div>
                    @endif
                    
                    @if($payment->notes)
                    <div class="pt-4 border-t border-gray-600">
                        <span class="text-gray-400 block mb-2">Notes:</span>
                        <p class="text-green-50 bg-gray-700 p-3 rounded-lg">{{ $payment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Booking Information -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-green-50 mb-6">Booking Information</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Booking Reference:</span>
                        <span class="text-green-50 font-medium">{{ $payment->booking->booking_reference }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Room:</span>
                        <span class="text-green-50">{{ $payment->booking->room->name }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Check-in:</span>
                        <span class="text-green-50">{{ $payment->booking->check_in->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Check-out:</span>
                        <span class="text-green-50">{{ $payment->booking->check_out->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Nights:</span>
                        <span class="text-green-50">{{ $payment->booking->check_in->diffInDays($payment->booking->check_out) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Guests:</span>
                        <span class="text-green-50">{{ $payment->booking->guests }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Booking Status:</span>
                        <span class="inline-block px-2 py-1 rounded text-xs
                            {{ $payment->booking->status === 'confirmed' ? 'bg-green-500 text-white' : 
                               ($payment->booking->status === 'pending' ? 'bg-yellow-500 text-black' : 
                               ($payment->booking->status === 'cancelled' ? 'bg-red-500 text-white' : 'bg-gray-500 text-white')) }}">
                            {{ ucfirst(str_replace('_', ' ', $payment->booking->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="mt-8 bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Summary</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-50 mb-1">{{ $payment->booking->formatted_total_price }}</div>
                    <div class="text-sm text-gray-400">Total Booking Amount</div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">{{ $payment->booking->formatted_total_paid }}</div>
                    <div class="text-sm text-gray-400">Total Paid</div>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold {{ $payment->booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400' }} mb-1">
                        {{ $payment->booking->formatted_remaining_balance }}
                    </div>
                    <div class="text-sm text-gray-400">Remaining Balance</div>
                </div>
            </div>
            
            @if($payment->booking->remaining_balance > 0)
            <div class="mt-6 text-center">
                <a 
                    href="{{ route('payments.create', $payment->booking) }}" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-credit-card mr-2"></i>
                    Make Another Payment
                </a>
            </div>
            @endif
        </div>

        <!-- Payment Timeline -->
        <div class="mt-8 bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Timeline</h2>
            
            <div class="space-y-4">
                @foreach($payment->booking->payments->sortBy('created_at') as $bookingPayment)
                <div class="flex items-center {{ $bookingPayment->id === $payment->id ? 'bg-green-900/20 p-3 rounded-lg' : '' }}">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-4
                        {{ $bookingPayment->status === 'completed' ? 'bg-green-500' : 
                           ($bookingPayment->status === 'pending' ? 'bg-yellow-500' : 
                           ($bookingPayment->status === 'failed' ? 'bg-red-500' : 'bg-gray-500')) }}">
                        <i class="fas {{ $bookingPayment->status === 'completed' ? 'fa-check' : 
                                       ($bookingPayment->status === 'pending' ? 'fa-clock' : 
                                       ($bookingPayment->status === 'failed' ? 'fa-times' : 'fa-question')) }} text-white text-sm"></i>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-green-50 font-medium">{{ $bookingPayment->formatted_amount }}</span>
                                <span class="text-gray-400 ml-2">via {{ $bookingPayment->payment_method_display }}</span>
                                @if($bookingPayment->id === $payment->id)
                                    <span class="ml-2 text-xs bg-green-600 text-white px-2 py-1 rounded">Current</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-400">
                                {{ $bookingPayment->created_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">{{ $bookingPayment->payment_reference }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a 
                href="{{ route('guest.bookings.show', $payment->booking) }}" 
                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-green-700 transition-colors"
            >
                <i class="fas fa-bed mr-2"></i>
                View Booking
            </a>
            
            @if($payment->booking->invoice)
            <a 
                href="{{ route('invoices.show', $payment->booking->invoice) }}" 
                class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-purple-700 transition-colors"
            >
                <i class="fas fa-file-invoice mr-2"></i>
                View Invoice
            </a>
            @endif
            
            <a 
                href="{{ route('payments.history') }}" 
                class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 transition-colors"
            >
                <i class="fas fa-history mr-2"></i>
                Payment History
            </a>
            
            <button 
                onclick="window.print()" 
                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors"
            >
                <i class="fas fa-print mr-2"></i>
                Print Receipt
            </button>
        </div>
    </div>
</div>
@endsection
