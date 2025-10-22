@extends('layouts.guest')
@section('title', 'Make Payment')
@section('content')
<!-- Make Payment Page -->
<div class="min-h-screen bg-gray-900 py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-green-50 mb-6">Make Payment</h1>
        <p class="text-gray-400 mb-10">Complete your booking payment below.</p>
        <!-- Booking Summary -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6 mb-8">
            <h3 class="text-xl font-bold text-green-50 mb-4">Booking Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Booking Reference:</span>
                    <span class="text-green-50 font-medium">{{ $booking->booking_reference }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-green-50">{{ $booking->room->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Check-in:</span>
                    <span class="text-green-50">{{ $booking->check_in->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Check-out:</span>
                    <span class="text-green-50">{{ $booking->check_out->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Guests:</span>
                    <span class="text-green-50">{{ $booking->guests }}</span>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-4">
                    <span class="text-green-300">Total Amount:</span>
                    <span class="text-green-50 font-bold text-lg">₱{{ number_format($booking->total_price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-green-300">Amount Paid:</span>
                    <span class="text-green-400 font-medium">₱{{ number_format($booking->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">
                    <span class="text-green-100 font-semibold text-lg">Remaining Balance:</span>
                    <span class="text-yellow-400 font-bold text-2xl">₱{{ number_format($remainingBalance, 2) }}</span>
                </div>
            </div>
        </div>
        <!-- Payment Form -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8">
            <h3 class="text-xl font-bold text-green-50 mb-6">Payment Details</h3>
            <form action="{{ route('payments.store', $booking) }}" method="POST" id="paymentForm" class="space-y-6">
                @csrf
                <!-- Payment Amount -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">Payment Amount</label>
                    <input type="number" id="payment_amount" name="payment_amount" min="{{ $minimumPayment }}" max="{{ $remainingBalance }}" step="0.01" value="{{ old('payment_amount', $remainingBalance) }}" required class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 text-xl font-bold focus:ring-2 focus:ring-green-500 focus:border-transparent" oninput="updatePaymentSummary()">
                    <p class="text-green-400 text-sm mt-2">Minimum payment: ₱{{ number_format($minimumPayment, 2) }}</p>
                </div>
                <!-- Payment Method -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-3">Payment Method</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" required>
                            <div class="payment-method-card">
                                <i class="fas fa-money-bill-wave text-2xl text-green-400 mb-2"></i>
                                <span class="text-sm font-medium">Cash</span>
                            </div>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="card" class="sr-only payment-method-radio">
                            <div class="payment-method-card">
                                <i class="fas fa-credit-card text-2xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-medium">Card</span>
                            </div>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="gcash" class="sr-only payment-method-radio">
                            <div class="payment-method-card">
                                <i class="fas fa-mobile-alt text-2xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-medium">GCash</span>
                            </div>
                        </label>
                        <label class="payment-method-option">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only payment-method-radio">
                            <div class="payment-method-card">
                                <i class="fas fa-university text-2xl text-purple-400 mb-2"></i>
                                <span class="text-sm font-medium">Bank Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>
                <!-- Notes -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Add any payment notes..." class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('notes') }}</textarea>
                </div>
                <!-- Summary -->
                <div class="bg-green-800/30 rounded-lg p-6 mb-6" id="paymentSummary">
                    <h4 class="text-green-200 font-semibold mb-4">Payment Summary</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-green-300">Payment Amount:</span>
                            <span class="text-green-50 font-bold" id="summaryAmount">₱{{ number_format($remainingBalance, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-green-300">Current Paid:</span>
                            <span class="text-green-50">₱{{ number_format($booking->amount_paid, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-green-700/30 pt-2">
                            <span class="text-green-100 font-semibold">Remaining Balance:</span>
                            <span class="text-yellow-400 font-bold" id="newRemainingBalance">₱0.00</span>
                        </div>
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-credit-card mr-2"></i>Process Payment
                    </button>
                    <a href="{{ route('guest.bookings.show', $booking) }}" class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Booking
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
.payment-method-card { @apply flex flex-col items-center justify-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-green-500 transition-all; }
.payment-method-radio:checked + .payment-method-card { @apply border-green-500 bg-green-900/20; }
.payment-method-option { @apply block; }
</style>
<script>
function updatePaymentSummary() {
    const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
    const currentPaid = {{ $booking->amount_paid }};
    const totalPrice = {{ $booking->total_price }};
    const newRemaining = Math.max(0, totalPrice - (currentPaid + paymentAmount));
    document.getElementById('summaryAmount').textContent = '₱' + paymentAmount.toFixed(2);
    document.getElementById('newRemainingBalance').textContent = '₱' + newRemaining.toFixed(2);
}
document.addEventListener('DOMContentLoaded', updatePaymentSummary);
</script>
@endsection




