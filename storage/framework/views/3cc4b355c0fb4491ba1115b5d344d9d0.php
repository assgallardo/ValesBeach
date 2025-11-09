<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <?php if(session('success')): ?>
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            <?php echo e(session('success')); ?>

        </div>
        <?php endif; ?>

        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Booking Header -->
            <div class="bg-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-white">
                        Booking #<?php echo e($booking->id); ?>

                    </h2>
                    <?php echo $booking->status_badge; ?>

                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400">Room</p>
                        <p class="text-white text-lg"><?php echo e($booking->room->name); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Price</p>
                        <p class="text-green-400 text-xl font-bold"><?php echo e($booking->formatted_total_price); ?></p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="p-6 space-y-6">
                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Check-in</h3>
                        <p class="text-white text-lg"><?php echo e($booking->check_in->format('M d, Y')); ?></p>
                        <p class="text-gray-400 text-sm"><?php echo e($booking->check_in->format('l \a\t g:i A')); ?></p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Check-out</h3>
                        <p class="text-white text-lg"><?php echo e($booking->check_out->format('M d, Y')); ?></p>
                        <p class="text-gray-400 text-sm"><?php echo e($booking->check_out->format('l \a\t g:i A')); ?></p>
                    </div>
                </div>

                <!-- Guests -->
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-1">Number of Guests</h3>
                    <p class="text-white"><?php echo e($booking->guests); ?> persons</p>
                </div>

                <!-- Special Requests -->
                <?php if($booking->special_requests): ?>
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-1">Special Requests</h3>
                    <p class="text-white"><?php echo e($booking->special_requests); ?></p>
                </div>
                <?php endif; ?>

                <!-- Room Details -->
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-2">Room Information</h3>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <p class="text-white mb-2"><?php echo e($booking->room->description); ?></p>
                        <p class="text-gray-300">Maximum capacity: <?php echo e($booking->room->max_guests); ?> guests</p>
                    </div>
                </div>

                <!-- Payment Transactions Section -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-medium text-gray-400">Payment Transactions</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            <?php if($booking->payment_status === 'paid'): ?> bg-green-600 text-white
                            <?php elseif($booking->payment_status === 'partial'): ?> bg-yellow-500 text-black
                            <?php else: ?> bg-gray-600 text-white
                            <?php endif; ?>">
                            <?php echo e(ucfirst($booking->payment_status ?? 'unpaid')); ?>

                        </span>
                    </div>

                    <!-- Payment Summary -->
                    <div class="grid grid-cols-3 gap-4 mb-4 p-4 bg-gray-700 rounded-lg">
                        <div class="text-center">
                            <label class="block text-gray-400 text-xs font-medium mb-1">Total Amount</label>
                            <p class="text-white text-lg font-bold">₱<?php echo e(number_format($booking->total_price, 2)); ?></p>
                        </div>
                        <div class="text-center">
                            <label class="block text-gray-400 text-xs font-medium mb-1">Amount Paid</label>
                            <p class="text-green-400 text-lg font-bold">₱<?php echo e(number_format($booking->amount_paid ?? 0, 2)); ?></p>
                        </div>
                        <div class="text-center">
                            <label class="block text-gray-400 text-xs font-medium mb-1">Remaining Balance</label>
                            <p class="text-lg font-bold <?php echo e(($booking->remaining_balance ?? $booking->total_price) > 0 ? 'text-yellow-400' : 'text-green-400'); ?>">
                                ₱<?php echo e(number_format($booking->remaining_balance ?? $booking->total_price, 2)); ?>

                            </p>
                        </div>
                    </div>

                    <!-- Payment Transactions List -->
                    <?php if($booking->payments && $booking->payments->count() > 0): ?>
                        <div class="space-y-3">
                            <h4 class="text-sm font-semibold text-gray-400 mb-3">Payment History (<?php echo e($booking->payments->count()); ?> <?php echo e($booking->payments->count() > 1 ? 'payments' : 'payment'); ?>)</h4>
                            
                            <?php $__currentLoopData = $booking->payments->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-700 rounded-lg p-4 border-l-4 
                                <?php if($payment->status === 'completed'): ?> border-green-500
                                <?php elseif($payment->status === 'confirmed'): ?> border-blue-500
                                <?php elseif($payment->status === 'pending'): ?> border-yellow-500
                                <?php elseif($payment->status === 'overdue'): ?> border-orange-500
                                <?php elseif($payment->status === 'processing'): ?> border-indigo-500
                                <?php elseif($payment->status === 'failed'): ?> border-red-700
                                <?php elseif($payment->status === 'refunded'): ?> border-red-500
                                <?php elseif($payment->status === 'cancelled'): ?> border-gray-600
                                <?php else: ?> border-gray-500
                                <?php endif; ?>">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="text-white font-bold text-lg">₱<?php echo e(number_format($payment->amount, 2)); ?></h5>
                                        <p class="text-gray-300 text-sm"><?php echo e($payment->payment_reference); ?></p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        <?php if($payment->status === 'completed'): ?> bg-green-600 text-white
                                        <?php elseif($payment->status === 'confirmed'): ?> bg-blue-600 text-white
                                        <?php elseif($payment->status === 'pending'): ?> bg-yellow-500 text-black
                                        <?php elseif($payment->status === 'overdue'): ?> bg-orange-600 text-white
                                        <?php elseif($payment->status === 'processing'): ?> bg-indigo-600 text-white
                                        <?php elseif($payment->status === 'failed'): ?> bg-red-700 text-white
                                        <?php elseif($payment->status === 'refunded'): ?> bg-red-600 text-white
                                        <?php elseif($payment->status === 'cancelled'): ?> bg-gray-600 text-white
                                        <?php else: ?> bg-gray-600 text-white
                                        <?php endif; ?>">
                                        <?php echo e(ucfirst($payment->status)); ?>

                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-400">Method:</span>
                                        <p class="text-white">
                                            <?php
                                                $methodIcons = [
                                                    'cash' => 'money-bill-wave',
                                                    'card' => 'credit-card',
                                                    'gcash' => 'mobile-alt',
                                                    'bank_transfer' => 'university',
                                                    'paymaya' => 'mobile-alt',
                                                    'online' => 'globe'
                                                ];
                                                $icon = $methodIcons[$payment->payment_method] ?? 'money-bill';
                                            ?>
                                            <i class="fas fa-<?php echo e($icon); ?> mr-1"></i>
                                            <?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?>

                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-400">Date:</span>
                                        <p class="text-white"><?php echo e($payment->created_at->format('M d, Y g:i A')); ?></p>
                                    </div>
                                </div>

                                <?php if($payment->notes): ?>
                                <div class="mt-2 pt-2 border-t border-gray-600">
                                    <span class="text-gray-400 text-xs">Notes:</span>
                                    <p class="text-white text-sm mt-1"><?php echo e($payment->notes); ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if($payment->transaction_id): ?>
                                <div class="mt-2">
                                    <span class="text-gray-400 text-xs">Transaction ID:</span>
                                    <p class="text-white text-sm"><?php echo e($payment->transaction_id); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6 bg-gray-700 rounded-lg">
                            <i class="fas fa-receipt text-gray-500 text-4xl mb-2"></i>
                            <p class="text-gray-400">No payments recorded yet for this booking.</p>
                            <?php if($booking->remaining_balance > 0 && !in_array($booking->status, ['cancelled', 'completed'])): ?>
                                <a href="<?php echo e(route('payments.create', $booking)); ?>" 
                                   class="inline-block mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Make Payment
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-600">
                    <a href="<?php echo e(route('guest.bookings')); ?>" 
                       class="text-gray-400 hover:text-white transition-colors duration-200">
                        ← Back to My Bookings
                    </a>
                    <?php if(in_array($booking->status, ['pending', 'confirmed'])): ?>
                    <form action="<?php echo e(route('guest.bookings.cancel', $booking)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            Cancel Booking
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/bookings/show.blade.php ENDPATH**/ ?>