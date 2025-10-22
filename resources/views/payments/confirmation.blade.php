
@extends('layouts.guest')

@section('title', 'Payment Confirmation')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-4">
                <i class="fas fa-check text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">
                Payment {{ $payment->status === 'completed' ? 'Completed' : 'Submitted' }}!
            </h1>
            <p class="text-gray-400">
                {{ $payment->status === 'completed' ? 'Your payment has been processed successfully.' : 'Your payment is being processed.' }}
            </p>
        </div>

        <!-- Payment Details -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Details</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Payment Reference:</span>
                    <span class="text-green-50 font-medium">{{ $payment->payment_reference }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Amount:</span>
                    <span class="text-green-400 font-bold text-lg">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Payment Method:</span>
                    <span class="text-green-50">{{ $payment->payment_method_display }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Status:</span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Date:</span>
                    <span class="text-green-50">{{ $payment->created_at->format('M d, Y - g:i A') }}</span>
                </div>
                @if($payment->notes)
                <div class="pt-2 border-t border-gray-600">
                    <span class="text-gray-400 block mb-1">Notes:</span>
                    <span class="text-green-50">{{ $payment->notes }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Booking Information -->
        @if($payment->booking)
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
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
                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">
                    <span class="text-green-300">Total Booking Amount:</span>
                    <span class="text-green-50 font-bold">₱{{ number_format($payment->booking->total_price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-green-300">Total Amount Paid:</span>
                    <span class="text-green-400 font-bold">₱{{ number_format($payment->booking->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">
                    <span class="text-green-100 font-semibold text-lg">Remaining Balance:</span>
                    <span class="font-bold text-2xl {{ $payment->booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400' }}">
                        ₱{{ number_format($payment->booking->remaining_balance, 2) }}
                    </span>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('guest.bookings.show', $payment->booking) }}" 
               class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-eye mr-2"></i>View Booking
            </a>
            @if($payment->booking->remaining_balance > 0)
            <a href="{{ route('payments.create', $payment->booking) }}" 
               class="flex-1 bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-yellow-700 focus:ring-2 focus:ring-yellow-500 transition-colors">
                <i class="fas fa-credit-card mr-2"></i>Make Another Payment
            </a>
            @endif
            <a href="{{ route('guest.bookings') }}" 
               class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors">
                <i class="fas fa-list mr-2"></i>My Bookings
            </a>
        </div>

        <!-- Print Receipt -->
        <div class="text-center mt-8">
            <button onclick="window.print()" 
                    class="text-green-300 hover:text-green-100 transition-colors">
                <i class="fas fa-print mr-2"></i>Print Payment Receipt
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.max-w-2xl');
    if (mainContent) {
        mainContent.classList.add('print-area');
    }
    
    const actionButtons = document.querySelector('.flex.flex-col.sm\\:flex-row.gap-4');
    if (actionButtons) {
        actionButtons.classList.add('no-print');
    }
});
</script>
@endsection
