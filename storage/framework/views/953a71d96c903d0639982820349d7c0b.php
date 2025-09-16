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
                                Check-in: <?php echo e($booking->check_in->format('M d, Y')); ?>

                            </p>
                            <p class="text-gray-300">
                                Check-out: <?php echo e($booking->check_out->format('M d, Y')); ?>

                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-white">
                                <?php echo e($booking->formatted_total_price); ?>

                            </p>
                            <p class="text-sm text-gray-400">
                                Status: <span class="text-green-400"><?php echo e($booking->status); ?></span>
                            </p>
                        </div>
                    </div>

                    <?php if($booking->status === 'pending' || $booking->status === 'confirmed'): ?>
                        <div class="mt-4 flex justify-end">
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

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\valesbeach\resources\views/guest/bookings/index.blade.php ENDPATH**/ ?>