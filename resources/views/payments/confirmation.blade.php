@extends('layouts.guest')@extends('layouts.guest')



@section('content')@section('title', 'Payment Confirmation')

<!-- Background decorative blur elements -->

<div class="fixed inset-0 overflow-hidden pointer-events-none">@section('content')

    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div><div class="min-h-screen bg-gray-900 py-6">

    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>        <!-- Success Message -->

</div>        <div class="text-center mb-8">

            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-600 rounded-full mb-4">

<main class="relative z-10 py-8 lg:py-16">                <i class="fas fa-check text-white text-2xl"></i>

    <div class="container mx-auto px-4 lg:px-16 max-w-4xl">            </div>

        <!-- Success Header -->            <h1 class="text-3xl font-bold text-green-50 mb-2">Payment {{ $payment->status === 'completed' ? 'Completed' : 'Submitted' }}!</h1>

        <div class="text-center mb-8">            <p class="text-gray-400">{{ $payment->status === 'completed' ? 'Your payment has been processed successfully.' : 'Your payment is being processed.' }}</p>

            <div class="inline-block p-4 bg-green-600 rounded-full mb-4">        </div>

                <i class="fas fa-check-circle text-6xl text-white"></i>

            </div>        <!-- Payment Details Card -->

            <h2 class="text-3xl md:text-4xl font-bold text-green-50 mb-4">        <div class="bg-gray-800 rounded-lg p-6 mb-6">

                Payment Successful!            <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Details</h2>

            </h2>            

            <p class="text-green-50 opacity-80 text-lg">            <div class="space-y-4">

                Your payment has been processed                <div class="flex justify-between items-center">

            </p>                    <span class="text-gray-400">Payment Reference:</span>

        </div>                    <span class="text-green-50 font-medium">{{ $payment->payment_reference }}</span>

                </div>

        <!-- Payment Details Card -->                

        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8 mb-6">                <div class="flex justify-between items-center">

            <h3 class="text-xl font-bold text-green-50 mb-6">Payment Details</h3>                    <span class="text-gray-400">Amount:</span>

                                <span class="text-green-400 font-bold text-lg">{{ $payment->formatted_amount }}</span>

            <div class="space-y-4">                </div>

                <div class="flex justify-between border-b border-green-700/30 pb-3">                

                    <span class="text-green-300">Payment Reference:</span>                <div class="flex justify-between items-center">

                    <span class="text-green-50 font-mono font-bold">{{ $payment->payment_reference }}</span>                    <span class="text-gray-400">Payment Method:</span>

                </div>                    <span class="text-green-50">{{ $payment->payment_method_display }}</span>

                                </div>

                <div class="flex justify-between border-b border-green-700/30 pb-3">                

                    <span class="text-green-300">Payment Amount:</span>                <div class="flex justify-between items-center">

                    <span class="text-green-50 font-bold text-xl">₱{{ number_format($payment->amount, 2) }}</span>                    <span class="text-gray-400">Status:</span>

                </div>                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium

                                        {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 

                <div class="flex justify-between border-b border-green-700/30 pb-3">                           ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white') }}">

                    <span class="text-green-300">Payment Method:</span>                        {{ ucfirst($payment->status) }}

                    <span class="text-green-50">{{ $payment->payment_method_display }}</span>                    </span>

                </div>                </div>

                                

                <div class="flex justify-between border-b border-green-700/30 pb-3">                <div class="flex justify-between items-center">

                    <span class="text-green-300">Payment Status:</span>                    <span class="text-gray-400">Date:</span>

                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $payment->status === 'completed' ? 'bg-green-600 text-white' : 'bg-yellow-600 text-white' }}">                    <span class="text-green-50">{{ $payment->created_at->format('M d, Y - g:i A') }}</span>

                        {{ ucfirst($payment->status) }}                </div>

                    </span>                

                </div>                @if($payment->notes)

                                <div class="pt-2 border-t border-gray-600">

                <div class="flex justify-between">                    <span class="text-gray-400 block mb-1">Notes:</span>

                    <span class="text-green-300">Payment Date:</span>                    <span class="text-green-50">{{ $payment->notes }}</span>

                    <span class="text-green-50">{{ $payment->created_at->format('M d, Y h:i A') }}</span>                </div>

                </div>                @endif

            </div>            </div>

        </div>        </div>



        <!-- Booking Payment Summary -->        <!-- Booking Information -->

        @if($payment->booking)        <div class="bg-gray-800 rounded-lg p-6 mb-6">

        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8 mb-6">            <h2 class="text-xl font-semibold text-green-50 mb-6">Booking Information</h2>

            <h3 class="text-xl font-bold text-green-50 mb-6">Booking Payment Summary</h3>            

                        <div class="space-y-4">

            <div class="space-y-4">                <div class="flex justify-between items-center">

                <div class="flex justify-between">                    <span class="text-gray-400">Booking Reference:</span>

                    <span class="text-green-300">Booking Reference:</span>                    <span class="text-green-50 font-medium">{{ $payment->booking->booking_reference }}</span>

                    <span class="text-green-50 font-mono">#{{ $payment->booking->id }}</span>                </div>

                </div>                

                                <div class="flex justify-between items-center">

                <div class="flex justify-between">                    <span class="text-gray-400">Room:</span>

                    <span class="text-green-300">Room:</span>                    <span class="text-green-50">{{ $payment->booking->room->name }}</span>

                    <span class="text-green-50">{{ $payment->booking->room->name }}</span>                </div>

                </div>                

                                <div class="flex justify-between items-center">

                <div class="flex justify-between border-t border-green-700/30 pt-3">                    <span class="text-gray-400">Check-in:</span>

                    <span class="text-green-300">Total Booking Amount:</span>                    <span class="text-green-50">{{ $payment->booking->check_in->format('M d, Y') }}</span>

                    <span class="text-green-50 font-bold">₱{{ number_format($payment->booking->total_price, 2) }}</span>                </div>

                </div>                

                                <div class="flex justify-between items-center">

                <div class="flex justify-between">                    <span class="text-gray-400">Check-out:</span>

                    <span class="text-green-300">Total Amount Paid:</span>                    <span class="text-green-50">{{ $payment->booking->check_out->format('M d, Y') }}</span>

                    <span class="text-green-400 font-bold">₱{{ number_format($payment->booking->amount_paid, 2) }}</span>                </div>

                </div>                

                                <hr class="border-gray-600">

                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">                

                    <span class="text-green-100 font-semibold text-lg">Remaining Balance:</span>                <div class="flex justify-between items-center">

                    <span class="font-bold text-2xl {{ $payment->booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400' }}">                    <span class="text-gray-400">Total Booking Amount:</span>

                        ₱{{ number_format($payment->booking->remaining_balance, 2) }}                    <span class="text-green-50 font-semibold">{{ $payment->booking->formatted_total_price }}</span>

                    </span>                </div>

                </div>                

                                <div class="flex justify-between items-center">

                <!-- Payment Status Badge -->                    <span class="text-gray-400">Total Paid:</span>

                <div class="text-center mt-6">                    <span class="text-green-50">{{ $payment->booking->formatted_total_paid }}</span>

                    <span class="px-6 py-3 rounded-full text-lg font-bold inline-block {{ $payment->booking->payment_status === 'paid' ? 'bg-green-600 text-white' : 'bg-yellow-600 text-white' }}">                </div>

                        @if($payment->booking->payment_status === 'paid')                

                            ✓ Booking Fully Paid                <div class="flex justify-between items-center">

                        @else                    <span class="text-green-400 font-medium">Remaining Balance:</span>

                            ⚠ Partial Payment - Balance Remaining                    <span class="text-green-400 font-bold">{{ $payment->booking->formatted_remaining_balance }}</span>

                        @endif                </div>

                    </span>            </div>

                </div>        </div>

                

                @if($payment->booking->remaining_balance > 0)        <!-- Payment Status Info -->

                <div class="bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-4 mt-4">        @if($payment->status === 'pending')

                    <div class="flex items-start">        <div class="bg-yellow-900/20 border border-yellow-600 rounded-lg p-4 mb-6">

                        <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mt-1 mr-3"></i>            <div class="flex items-start">

                        <div class="text-yellow-100 text-sm">                <i class="fas fa-info-circle text-yellow-400 mt-1 mr-3"></i>

                            <p class="font-semibold mb-2">Balance Payment Required:</p>                <div>

                            <p>You still have a remaining balance of <span class="font-bold">₱{{ number_format($payment->booking->remaining_balance, 2) }}</span> to be paid before your check-in date.</p>                    <h3 class="text-yellow-400 font-medium mb-1">Payment Processing</h3>

                            <p class="mt-2">You can make another payment anytime from your booking details page.</p>                    <p class="text-yellow-200 text-sm">

                        </div>                        Your payment is currently being processed. You will receive an email confirmation once the payment is completed. 

                    </div>                        This may take a few minutes for card payments or up to 24 hours for bank transfers.

                </div>                    </p>

                @else                </div>

                <div class="bg-green-900/20 border border-green-600/30 rounded-lg p-4 mt-4">            </div>

                    <div class="flex items-start">        </div>

                        <i class="fas fa-check-circle text-green-400 text-xl mt-1 mr-3"></i>        @endif

                        <div class="text-green-100 text-sm">

                            <p class="font-semibold mb-2">Payment Complete!</p>        @if($payment->booking->isPaid())

                            <p>Your booking is now fully paid. We look forward to welcoming you!</p>        <div class="bg-green-900/20 border border-green-600 rounded-lg p-4 mb-6">

                        </div>            <div class="flex items-start">

                    </div>                <i class="fas fa-check-circle text-green-400 mt-1 mr-3"></i>

                </div>                <div>

                @endif                    <h3 class="text-green-400 font-medium mb-1">Booking Confirmed</h3>

            </div>                    <p class="text-green-200 text-sm">

        </div>                        Your booking is now fully paid and confirmed! You can check your booking details anytime in your dashboard.

        @endif                    </p>

                </div>

        <!-- Action Buttons -->            </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">        </div>

            <a href="{{ route('guest.bookings.show', $payment->booking) }}"         @endif

               class="bg-blue-600 hover:bg-blue-700 text-white text-center py-3 px-6 rounded-lg transition-colors">

                <i class="fas fa-eye mr-2"></i>View Booking        <!-- Action Buttons -->

            </a>        <div class="flex flex-col sm:flex-row gap-4">

                        <a 

            @if($payment->booking->remaining_balance > 0)                href="{{ route('guest.bookings.show', $payment->booking) }}" 

            <a href="{{ route('payments.create', $payment->booking) }}"                 class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"

               class="bg-yellow-600 hover:bg-yellow-700 text-white text-center py-3 px-6 rounded-lg transition-colors">            >

                <i class="fas fa-credit-card mr-2"></i>Make Another Payment                <i class="fas fa-eye mr-2"></i>

            </a>                View Booking

            @endif            </a>

                        

            <a href="{{ route('guest.bookings') }}"             <a 

               class="bg-green-600 hover:bg-green-700 text-white text-center py-3 px-6 rounded-lg transition-colors">                href="{{ route('payments.history') }}" 

                <i class="fas fa-list mr-2"></i>My Bookings                class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"

            </a>            >

        </div>                <i class="fas fa-history mr-2"></i>

                Payment History

        <!-- Print/Download Section -->            </a>

        <div class="text-center mt-8">            

            <button onclick="window.print()"             <a 

                    class="text-green-300 hover:text-green-100 transition-colors">                href="{{ route('guest.dashboard') }}" 

                <i class="fas fa-print mr-2"></i>Print Payment Receipt                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"

            </button>            >

        </div>                <i class="fas fa-home mr-2"></i>

    </div>                Dashboard

</main>            </a>

        </div>

<style>

@media print {        <!-- Print Receipt Button -->

    .fixed, button, a {        <div class="text-center mt-6">

        display: none !important;            <button 

    }                onclick="window.print()" 

    main {                class="text-green-400 hover:text-green-300 font-medium"

        background: white !important;            >

    }                <i class="fas fa-print mr-2"></i>

    .bg-green-900\/50 {                Print Receipt

        background: #f9fafb !important;            </button>

        border: 1px solid #e5e7eb !important;        </div>

    }    </div>

}</div>

</style>

@endsection<!-- Print Styles -->

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
