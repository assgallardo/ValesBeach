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