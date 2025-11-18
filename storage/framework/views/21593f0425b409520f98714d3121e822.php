<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header with View History Button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">My Bookings</h1>
        <a href="<?php echo e(route('guest.bookings.history')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-history mr-2"></i>
            View History
        </a>
    </div>

    <?php if($bookings->isEmpty()): ?>
        <div class="bg-green-900/50 rounded-lg p-8 text-center">
            <p class="text-gray-300 text-lg">You haven't made any bookings yet.</p>
            <a href="<?php echo e(route('guest.rooms.browse')); ?>" 
               class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Browse Rooms
            </a>
        </div>
    <?php else: ?>
        <div class="grid gap-6">
            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-green-900/50 rounded-lg p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                <?php echo e($booking->room->name); ?>

                            </h3>
                            <p class="text-gray-300">
                                Check-in: <?php echo e($booking->check_in->format('M d, Y')); ?> at <?php echo e($booking->room->check_in_time ? \Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A') : '12:00 AM'); ?>

                            </p>
                            <p class="text-gray-300">
                                Check-out: <?php echo e($booking->check_out->format('M d, Y')); ?> at <?php echo e($booking->room->check_out_time ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A') : '12:00 AM'); ?>

                            </p>
                            <?php if($booking->early_checkin || $booking->late_checkout): ?>
                            <div class="mt-2 space-y-1">
                                <?php if($booking->early_checkin): ?>
                                <p class="text-green-400 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Early Check-in Requested <?php if($booking->early_checkin_time): ?>(<?php echo e(\Carbon\Carbon::parse($booking->early_checkin_time)->format('g:i A')); ?>)<?php endif; ?>
                                </p>
                                <?php endif; ?>
                                <?php if($booking->late_checkout): ?>
                                <p class="text-yellow-400 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Late Check-out Requested <?php if($booking->late_checkout_time): ?>(<?php echo e(\Carbon\Carbon::parse($booking->late_checkout_time)->format('g:i A')); ?>)<?php endif; ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                <?php
                                    // Compute a reliable fallback total for display
                                    // Same-day bookings count as 1 night/day
                                    $checkInVal = $booking->check_in ?? null;
                                    $checkOutVal = $booking->check_out ?? null;
                                    $checkIn = $checkInVal ? \Carbon\Carbon::parse($checkInVal)->startOfDay() : null;
                                    $checkOut = $checkOutVal ? \Carbon\Carbon::parse($checkOutVal)->startOfDay() : null;
                                    $nights = ($checkIn && $checkOut) ? $checkIn->diffInDays($checkOut) : 0;
                                    // diffInDays returns float, use == not ===
                                    if ($nights == 0) { $nights = 1; }
                                    $roomPrice = optional($booking->room)->price ?? 0;
                                    $fallbackTotal = $roomPrice * $nights;
                                    $rawTotal = (float)($booking->total_price ?? 0);
                                    $displayTotal = $rawTotal > 0 ? $rawTotal : $fallbackTotal;
                                    
                                    // Calculate total refunds for this booking
                                    $totalRefunded = $booking->payments->sum('refund_amount');
                                ?>
                                <?php echo e('₱' . number_format($displayTotal, 2)); ?>

                            </p>
                            <?php if($totalRefunded > 0): ?>
                                <p class="text-sm font-semibold text-red-400 mt-1">
                                    Refunded: ₱<?php echo e(number_format($totalRefunded, 2)); ?>

                                </p>
                            <?php endif; ?>
                            <p class="text-sm text-gray-400 mt-2">
                                Status: <span class="capitalize px-2 py-1 rounded text-xs font-medium
                                    <?php if($booking->status === 'confirmed'): ?> bg-green-600 text-white
                                    <?php elseif($booking->status === 'pending'): ?> bg-yellow-600 text-white
                                    <?php elseif($booking->status === 'checked_in'): ?> bg-blue-600 text-white
                                    <?php elseif($booking->status === 'checked_out'): ?> bg-gray-600 text-white
                                    <?php elseif($booking->status === 'cancelled'): ?> bg-red-600 text-white
                                    <?php else: ?> bg-gray-600 text-white <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $booking->status)); ?>

                                </span>
                            </p>
                        </div>
                    </div>

                    <?php if($booking->status === 'pending' || $booking->status === 'confirmed'): ?>
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <!-- View Details Button -->
                            <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                               class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            
                            <!-- Payment Button (if payment is needed) -->
                            <?php if($booking->remaining_balance > 0): ?>
                            <a href="<?php echo e(route('payments.create', $booking)); ?>" 
                               class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-credit-card mr-1"></i>Pay Now
                            </a>
                            <?php endif; ?>
                            
                            <!-- Invoice Button (if invoice exists) -->
                            <?php if($booking->invoice): ?>
                            <a href="<?php echo e(route('invoices.show', $booking->invoice)); ?>" 
                               class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                <i class="fas fa-file-invoice mr-1"></i>Invoice
                            </a>
                            <?php endif; ?>
                            
                            <!-- Cancel Booking Button -->
                            <form action="<?php echo e(route('guest.bookings.cancel', $booking)); ?>" 
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?')"
                                  class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" 
                                        class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- View Details Button for other statuses -->
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                               class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            
                            <!-- Invoice Button (if invoice exists) -->
                            <?php if($booking->invoice): ?>
                            <a href="<?php echo e(route('invoices.show', $booking->invoice)); ?>" 
                               class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                <i class="fas fa-file-invoice mr-1"></i>Invoice
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-6">
            <?php echo e($bookings->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/bookings/index.blade.php ENDPATH**/ ?>