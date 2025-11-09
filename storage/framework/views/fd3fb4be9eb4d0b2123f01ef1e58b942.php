<?php $__env->startSection('title', 'Edit Payment'); ?>
<?php $__env->startSection('content'); ?>
<!-- Edit Payment Page -->
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="<?php echo e(route('payments.confirmation', $payment)); ?>" class="text-green-400 hover:text-green-300 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Confirmation
            </a>
        </div>

        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
                <i class="fas fa-edit text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">Edit Payment</h1>
            <p class="text-gray-400">Update your payment details below.</p>
        </div>

        <!-- Time Limit Warning -->
        <div class="bg-yellow-900/30 border border-yellow-600/50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-clock text-yellow-400 text-xl mr-3 mt-1"></i>
                <div>
                    <p class="text-yellow-200 font-semibold">Time Limit</p>
                    <p class="text-yellow-300 text-sm mt-1">
                        Payments can only be edited within 5 minutes of creation. 
                        Time remaining: <span id="timeRemaining" class="font-bold"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-green-50 mb-4">Booking Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Booking Reference:</span>
                    <span class="text-green-50 font-medium"><?php echo e($booking->booking_reference); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-green-50"><?php echo e($booking->room->name); ?></span>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-4">
                    <span class="text-green-300">Total Amount:</span>
                    <span class="text-green-50 font-bold text-lg">₱<?php echo e(number_format($booking->total_price, 2)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-green-300">Other Payments:</span>
                    <span class="text-green-400 font-medium">₱<?php echo e(number_format($booking->amount_paid - $payment->amount, 2)); ?></span>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-3">
                    <span class="text-green-100 font-semibold text-lg">Available Balance:</span>
                    <span class="text-yellow-400 font-bold text-2xl">₱<?php echo e(number_format($remainingBalance, 2)); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Current Payment Info -->
        <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-400 text-xl mr-3 mt-1"></i>
                <div class="flex-1">
                    <p class="text-blue-200 font-semibold">Current Payment Details</p>
                    <p class="text-blue-300 text-sm mt-1">
                        Amount: ₱<?php echo e(number_format($payment->amount, 2)); ?> | 
                        Method: <?php echo e($payment->payment_method_display); ?> |
                        Created: <?php echo e($payment->created_at->format('M d, Y g:i A')); ?>

                    </p>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">
                <i class="fas fa-wallet mr-2"></i>Update Payment Details
            </h2>
            
            <?php if($errors->any()): ?>
                <div class="bg-red-900/50 border border-red-600 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
                        <div>
                            <h4 class="text-red-200 font-semibold mb-2">Please fix the following errors:</h4>
                            <ul class="list-disc list-inside text-red-300 text-sm">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-900/50 border border-red-600 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-400 mr-3"></i>
                        <p class="text-red-200"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo e(route('payments.update', $payment)); ?>" method="POST" id="paymentForm" class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                
                <!-- Payment Amount -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">
                        <i class="fas fa-money-bill-wave mr-2"></i>Payment Amount
                    </label>
                    
                    <!-- Quick Select Buttons -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <button type="button" onclick="selectPaymentAmount(<?php echo e($minimumPayment); ?>)" 
                                class="px-4 py-3 bg-yellow-600/80 hover:bg-yellow-600 text-white rounded-lg font-medium transition-all text-sm">
                            <i class="fas fa-percentage mr-1"></i>
                            Minimum (50%)
                            <div class="text-xs opacity-90 mt-1">₱<?php echo e(number_format($minimumPayment, 2)); ?></div>
                        </button>
                        <button type="button" onclick="selectPaymentAmount(<?php echo e($remainingBalance); ?>)" 
                                class="px-4 py-3 bg-green-600/80 hover:bg-green-600 text-white rounded-lg font-medium transition-all text-sm">
                            <i class="fas fa-check-circle mr-1"></i>
                            Maximum
                            <div class="text-xs opacity-90 mt-1">₱<?php echo e(number_format($remainingBalance, 2)); ?></div>
                        </button>
                    </div>
                    
                    <!-- Amount Input -->
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-100 text-xl font-bold">₱</span>
                        <input type="number" 
                               id="payment_amount" 
                               name="payment_amount" 
                               min="<?php echo e($minimumPayment); ?>" 
                               max="<?php echo e($remainingBalance); ?>" 
                               step="0.01" 
                               value="<?php echo e(old('payment_amount', $payment->amount)); ?>" 
                               required 
                               class="w-full pl-12 pr-4 py-4 bg-green-800/50 border-2 border-green-600/50 rounded-lg text-green-100 text-2xl font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" 
                               oninput="updatePaymentSummary()"
                               placeholder="Enter amount">
                    </div>
                    
                    <div class="flex justify-between items-center mt-2 text-sm">
                        <p class="text-green-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Minimum: ₱<?php echo e(number_format($minimumPayment, 2)); ?>

                        </p>
                        <p class="text-green-400">
                            Maximum: ₱<?php echo e(number_format($remainingBalance, 2)); ?>

                        </p>
                    </div>
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
                            <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio" 
                                   <?php echo e(old('payment_method', $payment->payment_method) === 'cash' ? 'checked' : ''); ?>

                                   onchange="updateSelectedMethod('Cash', 'money-bill-wave', 'green')">
                            <div class="payment-method-card">
                                <i class="fas fa-money-bill-wave text-3xl text-green-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Cash</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="sr-only payment-method-radio"
                                   <?php echo e(old('payment_method', $payment->payment_method) === 'card' ? 'checked' : ''); ?>

                                   onchange="updateSelectedMethod('Credit/Debit Card', 'credit-card', 'blue')">
                            <div class="payment-method-card">
                                <i class="fas fa-credit-card text-3xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Card</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="gcash" class="sr-only payment-method-radio"
                                   <?php echo e(old('payment_method', $payment->payment_method) === 'gcash' ? 'checked' : ''); ?>

                                   onchange="updateSelectedMethod('GCash', 'mobile-alt', 'blue')">
                            <div class="payment-method-card">
                                <i class="fas fa-mobile-alt text-3xl text-blue-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">GCash</span>
                            </div>
                        </label>
                        
                        <label class="payment-method-option cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" class="sr-only payment-method-radio"
                                   <?php echo e(old('payment_method', $payment->payment_method) === 'bank_transfer' ? 'checked' : ''); ?>

                                   onchange="updateSelectedMethod('Bank Transfer', 'university', 'purple')">
                            <div class="payment-method-card">
                                <i class="fas fa-university text-3xl text-purple-400 mb-2"></i>
                                <span class="text-sm font-semibold text-white">Bank Transfer</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-green-200 text-sm font-medium mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Add any payment notes..." class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent"><?php echo e(old('notes', $payment->notes)); ?></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Payment
                    </button>
                    <a href="<?php echo e(route('payments.confirmation', $payment)); ?>" class="flex-1 bg-gray-600 text-white px-6 py-3 rounded-lg text-center hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
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
// Calculate time remaining
const createdAt = new Date('<?php echo e($payment->created_at->toIso8601String()); ?>');
const expiryTime = new Date(createdAt.getTime() + 5 * 60000); // 5 minutes

function updateTimeRemaining() {
    const now = new Date();
    const diff = expiryTime - now;
    
    if (diff <= 0) {
        document.getElementById('timeRemaining').textContent = 'Expired';
        document.getElementById('timeRemaining').classList.add('text-red-400');
        // Disable form
        document.getElementById('paymentForm').querySelectorAll('input, textarea, button[type="submit"]').forEach(el => {
            el.disabled = true;
        });
        return;
    }
    
    const minutes = Math.floor(diff / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    document.getElementById('timeRemaining').textContent = `${minutes}m ${seconds}s`;
}

// Update every second
setInterval(updateTimeRemaining, 1000);
updateTimeRemaining();

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

// Update payment summary (optional - can add if needed)
function updatePaymentSummary() {
    // Simple validation for amount
    const amount = parseFloat(document.getElementById('payment_amount').value);
    const min = <?php echo e($minimumPayment); ?>;
    const max = <?php echo e($remainingBalance); ?>;
    
    if (amount < min || amount > max) {
        document.getElementById('payment_amount').classList.add('border-red-500');
    } else {
        document.getElementById('payment_amount').classList.remove('border-red-500');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if payment method was already selected
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
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/payments/edit.blade.php ENDPATH**/ ?>