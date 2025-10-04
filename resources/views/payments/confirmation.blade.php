@extends('layouts.guest')

@section('title', 'Payment Confirmation')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-4">
                <i class="fas fa-check text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">Payment {{ $payment->status === 'completed' ? 'Completed' : 'Submitted' }}!</h1>
            <p class="text-gray-400">{{ $payment->status === 'completed' ? 'Your payment has been processed successfully.' : 'Your payment is being processed.' }}</p>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Details</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Payment Reference:</span>
                    <span class="text-green-50 font-medium">{{ $payment->payment_reference }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Amount:</span>
                    <span class="text-green-400 font-bold text-lg">{{ $payment->formatted_amount }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Payment Method:</span>
                    <span class="text-green-50">{{ $payment->payment_method_display }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Status:</span>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 
                           ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white') }}">
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
                
                <hr class="border-gray-600">
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Booking Amount:</span>
                    <span class="text-green-50 font-semibold">{{ $payment->booking->formatted_total_price }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total Paid:</span>
                    <span class="text-green-50">{{ $payment->booking->formatted_total_paid }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-green-400 font-medium">Remaining Balance:</span>
                    <span class="text-green-400 font-bold">{{ $payment->booking->formatted_remaining_balance }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Status Info -->
        @if($payment->status === 'pending')
        <div class="bg-yellow-900/20 border border-yellow-600 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-400 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-yellow-400 font-medium mb-1">Payment Processing</h3>
                    <p class="text-yellow-200 text-sm">
                        Your payment is currently being processed. You will receive an email confirmation once the payment is completed. 
                        This may take a few minutes for card payments or up to 24 hours for bank transfers.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($payment->booking->isPaid())
        <div class="bg-green-900/20 border border-green-600 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>
                <div>
                    <h3 class="text-green-400 font-medium mb-1">Booking Confirmed</h3>
                    <p class="text-green-200 text-sm">
                        Your booking is now fully paid and confirmed! You can check your booking details anytime in your dashboard.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a 
                href="{{ route('guest.bookings.show', $payment->booking) }}" 
                class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"
            >
                <i class="fas fa-eye mr-2"></i>
                View Booking
            </a>
            
            <a 
                href="{{ route('payments.history') }}" 
                class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"
            >
                <i class="fas fa-history mr-2"></i>
                Payment History
            </a>
            
            <a 
                href="{{ route('guest.dashboard') }}" 
                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"
            >
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </div>

        <!-- Print Receipt Button -->
        <div class="text-center mt-6">
            <button 
                onclick="window.print()" 
                class="text-green-400 hover:text-green-300 font-medium"
            >
                <i class="fas fa-print mr-2"></i>
                Print Receipt
            </button>
        </div>
    </div>
</div>

<!-- Print Styles -->
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
// Add print-area class to the main content for printing
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.querySelector('.max-w-2xl');
    if (mainContent) {
        mainContent.classList.add('print-area');
    }
    
    // Hide action buttons when printing
    const actionButtons = document.querySelector('.flex.flex-col.sm\\:flex-row.gap-4');
    if (actionButtons) {
        actionButtons.classList.add('no-print');
    }
});
</script>
@endsection
