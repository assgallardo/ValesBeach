@extends('layouts.guest')
@section('title', 'Make Payment')
@section('content')
<!-- Make Payment Page -->
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
                <i class="fas fa-credit-card text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">Make Payment</h1>
            <p class="text-gray-400">Complete your booking payment below.</p>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
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
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">
                <i class="fas fa-wallet mr-2"></i>Payment Details
            </h2>
            
            @if ($errors->any())
                <div class="bg-red-900/50 border border-red-600 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-red-200 font-semibold mb-2">Please fix the following errors:</h4>
                            <ul class="list-disc list-inside text-red-300 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-900/50 border border-red-600 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-400 mr-3"></i>
                        <p class="text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
            
            <form action="{{ route('payments.store', $booking) }}" method="POST" id="paymentForm" class="space-y-6">
                @csrf
                
                <!-- Payment Amount -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">
                        <i class="fas fa-money-bill-wave mr-2"></i>Payment Amount
                    </label>
                    
                    <!-- Quick Select Buttons -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <button type="button" onclick="selectPaymentAmount({{ $minimumPayment }})" 
                                class="px-4 py-3 bg-yellow-600/80 hover:bg-yellow-600 text-white rounded-lg font-medium transition-all text-sm">
                            <i class="fas fa-percentage mr-1"></i>
                            Partial (50%)
                            <div class="text-xs opacity-90 mt-1">₱{{ number_format($minimumPayment, 2) }}</div>
                        </button>
                        <button type="button" onclick="selectPaymentAmount({{ $remainingBalance }})" 
                                class="px-4 py-3 bg-green-600/80 hover:bg-green-600 text-white rounded-lg font-medium transition-all text-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            Full Payment
                            <div class="text-xs opacity-90 mt-1">₱{{ number_format($remainingBalance, 2) }}</div>
                        </button>
                    </div>
                    
                    <!-- Amount Input -->
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-100 text-xl font-bold">₱</span>
                        <input type="number" 
                               id="payment_amount" 
                               name="payment_amount" 
                               min="{{ $minimumPayment }}" 
                               max="{{ $remainingBalance }}" 
                               step="0.01" 
                               value="{{ old('payment_amount', $remainingBalance) }}" 
                               required 
                               class="w-full pl-12 pr-4 py-4 bg-green-800/50 border-2 border-green-600/50 rounded-lg text-green-100 text-2xl font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" 
                               oninput="updatePaymentSummary()"
                               placeholder="Enter amount">
                    </div>
                    
                    <div class="flex justify-between items-center mt-2 text-sm">
                        <p class="text-green-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Minimum: ₱{{ number_format($minimumPayment, 2) }} (50%)
                        </p>
                        <p class="text-green-400">
                            Maximum: ₱{{ number_format($remainingBalance, 2) }}
                        </p>
                    </div>
                    
                    @error('payment_amount')
                        <p class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Payment Method -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">
                        <i class="fas fa-credit-card mr-2"></i>Select Payment Method
                    </label>
                    
                    <!-- Selected Method Display -->
                    <div id="selectedMethodDisplay" class="bg-gray-800/50 rounded-lg p-3 mb-3 border border-gray-700">
                        <p class="text-gray-400 text-sm">
                            <i class="fas fa-hand-pointer mr-1"></i>
                            Click a payment method below
                        </p>
                    </div>
                    
                    <!-- Payment Method Cards -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" required 
                                   onchange="updateSelectedMethod('Cash', 'money-bill-wave', 'green')">
                            <div class="payment-method-card">
                                <i class="fas fa-money-bill-wave text-3xl text-green-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Cash</span>
                                <span class="text-xs text-gray-400 mt-1">Pay on arrival</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="sr-only payment-method-radio"
                                   onchange="updateSelectedMethod('Credit/Debit Card', 'credit-card', 'blue')">
                            <div class="payment-method-card">
                                <i class="fas fa-credit-card text-3xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Card</span>
                                <span class="text-xs text-gray-400 mt-1">Visa/Mastercard</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="gcash" class="sr-only payment-method-radio"
                                   onchange="updateSelectedMethod('GCash', 'mobile-alt', 'blue')">
                            <div class="payment-method-card">
                                <i class="fas fa-mobile-alt text-3xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">GCash</span>
                                <span class="text-xs text-gray-400 mt-1">E-wallet</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only payment-method-radio"
                                   onchange="updateSelectedMethod('Bank Transfer', 'university', 'purple')">
                            <div class="payment-method-card">
                                <i class="fas fa-university text-3xl text-purple-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Bank Transfer</span>
                                <span class="text-xs text-gray-400 mt-1">Online banking</span>
                            </div>
                        </label>
                    </div>
                    
                    @error('payment_method')
                        <p class="text-red-400 text-sm mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Notes -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Add any payment notes..." class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('notes') }}</textarea>
                </div>
                <!-- Summary -->
                <div class="bg-gray-900/50 rounded-lg p-4 border border-gray-700" id="paymentSummary">
                    <h4 class="text-green-50 font-semibold mb-4 text-sm">
                        <i class="fas fa-calculator mr-2"></i>Payment Summary
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-green-300">Payment Amount:</span>
                            <span class="text-green-400 font-bold text-xl" id="summaryAmount">₱{{ number_format($remainingBalance, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-300">Already Paid:</span>
                            <span class="text-green-50">₱{{ number_format($booking->amount_paid ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center border-t border-green-700/30 pt-3 mt-3">
                            <span class="text-green-100 font-semibold text-lg">After This Payment:</span>
                            <span id="totalPaidDisplay" class="text-green-400 font-bold text-lg">₱{{ number_format($remainingBalance + ($booking->amount_paid ?? 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3">
                            <span class="text-green-100 font-semibold text-lg">Remaining Balance:</span>
                            <span class="font-bold text-2xl" id="newRemainingBalance">₱0.00</span>
                        </div>
                        
                        <!-- Payment Status Indicator -->
                        <div class="border-t border-green-700/30 pt-3 mt-3">
                            <div id="paymentStatusIndicator" class="text-center py-3 rounded-lg bg-green-600 text-white font-semibold">
                                <i class="fas fa-check-circle mr-2"></i>Full Payment - Booking will be COMPLETED
                            </div>
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
.payment-method-card { 
    @apply flex flex-col items-center justify-center p-5 bg-gray-800 border-2 border-gray-600 rounded-lg cursor-pointer transition-all;
}

.payment-method-card:hover {
    @apply border-green-500 bg-gray-700 transform scale-105 shadow-lg;
}

.payment-method-radio:checked + .payment-method-card { 
    @apply border-green-500 bg-green-900/30 shadow-xl ring-2 ring-green-500/50;
    transform: scale(1.05);
}

.payment-method-option { 
    @apply block; 
}
</style>

<script>
// Quick select payment amount
function selectPaymentAmount(amount) {
    document.getElementById('payment_amount').value = amount.toFixed(2);
    updatePaymentSummary();
}

// Update selected payment method display
function updateSelectedMethod(methodName, iconClass, colorClass) {
    const display = document.getElementById('selectedMethodDisplay');
    
    const colorMap = {
        'green': 'text-green-400',
        'blue': 'text-blue-400',
        'purple': 'text-purple-400'
    };
    
    const iconColor = colorMap[colorClass] || 'text-green-400';
    
    display.innerHTML = `
        <div class="flex items-center justify-center">
            <i class="fas fa-${iconClass} ${iconColor} text-2xl mr-3"></i>
            <div>
                <p class="text-gray-400 text-xs">Selected Payment Method:</p>
                <p class="text-green-50 font-bold text-base">${methodName}</p>
            </div>
        </div>
    `;
    display.className = 'bg-green-900/30 rounded-lg p-3 mb-3 border border-green-600/50';
}

// Update payment summary in real-time
function updatePaymentSummary() {
    const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;
    const currentPaid = {{ $booking->amount_paid ?? 0 }};
    const totalPrice = {{ $booking->total_price }};
    
    const totalAfterPayment = currentPaid + paymentAmount;
    const newRemaining = Math.max(0, totalPrice - totalAfterPayment);
    
    // Format numbers with thousand separators
    const formatMoney = (amount) => '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // Update display elements
    document.getElementById('summaryAmount').textContent = formatMoney(paymentAmount);
    document.getElementById('totalPaidDisplay').textContent = formatMoney(totalAfterPayment);
    document.getElementById('newRemainingBalance').textContent = formatMoney(newRemaining);
    
    // Update remaining balance color
    const remainingElement = document.getElementById('newRemainingBalance');
    if (newRemaining > 0) {
        remainingElement.className = 'font-bold text-2xl text-yellow-400';
    } else {
        remainingElement.className = 'font-bold text-2xl text-green-400';
    }
    
    // Update payment status indicator
    const statusIndicator = document.getElementById('paymentStatusIndicator');
    const minimumPayment = {{ $minimumPayment }};
    
    if (paymentAmount < minimumPayment) {
        statusIndicator.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Amount below minimum (50% required)';
        statusIndicator.className = 'text-center py-3 rounded-lg bg-red-600 text-white font-semibold';
    } else if (newRemaining === 0) {
        statusIndicator.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Full Payment - Booking will be COMPLETED';
        statusIndicator.className = 'text-center py-3 rounded-lg bg-green-600 text-white font-semibold';
    } else {
        statusIndicator.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Partial Payment - Booking will be CONFIRMED<br><small class="text-sm opacity-90">Remaining balance: ' + formatMoney(newRemaining) + '</small>';
        statusIndicator.className = 'text-center py-3 rounded-lg bg-yellow-500 text-black font-semibold';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePaymentSummary();
    
    // Check if payment method was already selected (from validation errors)
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (selectedMethod) {
        const methods = {
            'cash': { name: 'Cash', icon: 'money-bill-wave', color: 'green' },
            'card': { name: 'Credit/Debit Card', icon: 'credit-card', color: 'blue' },
            'gcash': { name: 'GCash', icon: 'mobile-alt', color: 'blue' },
            'bank_transfer': { name: 'Bank Transfer', icon: 'university', color: 'purple' }
        };
        const method = methods[selectedMethod.value];
        if (method) {
            updateSelectedMethod(method.name, method.icon, method.color);
        }
    }
    
    // Add form submit handler for debugging
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Payment Amount:', document.getElementById('payment_amount').value);
            console.log('Payment Method:', document.querySelector('input[name="payment_method"]:checked')?.value);
            
            // Check if payment method is selected
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod) {
                e.preventDefault();
                alert('Please select a payment method');
                return false;
            }
            
            // Check if amount is valid
            const amount = parseFloat(document.getElementById('payment_amount').value);
            const min = {{ $minimumPayment }};
            const max = {{ $remainingBalance }};
            
            if (isNaN(amount) || amount < min || amount > max) {
                e.preventDefault();
                alert('Please enter a valid payment amount between ₱' + min.toFixed(2) + ' and ₱' + max.toFixed(2));
                return false;
            }
            
            console.log('Form validation passed, submitting...');
            // Form will submit normally
        });
    }
});
</script>
@endsection




