<?php $__env->startSection('content'); ?>
    <header class="bg-green-900 shadow">
        <div class="container mx-auto px-4 lg:px-16 py-6">
            <h2 class="text-2xl font-semibold text-white">
                Reservation Details
            </h2>
        </div>
    </header>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="<?php echo e(route('admin.reservations')); ?>" class="inline-flex items-center text-green-100 hover:text-green-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Reservations
        </a>
    </div>

    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50">Booking Details</h1>
        <p class="text-green-100 mt-2">Booking #<?php echo e($booking->id); ?></p>
    </div>

    <!-- Booking Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Guest Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Guest Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Name</label>
                    <p class="text-green-50"><?php echo e($booking->user->name); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Email</label>
                    <p class="text-green-50"><?php echo e($booking->user->email); ?></p>
                </div>
            </div>
        </div>

        <!-- Room Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Room Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Room Name</label>
                    <p class="text-green-50"><?php echo e($booking->room->name); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Room Type</label>
                    <p class="text-green-50"><?php echo e(ucfirst($booking->room->type)); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Price per Night</label>
                    <p class="text-green-50">₱<?php echo e(number_format($booking->room->price, 2)); ?></p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Booking Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Check-in Date</label>
                    <p class="text-green-50 text-lg"><?php echo e($booking->check_in->format('F d, Y')); ?></p>
                    <p class="text-gray-400 text-sm"><?php echo e($booking->check_in->format('l')); ?></p>
                    <?php if($booking->room->check_in_time && $booking->room->check_in_time !== '00:00:00'): ?>
                    <p class="text-green-400 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i>Check-in time: <?php echo e(\Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A')); ?>

                    </p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="text-sm text-green-200">Check-out Date</label>
                    <p class="text-green-50 text-lg"><?php echo e($booking->check_out->format('F d, Y')); ?></p>
                    <p class="text-gray-400 text-sm"><?php echo e($booking->check_out->format('l')); ?></p>
                    <?php if($booking->room->check_out_time && $booking->room->check_out_time !== '00:00:00'): ?>
                    <p class="text-yellow-400 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i>Check-out time: <?php echo e(\Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A')); ?>

                    </p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Nights</label>
                    <p class="text-green-50">
                        <?php
                            $checkIn = $booking->check_in->copy()->startOfDay();
                            $checkOut = $booking->check_out->copy()->startOfDay();
                            $daysDiff = $checkIn->diffInDays($checkOut);
                            $nights = $daysDiff === 0 ? 1 : $daysDiff;
                        ?>
                        <?php echo e($nights); ?>

                    </p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Guests</label>
                    <p class="text-green-50"><?php echo e($booking->guests); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Status</label>
                    <p class="text-green-50">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            <?php if($booking->status === 'confirmed'): ?> bg-green-600 text-white
                            <?php elseif($booking->status === 'pending'): ?> bg-yellow-500 text-black
                            <?php elseif($booking->status === 'cancelled'): ?> bg-red-600 text-white
                            <?php elseif($booking->status === 'completed'): ?> bg-blue-600 text-white
                            <?php else: ?> bg-gray-600 text-white
                            <?php endif; ?>">
                            <?php echo e(ucfirst($booking->status)); ?>

                        </span>
                    </p>
                </div>

                <?php if($booking->early_checkin || $booking->late_checkout): ?>
                <div class="border-t border-gray-700 pt-4 mt-4">
                    <label class="text-sm text-green-200 mb-2 block">Special Timing Requests</label>
                    
                    <?php if($booking->early_checkin): ?>
                    <div class="bg-green-800 bg-opacity-30 border border-green-700 rounded-lg p-3 mb-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-white font-medium">Early Check-in</p>
                                <?php if($booking->early_checkin_time): ?>
                                <p class="text-green-300 text-sm">Time: <?php echo e(\Carbon\Carbon::parse($booking->early_checkin_time)->format('g:i A')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($booking->late_checkout): ?>
                    <div class="bg-yellow-800 bg-opacity-30 border border-yellow-700 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-white font-medium">Late Check-out</p>
                                <?php if($booking->late_checkout_time): ?>
                                <p class="text-yellow-300 text-sm">Time: <?php echo e(\Carbon\Carbon::parse($booking->late_checkout_time)->format('g:i A')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Payment Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Room Charges</label>
                    <p class="text-white text-lg"><?php echo e($booking->formatted_total_price); ?></p>
                    <p class="text-gray-400 text-sm">
                        <?php
                            $checkIn = $booking->check_in->copy()->startOfDay();
                            $checkOut = $booking->check_out->copy()->startOfDay();
                            $daysDiff = $checkIn->diffInDays($checkOut);
                            $nights = $daysDiff === 0 ? 1 : $daysDiff;
                        ?>
                        ₱<?php echo e(number_format($booking->room->price, 2)); ?> × <?php echo e($nights); ?> night(s)
                    </p>
                </div>

                <div>
                    <label class="text-sm text-green-200">Status</label>
                    <span class="inline-block px-3 py-1 rounded-full text-sm mt-1
                        <?php if($booking->status === 'confirmed'): ?> bg-green-500 text-white
                        <?php elseif($booking->status === 'pending'): ?> bg-yellow-500 text-black
                        <?php elseif($booking->status === 'cancelled'): ?> bg-red-500 text-white
                        <?php elseif($booking->status === 'checked_in'): ?> bg-blue-500 text-white
                        <?php else: ?> bg-gray-500 text-white
                        <?php endif; ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Requests -->
    <?php if($booking->special_requests): ?>
    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-green-50 mb-4">Special Requests</h2>
        <p class="text-green-100"><?php echo e($booking->special_requests); ?></p>
    </div>
    <?php endif; ?>

    <!-- Payment Transactions Section -->
    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-green-50">Payment Transactions</h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                <?php if($booking->payment_status === 'paid'): ?> bg-green-600 text-white
                <?php elseif($booking->payment_status === 'partial'): ?> bg-yellow-500 text-black
                <?php else: ?> bg-gray-600 text-white
                <?php endif; ?>">
                <?php echo e(ucfirst($booking->payment_status ?? 'unpaid')); ?>

            </span>
        </div>

        <!-- Payment Summary -->
        <div class="grid grid-cols-3 gap-4 mb-6 p-4 bg-gray-700 rounded-lg">
            <div class="text-center">
                <label class="block text-gray-400 text-sm font-medium mb-1">Total Amount</label>
                <p class="text-green-50 text-xl font-bold">₱<?php echo e(number_format($booking->total_price, 2)); ?></p>
            </div>
            <div class="text-center">
                <label class="block text-gray-400 text-sm font-medium mb-1">Amount Paid</label>
                <p class="text-green-400 text-xl font-bold">₱<?php echo e(number_format($booking->amount_paid ?? 0, 2)); ?></p>
            </div>
            <div class="text-center">
                <label class="block text-gray-400 text-sm font-medium mb-1">Remaining Balance</label>
                <p class="text-xl font-bold <?php echo e(($booking->remaining_balance ?? $booking->total_price) > 0 ? 'text-yellow-400' : 'text-green-400'); ?>">
                    ₱<?php echo e(number_format($booking->remaining_balance ?? $booking->total_price, 2)); ?>

                </p>
            </div>
        </div>

        <!-- Payment Transactions List -->
        <?php if($booking->payments && $booking->payments->count() > 0): ?>
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-green-50 mb-4">Payment History (<?php echo e($booking->payments->count()); ?> <?php echo e($booking->payments->count() > 1 ? 'payments' : 'payment'); ?>)</h4>
                
                <?php $__currentLoopData = $booking->payments->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-700 rounded-lg p-4 border-l-4 
                    <?php if($payment->status === 'completed'): ?> border-green-500
                    <?php elseif($payment->status === 'pending'): ?> border-yellow-500
                    <?php elseif($payment->status === 'refunded'): ?> border-red-500
                    <?php else: ?> border-gray-500
                    <?php endif; ?>">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h5 class="text-green-50 font-bold text-lg">₱<?php echo e(number_format($payment->amount, 2)); ?></h5>
                            <p class="text-green-200 text-sm"><?php echo e($payment->payment_reference); ?></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            <?php if($payment->status === 'completed'): ?> bg-green-600 text-white
                            <?php elseif($payment->status === 'pending'): ?> bg-yellow-500 text-black
                            <?php elseif($payment->status === 'refunded'): ?> bg-red-600 text-white
                            <?php else: ?> bg-gray-600 text-white
                            <?php endif; ?>">
                            <?php echo e(ucfirst($payment->status)); ?>

                        </span>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-gray-400">Method:</span>
                            <p class="text-green-50 font-medium">
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
                            <p class="text-green-50"><?php echo e($payment->created_at->format('M d, Y')); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-400">Time:</span>
                            <p class="text-green-50"><?php echo e($payment->created_at->format('g:i A')); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-400">Paid by:</span>
                            <p class="text-green-50"><?php echo e($payment->user->name); ?></p>
                        </div>
                    </div>

                    <?php if($payment->notes): ?>
                    <div class="mt-3 pt-3 border-t border-gray-600">
                        <span class="text-gray-400 text-sm">Notes:</span>
                        <p class="text-green-50 text-sm mt-1"><?php echo e($payment->notes); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($payment->transaction_id): ?>
                    <div class="mt-2">
                        <span class="text-gray-400 text-sm">Transaction ID:</span>
                        <p class="text-green-50 text-sm"><?php echo e($payment->transaction_id); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <i class="fas fa-receipt text-gray-600 text-5xl mb-3"></i>
                <p class="text-gray-400 text-lg">No payments recorded yet for this booking.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-green-50 mb-4">Update Status</h2>
        <form action="<?php echo e(route('admin.bookings.status', $booking)); ?>" method="POST" class="flex items-center space-x-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <select name="status" 
                    class="px-4 py-2 bg-gray-700 text-white rounded border border-gray-600 focus:border-green-500">
                <?php $__currentLoopData = ['pending', 'confirmed', 'checked_in', 'checked_out', 'completed', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>" <?php echo e($booking->status === $status ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 transition-colors">
                Update Status
            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/admin/bookings/show.blade.php ENDPATH**/ ?>