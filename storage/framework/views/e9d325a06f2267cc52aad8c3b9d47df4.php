<?php $__env->startSection('title', 'Payment Confirmation'); ?>

<?php $__env->startSection('content'); ?>
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
                Payment <?php echo e($payment->status === 'completed' ? 'Completed' : 'Submitted'); ?>!
            </h1>
            <p class="text-gray-400">
                <?php echo e($payment->status === 'completed' ? 'Your payment has been processed successfully.' : 'Your payment is being processed.'); ?>

            </p>
        </div>

        <!-- Payment Details -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">
                <i class="fas fa-receipt mr-2"></i>Payment Details
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-hashtag mr-1"></i>Payment Reference:
                    </span>
                    <span class="text-green-50 font-medium"><?php echo e($payment->payment_reference); ?></span>
                </div>
                <div class="flex justify-between items-center bg-green-900/20 rounded-lg p-3 border border-green-600/30">
                    <span class="text-green-300 font-medium">
                        <i class="fas fa-money-bill-wave mr-1"></i>Payment Amount:
                    </span>
                    <span class="text-green-400 font-bold text-2xl">₱<?php echo e(number_format($payment->amount, 2)); ?></span>
                </div>
                <div class="bg-gray-700/50 rounded-lg p-4 border-2 border-gray-600">
                    <span class="text-gray-400 text-sm block mb-2">
                        <i class="fas fa-credit-card mr-1"></i>Payment Method:
                    </span>
                    <div class="flex items-center">
                        <?php
                            $methodIcons = [
                                'cash' => 'money-bill-wave',
                                'card' => 'credit-card',
                                'gcash' => 'mobile-alt',
                                'bank_transfer' => 'university',
                                'paymaya' => 'mobile-alt',
                                'online' => 'globe'
                            ];
                            $methodColors = [
                                'cash' => 'text-green-400',
                                'card' => 'text-blue-400',
                                'gcash' => 'text-blue-400',
                                'bank_transfer' => 'text-purple-400',
                                'paymaya' => 'text-blue-400',
                                'online' => 'text-indigo-400'
                            ];
                            $icon = $methodIcons[$payment->payment_method] ?? 'money-bill';
                            $iconColor = $methodColors[$payment->payment_method] ?? 'text-gray-400';
                        ?>
                        <i class="fas fa-<?php echo e($icon); ?> text-3xl <?php echo e($iconColor); ?> mr-3"></i>
                        <span class="text-green-50 font-bold text-xl"><?php echo e($payment->payment_method_display); ?></span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>Payment Status:
                    </span>
                    <?php
                        $isPartialPayment = $payment->booking && $payment->booking->remaining_balance > 0;
                        $statusBadge = $isPartialPayment ? 'bg-yellow-500 text-black' : ($payment->status === 'completed' ? 'bg-green-500 text-white' : ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white'));
                        $statusIcon = $isPartialPayment ? 'exclamation-triangle' : ($payment->status === 'completed' ? 'check-circle' : ($payment->status === 'pending' ? 'clock' : 'info-circle'));
                        $statusText = $isPartialPayment ? 'Partial' : ucfirst($payment->status);
                    ?>
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold <?php echo e($statusBadge); ?>">
                        <i class="fas fa-<?php echo e($statusIcon); ?> mr-1"></i>
                        <?php echo e($statusText); ?>

                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-calendar mr-1"></i>Date & Time:
                    </span>
                    <span class="text-green-50"><?php echo e($payment->created_at->format('M d, Y - g:i A')); ?></span>
                </div>
                <?php if($payment->notes): ?>
                <div class="pt-3 border-t border-gray-600">
                    <span class="text-gray-400 block mb-2">
                        <i class="fas fa-sticky-note mr-1"></i>Notes:
                    </span>
                    <div class="bg-gray-700/50 rounded-lg p-3">
                        <span class="text-green-50"><?php echo e($payment->notes); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Information -->
        <?php if($payment->booking): ?>
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-green-50 mb-6">
                <i class="fas fa-bed mr-2"></i>Booking Information
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-hashtag mr-1"></i>Booking Reference:
                    </span>
                    <span class="text-green-50 font-medium"><?php echo e($payment->booking->booking_reference); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-door-open mr-1"></i>Room:
                    </span>
                    <span class="text-green-50"><?php echo e($payment->booking->room->name); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-calendar-check mr-1"></i>Check-in:
                    </span>
                    <span class="text-green-50"><?php echo e($payment->booking->check_in->format('M d, Y')); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">
                        <i class="fas fa-calendar-times mr-1"></i>Check-out:
                    </span>
                    <span class="text-green-50"><?php echo e($payment->booking->check_out->format('M d, Y')); ?></span>
                </div>
                <div class="border-t border-green-700/30 pt-4 mt-4">
                    <h3 class="text-green-200 font-semibold mb-3 text-sm">
                        <i class="fas fa-calculator mr-2"></i>Payment Breakdown
                    </h3>
                    
                    <div class="bg-gray-900/50 rounded-lg p-4 space-y-3">
                        <!-- Total Booking Cost -->
                        <div class="flex justify-between items-center pb-2">
                            <span class="text-gray-400 text-sm">Total Booking Cost:</span>
                            <span class="text-green-50 font-bold text-lg">₱<?php echo e(number_format($payment->booking->total_price, 2)); ?></span>
                        </div>
                        
                        <!-- Previous Payments -->
                        <?php
                            $previousPaid = $payment->booking->amount_paid - $payment->amount;
                        ?>
                        
                        <?php if($previousPaid > 0): ?>
                        <div class="flex justify-between items-center pb-2">
                            <span class="text-gray-400 text-sm">Previously Paid:</span>
                            <span class="text-gray-300 font-medium">₱<?php echo e(number_format($previousPaid, 2)); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- This Payment -->
                        <div class="flex justify-between items-center pb-3 border-b border-gray-700 bg-green-900/20 rounded p-2">
                            <div>
                                <span class="text-green-300 text-sm font-medium">
                                    <i class="fas fa-plus-circle mr-1"></i>This Payment:
                                </span>
                                <p class="text-green-200 text-xs">Just paid</p>
                            </div>
                            <span class="text-green-400 font-bold text-xl">₱<?php echo e(number_format($payment->amount, 2)); ?></span>
                        </div>
                        
                        <!-- Total Amount Paid (calculation) -->
                        <div class="flex justify-between items-center bg-blue-900/20 rounded p-3 border border-blue-600/30">
                            <div>
                                <span class="text-blue-300 text-sm font-semibold">
                                    <i class="fas fa-equals mr-1"></i>Total Paid:
                                </span>
                                <p class="text-blue-200 text-xs">
                                    All payments
                                </p>
                            </div>
                            <span class="text-blue-400 font-bold text-xl">₱<?php echo e(number_format($payment->booking->amount_paid, 2)); ?></span>
                        </div>
                    </div>
                </div>
                <div class="border-t border-green-700/30 pt-4 mt-3">
                    <!-- Calculation Display -->
                    <div class="bg-gray-800/50 rounded-lg p-4 mb-3">
                        <div class="text-center space-y-2">
                            <div class="text-gray-400 text-sm font-medium">
                                <i class="fas fa-calculator mr-1"></i>Remaining Balance Calculation:
                            </div>
                            <div class="flex items-center justify-center gap-3 text-lg font-mono">
                                <span class="text-green-50">₱<?php echo e(number_format($payment->booking->total_price, 2)); ?></span>
                                <span class="text-gray-500">−</span>
                                <span class="text-blue-400">₱<?php echo e(number_format($payment->booking->amount_paid, 2)); ?></span>
                                <span class="text-gray-500">=</span>
                                <span class="font-bold <?php echo e($payment->booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400'); ?>">
                                    ₱<?php echo e(number_format($payment->booking->remaining_balance, 2)); ?>

                                </span>
                            </div>
                            <div class="text-gray-500 text-xs">
                                (Total Cost − Total Paid = Remaining)
                            </div>
                        </div>
                    </div>
                    
                    <!-- Remaining Balance Display -->
                    <div class="flex justify-between items-center pb-3 bg-<?php echo e($payment->booking->remaining_balance > 0 ? 'yellow' : 'green'); ?>-900/20 rounded-lg p-4 border-2 border-<?php echo e($payment->booking->remaining_balance > 0 ? 'yellow' : 'green'); ?>-600/50">
                        <div>
                            <span class="text-<?php echo e($payment->booking->remaining_balance > 0 ? 'yellow' : 'green'); ?>-200 font-semibold text-lg">
                                <i class="fas fa-<?php echo e($payment->booking->remaining_balance > 0 ? 'exclamation-triangle' : 'check-circle'); ?> mr-2"></i>Remaining Balance:
                            </span>
                            <p class="text-gray-400 text-xs mt-1">
                                <?php echo e($payment->booking->remaining_balance > 0 ? 'Amount still due' : 'Fully paid'); ?>

                            </p>
                        </div>
                        <span class="font-bold text-4xl <?php echo e($payment->booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400'); ?>">
                            ₱<?php echo e(number_format($payment->booking->remaining_balance, 2)); ?>

                        </span>
                    </div>
                    
                    <?php if($payment->booking->remaining_balance > 0): ?>
                        <div class="bg-yellow-900/30 border border-yellow-600/50 rounded-lg p-4 mt-2">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl mr-3 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-yellow-200 font-bold text-base">Partial Payment Made</p>
                                    <p class="text-yellow-300 text-sm mt-1">
                                        You have successfully made a partial payment. Please pay the remaining balance of 
                                        <span class="font-bold text-yellow-100">₱<?php echo e(number_format($payment->booking->remaining_balance, 2)); ?></span>
                                        to complete your booking.
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-green-900/30 border border-green-600/50 rounded-lg p-4 mt-2">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-400 text-xl mr-3 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-green-200 font-bold text-base">Full Payment Received!</p>
                                    <p class="text-green-300 text-sm mt-1">
                                        Your booking has been fully paid and will be marked as completed. Thank you for your payment!
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between items-center border-t border-green-700/30 pt-3 mt-3">
                    <span class="text-green-100 font-semibold text-lg">Booking Status:</span>
                    <?php
                        $status = $payment->booking->status;
                        $statusColors = [
                            'completed' => 'bg-green-600 text-white',
                            'confirmed' => 'bg-yellow-500 text-black',
                            'pending' => 'bg-gray-500 text-white',
                            'cancelled' => 'bg-red-600 text-white'
                        ];
                        $statusLabels = [
                            'completed' => 'Completed - Fully Paid',
                            'confirmed' => 'Confirmed - Partial Payment',
                            'pending' => 'Pending',
                            'cancelled' => 'Cancelled'
                        ];
                    ?>
                    <span class="inline-block px-4 py-2 rounded-lg font-bold text-lg <?php echo e($statusColors[$status] ?? 'bg-gray-500 text-white'); ?>">
                        <?php echo e($statusLabels[$status] ?? ucfirst($status)); ?>

                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Important Notice -->
        <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-400 text-xl mr-3 mt-1"></i>
                <div class="flex-1">
                    <p class="text-blue-200 font-semibold">Payment Recorded</p>
                    <p class="text-blue-300 text-sm mt-1">
                        Your payment has been recorded. 
                        <?php if($payment->created_at->diffInMinutes(now()) <= 5): ?>
                            <span class="text-blue-100 font-medium">You can edit this payment within 5 minutes</span> to change the amount or payment method.
                        <?php elseif($payment->booking->remaining_balance > 0): ?>
                            You can make another payment to complete your booking balance.
                        <?php else: ?>
                            If you have any concerns, please contact our support team.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Next Steps Section -->
        <?php if($payment->booking->remaining_balance > 0): ?>
        <div class="bg-gradient-to-r from-yellow-900/30 to-orange-900/30 border-2 border-yellow-600/50 rounded-lg p-6 mb-6">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mb-2"></i>
                <h3 class="text-xl font-bold text-yellow-200 mb-2">Complete Your Payment</h3>
                <p class="text-yellow-300 text-sm">
                    You still have a remaining balance of <span class="font-bold text-yellow-100 text-lg">₱<?php echo e(number_format($payment->booking->remaining_balance, 2)); ?></span>
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 mt-4">
                <?php if($payment->created_at->diffInMinutes(now()) <= 5): ?>
                <a href="<?php echo e(route('payments.edit', $payment)); ?>" 
                   class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4 rounded-lg font-bold text-center hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-500/50 transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-edit mr-2"></i>Edit This Payment
                    <span class="block text-xs mt-1 opacity-90">Change amount or method</span>
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('payments.create', $payment->booking)); ?>" 
                   class="flex-1 bg-gradient-to-r from-yellow-600 to-orange-600 text-white px-6 py-4 rounded-lg font-bold text-center hover:from-yellow-700 hover:to-orange-700 focus:ring-4 focus:ring-yellow-500/50 transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-credit-card mr-2"></i>Make Another Payment
                    <span class="block text-xs mt-1 opacity-90">Add new payment</span>
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="<?php echo e(route('guest.bookings.show', $payment->booking)); ?>" 
               class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors">
                <i class="fas fa-eye mr-2"></i>View Booking Details
            </a>
            <a href="<?php echo e(route('payments.history')); ?>" 
               class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium text-center hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors">
                <i class="fas fa-history mr-2"></i>Payment History
            </a>
            <a href="<?php echo e(route('guest.bookings')); ?>" 
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\payments\confirmation.blade.php ENDPATH**/ ?>