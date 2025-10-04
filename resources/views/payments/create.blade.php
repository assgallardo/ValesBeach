@extends('layouts.guest')

@section('title', 'Make Payment')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-green-50">Make Payment</h1>
            <p class="text-gray-400 mt-2">Complete your booking payment</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Booking Summary -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-green-50 mb-6">Booking Summary</h2>
                
                <div class="space-y-4">
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
                        <span class="text-gray-400">Nights:</span>
                        <span class="text-green-50">{{ $booking->check_in->diffInDays($booking->check_out) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Guests:</span>
                        <span class="text-green-50">{{ $booking->guests }}</span>
                    </div>
                    
                    <hr class="border-gray-600">
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Amount:</span>
                        <span class="text-green-50 font-semibold">{{ $booking->formatted_total_price }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Paid Amount:</span>
                        <span class="text-green-50">{{ $booking->formatted_total_paid }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-green-400 font-medium">Remaining Balance:</span>
                        <span class="text-green-400 font-bold text-lg">₱{{ number_format($remainingBalance, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Details</h2>
                
                <form action="{{ route('payments.store', $booking) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-green-200 mb-2">
                            Payment Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">₱</span>
                            <input 
                                type="number" 
                                id="amount" 
                                name="amount" 
                                min="1" 
                                max="{{ $remainingBalance }}" 
                                step="0.01"
                                value="{{ old('amount', $remainingBalance) }}"
                                class="w-full pl-8 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                required
                            >
                        </div>
                        @error('amount')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-400 mt-1">Maximum: ₱{{ number_format($remainingBalance, 2) }}</p>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-green-200 mb-3">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" required>
                                <div class="payment-method-card">
                                    <i class="fas fa-money-bill-wave text-2xl text-green-400 mb-2"></i>
                                    <span class="text-sm font-medium">Cash</span>
                                </div>
                            </label>
                            
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="card" class="sr-only payment-method-radio" required>
                                <div class="payment-method-card">
                                    <i class="fas fa-credit-card text-2xl text-blue-400 mb-2"></i>
                                    <span class="text-sm font-medium">Card</span>
                                </div>
                            </label>
                            
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="gcash" class="sr-only payment-method-radio" required>
                                <div class="payment-method-card">
                                    <i class="fas fa-mobile-alt text-2xl text-blue-500 mb-2"></i>
                                    <span class="text-sm font-medium">GCash</span>
                                </div>
                            </label>
                            
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="bank_transfer" class="sr-only payment-method-radio" required>
                                <div class="payment-method-card">
                                    <i class="fas fa-university text-2xl text-purple-400 mb-2"></i>
                                    <span class="text-sm font-medium">Bank Transfer</span>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-green-200 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            rows="3"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Add any payment notes..."
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6">
                        <button 
                            type="submit" 
                            class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"
                        >
                            <i class="fas fa-credit-card mr-2"></i>
                            Process Payment
                        </button>
                        
                        <a 
                            href="{{ route('guest.bookings.show', $booking) }}" 
                            class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Booking
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-card {
    @apply flex flex-col items-center justify-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer transition-all duration-200 hover:border-green-500;
}

.payment-method-radio:checked + .payment-method-card {
    @apply border-green-500 bg-green-900/20;
}

.payment-method-option {
    @apply block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method selection
    const paymentMethodInputs = document.querySelectorAll('.payment-method-radio');
    
    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('border-green-500', 'bg-green-900/20');
                card.classList.add('border-gray-600');
            });
            
            // Add selected class to current card
            if (this.checked) {
                const card = this.nextElementSibling;
                card.classList.remove('border-gray-600');
                card.classList.add('border-green-500', 'bg-green-900/20');
            }
        });
    });
});
</script>
@endsection
