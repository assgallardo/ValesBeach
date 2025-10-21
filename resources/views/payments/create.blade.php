@extends('layouts.guest')@extends('layouts.guest')



@section('content')@section('title', 'Make Payment')

<!-- Background decorative blur elements -->

<div class="fixed inset-0 overflow-hidden pointer-events-none">@section('content')

    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div><div class="min-h-screen bg-gray-900 py-6">

    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>        <!-- Header -->

</div>        <div class="mb-8">

            <h1 class="text-3xl font-bold text-green-50">Make Payment</h1>

<main class="relative z-10 py-8 lg:py-16">            <p class="text-gray-400 mt-2">Complete your booking payment</p>

    <div class="container mx-auto px-4 lg:px-16 max-w-4xl">        </div>

        <!-- Page Header -->

        <div class="text-center mb-8">        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <h2 class="text-3xl md:text-4xl font-bold text-green-50 mb-4">            <!-- Booking Summary -->

                Payment for Booking #{{ $booking->id }}            <div class="bg-gray-800 rounded-lg p-6">

            </h2>                <h2 class="text-xl font-semibold text-green-50 mb-6">Booking Summary</h2>

            <p class="text-green-50 opacity-80 text-lg">                

                {{ $booking->room->name }}                <div class="space-y-4">

            </p>                    <div class="flex justify-between items-center">

        </div>                        <span class="text-gray-400">Booking Reference:</span>

                        <span class="text-green-50 font-medium">{{ $booking->booking_reference }}</span>

        <!-- Booking Summary Card -->                    </div>

        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6 mb-8">                    

            <h3 class="text-xl font-bold text-green-50 mb-4">Booking Summary</h3>                    <div class="flex justify-between items-center">

                                    <span class="text-gray-400">Room:</span>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">                        <span class="text-green-50">{{ $booking->room->name }}</span>

                <div>                    </div>

                    <label class="text-green-400 text-sm">Check-in</label>                    

                    <p class="text-green-50 font-medium">{{ $booking->check_in->format('M d, Y') }}</p>                    <div class="flex justify-between items-center">

                </div>                        <span class="text-gray-400">Check-in:</span>

                <div>                        <span class="text-green-50">{{ $booking->check_in->format('M d, Y') }}</span>

                    <label class="text-green-400 text-sm">Check-out</label>                    </div>

                    <p class="text-green-50 font-medium">{{ $booking->check_out->format('M d, Y') }}</p>                    

                </div>                    <div class="flex justify-between items-center">

                <div>                        <span class="text-gray-400">Check-out:</span>

                    <label class="text-green-400 text-sm">Nights</label>                        <span class="text-green-50">{{ $booking->check_out->format('M d, Y') }}</span>

                    <p class="text-green-50 font-medium">{{ $booking->check_in->diffInDays($booking->check_out) }}</p>                    </div>

                </div>                    

                <div>                    <div class="flex justify-between items-center">

                    <label class="text-green-400 text-sm">Guests</label>                        <span class="text-gray-400">Nights:</span>

                    <p class="text-green-50 font-medium">{{ $booking->guests }}</p>                        <span class="text-green-50">{{ $booking->check_in->diffInDays($booking->check_out) }}</span>

                </div>                    </div>

            </div>                    

                    <div class="flex justify-between items-center">

            <!-- Payment Summary -->                        <span class="text-gray-400">Guests:</span>

            <div class="border-t border-green-700/30 pt-4 space-y-3">                        <span class="text-green-50">{{ $booking->guests }}</span>

                <div class="flex justify-between">                    </div>

                    <span class="text-green-300">Total Booking Amount:</span>                    

                    <span class="text-green-50 font-bold text-lg">₱{{ number_format($booking->total_price, 2) }}</span>                    <hr class="border-gray-600">

                </div>                    

                                    <div class="flex justify-between items-center">

                @if($booking->amount_paid > 0)                        <span class="text-gray-400">Total Amount:</span>

                <div class="flex justify-between">                        <span class="text-green-50 font-semibold">{{ $booking->formatted_total_price }}</span>

                    <span class="text-green-300">Amount Paid:</span>                    </div>

                    <span class="text-green-400 font-medium">₱{{ number_format($booking->amount_paid, 2) }}</span>                    

                </div>                    <div class="flex justify-between items-center">

                @endif                        <span class="text-gray-400">Paid Amount:</span>

                                        <span class="text-green-50">{{ $booking->formatted_total_paid }}</span>

                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">                    </div>

                    <span class="text-green-100 font-semibold text-lg">Remaining Balance:</span>                    

                    <span class="text-yellow-400 font-bold text-2xl">₱{{ number_format($remainingBalance, 2) }}</span>                    <div class="flex justify-between items-center">

                </div>                        <span class="text-green-400 font-medium">Remaining Balance:</span>

            </div>                        <span class="text-green-400 font-bold text-lg">₱{{ number_format($remainingBalance, 2) }}</span>

        </div>                    </div>

                </div>

        <!-- Payment Form -->            </div>

        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8">

            <h3 class="text-xl font-bold text-green-50 mb-6">Payment Details</h3>            <!-- Payment Form -->

            <div class="bg-gray-800 rounded-lg p-6">

            @if($errors->any())                <h2 class="text-xl font-semibold text-green-50 mb-6">Payment Details</h2>

                <div class="bg-red-600/20 border border-red-500/50 rounded-lg p-4 mb-6">                

                    <ul class="list-disc list-inside text-red-100">                <form action="{{ route('payments.store', $booking) }}" method="POST" class="space-y-6">

                        @foreach($errors->all() as $error)                    @csrf

                            <li>{{ $error }}</li>                    

                        @endforeach                    <!-- Amount -->

                    </ul>                    <div>

                </div>                        <label for="amount" class="block text-sm font-medium text-green-200 mb-2">

            @endif                            Payment Amount

                        </label>

            <form action="{{ route('payments.store', $booking) }}" method="POST" id="paymentForm">                        <div class="relative">

                @csrf                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">₱</span>

                            <input 

                <!-- Payment Amount Selection -->                                type="number" 

                <div class="mb-6">                                id="amount" 

                    <label class="block text-green-200 text-sm font-medium mb-3">Payment Amount</label>                                name="amount" 

                                                    min="1" 

                    <!-- Quick Select Buttons -->                                max="{{ $remainingBalance }}" 

                    <div class="grid grid-cols-2 gap-4 mb-4">                                step="0.01"

                        <button type="button"                                 value="{{ old('amount', $remainingBalance) }}"

                                onclick="setPaymentAmount({{ $minimumPayment }})"                                class="w-full pl-8 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500"

                                class="bg-yellow-600 hover:bg-yellow-700 text-white py-3 px-4 rounded-lg transition-colors">                                required

                            <div class="text-sm">Minimum (50%)</div>                            >

                            <div class="text-lg font-bold">₱{{ number_format($minimumPayment, 2) }}</div>                        </div>

                        </button>                        @error('amount')

                        <button type="button"                             <p class="text-red-400 text-sm mt-1">{{ $message }}</p>

                                onclick="setPaymentAmount({{ $remainingBalance }})"                        @enderror

                                class="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg transition-colors">                        <p class="text-sm text-gray-400 mt-1">Maximum: ₱{{ number_format($remainingBalance, 2) }}</p>

                            <div class="text-sm">Pay in Full</div>                    </div>

                            <div class="text-lg font-bold">₱{{ number_format($remainingBalance, 2) }}</div>

                        </button>                    <!-- Payment Method -->

                    </div>                    <div>

                        <label class="block text-sm font-medium text-green-200 mb-3">

                    <!-- Custom Amount Input -->                            Payment Method

                    <div>                        </label>

                        <label class="text-green-300 text-sm mb-2 block">Or enter custom amount:</label>                        <div class="grid grid-cols-2 gap-3">

                        <input type="number"                             <label class="payment-method-option">

                               name="payment_amount"                                 <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" required>

                               id="payment_amount"                                <div class="payment-method-card">

                               step="0.01"                                    <i class="fas fa-money-bill-wave text-2xl text-green-400 mb-2"></i>

                               min="{{ $minimumPayment }}"                                    <span class="text-sm font-medium">Cash</span>

                               max="{{ $remainingBalance }}"                                </div>

                               value="{{ old('payment_amount', $remainingBalance) }}"                            </label>

                               required                            

                               class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 text-xl font-bold focus:ring-2 focus:ring-green-500 focus:border-transparent"                            <label class="payment-method-option">

                               oninput="updatePaymentSummary()">                                <input type="radio" name="payment_method" value="card" class="sr-only payment-method-radio" required>

                        <p class="text-green-400 text-sm mt-2">                                <div class="payment-method-card">

                            Minimum payment: ₱{{ number_format($minimumPayment, 2) }} (50% of remaining balance)                                    <i class="fas fa-credit-card text-2xl text-blue-400 mb-2"></i>

                        </p>                                    <span class="text-sm font-medium">Card</span>

                    </div>                                </div>

                </div>                            </label>

                            

                <!-- Payment Method -->                            <label class="payment-method-option">

                <div class="mb-6">                                <input type="radio" name="payment_method" value="gcash" class="sr-only payment-method-radio" required>

                    <label class="block text-green-200 text-sm font-medium mb-3">Payment Method</label>                                <div class="payment-method-card">

                    <select name="payment_method"                                     <i class="fas fa-mobile-alt text-2xl text-blue-500 mb-2"></i>

                            required                                    <span class="text-sm font-medium">GCash</span>

                            class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">                                </div>

                        <option value="">Select Payment Method</option>                            </label>

                        <option value="cash" selected>Cash</option>                            

                        <option value="card">Credit/Debit Card</option>                            <label class="payment-method-option">

                        <option value="bank_transfer">Bank Transfer</option>                                <input type="radio" name="payment_method" value="bank_transfer" class="sr-only payment-method-radio" required>

                        <option value="gcash">GCash</option>                                <div class="payment-method-card">

                        <option value="paymaya">PayMaya</option>                                    <i class="fas fa-university text-2xl text-purple-400 mb-2"></i>

                        <option value="online">Online Payment</option>                                    <span class="text-sm font-medium">Bank Transfer</span>

                    </select>                                </div>

                </div>                            </label>

                        </div>

                <!-- Notes -->                        @error('payment_method')

                <div class="mb-6">                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>

                    <label class="block text-green-200 text-sm font-medium mb-3">Notes (Optional)</label>                        @enderror

                    <textarea name="notes"                     </div>

                              rows="3"

                              placeholder="Any additional notes or instructions..."                    <!-- Notes -->

                              class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('notes') }}</textarea>                    <div>

                </div>                        <label for="notes" class="block text-sm font-medium text-green-200 mb-2">

                            Notes (Optional)

                <!-- Payment Summary Display -->                        </label>

                <div class="bg-green-800/30 rounded-lg p-6 mb-6" id="paymentSummary">                        <textarea 

                    <h4 class="text-green-200 font-semibold mb-4">Payment Summary</h4>                            id="notes" 

                    <div class="space-y-2">                            name="notes" 

                        <div class="flex justify-between">                            rows="3"

                            <span class="text-green-300">Payment Amount:</span>                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500"

                            <span class="text-green-50 font-bold" id="summaryAmount">₱{{ number_format($remainingBalance, 2) }}</span>                            placeholder="Add any payment notes..."

                        </div>                        >{{ old('notes') }}</textarea>

                        <div class="flex justify-between">                        @error('notes')

                            <span class="text-green-300">Current Paid:</span>                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>

                            <span class="text-green-50">₱{{ number_format($booking->amount_paid, 2) }}</span>                        @enderror

                        </div>                    </div>

                        <div class="flex justify-between border-t border-green-700/30 pt-2">

                            <span class="text-green-100 font-semibold">Total After Payment:</span>                    <!-- Action Buttons -->

                            <span class="text-green-50 font-bold" id="totalAfterPayment">₱{{ number_format($booking->amount_paid + $remainingBalance, 2) }}</span>                    <div class="flex flex-col sm:flex-row gap-4 pt-6">

                        </div>                        <button 

                        <div class="flex justify-between items-center" id="remainingAfterPayment">                            type="submit" 

                            <span class="text-green-300">Remaining Balance:</span>                            class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"

                            <span class="text-red-400 font-medium" id="newRemainingBalance">₱0.00</span>                        >

                        </div>                            <i class="fas fa-credit-card mr-2"></i>

                        <div class="mt-4 text-center">                            Process Payment

                            <span id="paymentStatusBadge" class="px-4 py-2 rounded-full text-sm font-bold bg-green-600 text-white">                        </button>

                                ✓ Fully Paid                        

                            </span>                        <a 

                        </div>                            href="{{ route('guest.bookings.show', $booking) }}" 

                    </div>                            class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors"

                </div>                        >

                            <i class="fas fa-arrow-left mr-2"></i>

                <!-- Information Box -->                            Back to Booking

                <div class="bg-blue-900/20 border border-blue-600/30 rounded-lg p-4 mb-6">                        </a>

                    <div class="flex items-start">                    </div>

                        <i class="fas fa-info-circle text-blue-400 text-xl mt-1 mr-3"></i>                </form>

                        <div class="text-blue-100 text-sm">            </div>

                            <p class="font-semibold mb-2">Payment Information:</p>        </div>

                            <ul class="list-disc list-inside space-y-1">    </div>

                                <li>You can pay at least 50% of the remaining balance</li></div>

                                <li>Pay the full amount to complete your booking</li>

                                <li>Remaining balance must be paid before check-in</li><style>

                                <li>Cash payments are processed immediately</li>.payment-method-card {

                                <li>Online payments may take 1-2 business days to process</li>    @apply flex flex-col items-center justify-center p-4 bg-gray-700 border-2 border-gray-600 rounded-lg cursor-pointer transition-all duration-200 hover:border-green-500;

                            </ul>}

                        </div>

                    </div>.payment-method-radio:checked + .payment-method-card {

                </div>    @apply border-green-500 bg-green-900/20;

}

                <!-- Action Buttons -->

                <div class="flex gap-4">.payment-method-option {

                    <a href="{{ route('guest.bookings.show', $booking) }}"     @apply block;

                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-3 px-6 rounded-lg transition-colors">}

                        Cancel</style>

                    </a>

                    <button type="submit" <script>

                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg transition-colors font-semibold">document.addEventListener('DOMContentLoaded', function() {

                        <i class="fas fa-check-circle mr-2"></i>Process Payment    // Handle payment method selection

                    </button>    const paymentMethodInputs = document.querySelectorAll('.payment-method-radio');

                </div>    

            </form>    paymentMethodInputs.forEach(input => {

        </div>        input.addEventListener('change', function() {

    </div>            // Remove selected class from all cards

</main>            document.querySelectorAll('.payment-method-card').forEach(card => {

                card.classList.remove('border-green-500', 'bg-green-900/20');

<script>                card.classList.add('border-gray-600');

function setPaymentAmount(amount) {            });

    document.getElementById('payment_amount').value = amount.toFixed(2);            

    updatePaymentSummary();            // Add selected class to current card

}            if (this.checked) {

                const card = this.nextElementSibling;

function updatePaymentSummary() {                card.classList.remove('border-gray-600');

    const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;                card.classList.add('border-green-500', 'bg-green-900/20');

    const currentPaid = {{ $booking->amount_paid }};            }

    const totalPrice = {{ $booking->total_price }};        });

    const remainingBalance = {{ $remainingBalance }};    });

    });

    // Update display values</script>

    document.getElementById('summaryAmount').textContent = '₱' + paymentAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');@endsection

    
    const totalAfter = currentPaid + paymentAmount;
    document.getElementById('totalAfterPayment').textContent = '₱' + totalAfter.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    
    const newRemaining = Math.max(0, totalPrice - totalAfter);
    document.getElementById('newRemainingBalance').textContent = '₱' + newRemaining.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    
    // Update status badge
    const statusBadge = document.getElementById('paymentStatusBadge');
    const remainingDiv = document.getElementById('remainingAfterPayment');
    
    if (newRemaining <= 0) {
        statusBadge.className = 'px-4 py-2 rounded-full text-sm font-bold bg-green-600 text-white';
        statusBadge.innerHTML = '✓ Fully Paid';
        remainingDiv.style.display = 'none';
    } else {
        statusBadge.className = 'px-4 py-2 rounded-full text-sm font-bold bg-yellow-600 text-white';
        statusBadge.innerHTML = '⚠ Partial Payment';
        remainingDiv.style.display = 'flex';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePaymentSummary();
});
</script>
@endsection
