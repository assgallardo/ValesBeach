<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">My Bookings</h1>

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
                                Check-in: <?php echo e($booking->check_in->format('M d, Y \a\t g:i A')); ?>

                            </p>
                            <p class="text-gray-300">
                                Check-out: <?php echo e($booking->check_out->format('M d, Y \a\t g:i A')); ?>

                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                <?php echo e($booking->formatted_total_price); ?>

                            </p>
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
                        <div class="mt-4 flex justify-end gap-3">
                            <!-- View Details Button -->
                            <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                View Details
                            </a>
                            
                            <!-- Cancel Booking Button -->
                            <form action="<?php echo e(route('guest.bookings.cancel', $booking)); ?>" 
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    Cancel Booking
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- View Details Button for other statuses -->
                        <div class="mt-4 flex justify-end">
                            <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                View Details
                            </a>
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