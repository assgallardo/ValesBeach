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
                    <p class="text-gray-400 text-sm"><?php echo e($booking->check_in->format('l \a\t g:i A')); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Check-out Date</label>
                    <p class="text-green-50 text-lg"><?php echo e($booking->check_out->format('F d, Y')); ?></p>
                    <p class="text-gray-400 text-sm"><?php echo e($booking->check_out->format('l \a\t g:i A')); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Nights</label>
                    <p class="text-green-50"><?php echo e($booking->check_in->diffInDays($booking->check_out)); ?></p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Guests</label>
                    <p class="text-green-50"><?php echo e($booking->guests); ?></p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Payment Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Total Amount</label>
                    <p class="text-green-400 text-2xl font-bold"><?php echo e($booking->formatted_total_price); ?></p>
                    <p class="text-gray-400 text-sm">
                        ₱<?php echo e(number_format($booking->room->price, 2)); ?> × <?php echo e($booking->check_in->diffInDays($booking->check_out)); ?> night(s)
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

    <!-- Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-green-50 mb-4">Update Status</h2>
        <form action="<?php echo e(route('admin.bookings.status', $booking)); ?>" method="POST" class="flex items-center space-x-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <select name="status" 
                    class="px-4 py-2 bg-gray-700 text-white rounded border border-gray-600 focus:border-green-500">
                <?php $__currentLoopData = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\reservations\show.blade.php ENDPATH**/ ?>