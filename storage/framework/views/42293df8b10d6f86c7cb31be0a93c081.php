<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Booking History</h1>
    <?php if($bookings->isEmpty()): ?>
        <p class="text-gray-300">No booking history to display yet.</p>
    <?php else: ?>
        <div class="grid gap-6">
            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-green-900/50 rounded-lg p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                <?php echo e($booking->room->name ?? 'Room'); ?>

                            </h3>
                            <p class="text-gray-300">
                                Check-in: <?php echo e(\Carbon\Carbon::parse($booking->check_in ?? $booking->check_in_date)->format('M d, Y \a\t g:i A')); ?>

                            </p>
                            <p class="text-gray-300">
                                Check-out: <?php echo e(\Carbon\Carbon::parse($booking->check_out ?? $booking->check_out_date)->format('M d, Y \a\t g:i A')); ?>

                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                ₱<?php echo e(number_format($booking->total_price, 2)); ?>

                            </p>
                            <p class="text-sm text-gray-400 mt-2">
                                Status: <span class="capitalize px-2 py-1 rounded text-xs font-medium
                                    <?php if($booking->status === 'checked_out'): ?> bg-gray-600 text-white
                                    <?php elseif($booking->status === 'cancelled'): ?> bg-red-600 text-white
                                    <?php else: ?> bg-gray-600 text-white <?php endif; ?>">
                                    <?php echo e(str_replace('_', ' ', $booking->status)); ?>

                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                           class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        <?php if(isset($booking->invoice)): ?>
                        <a href="<?php echo e(route('invoices.show', $booking->invoice)); ?>" 
                           class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                            <i class="fas fa-file-invoice mr-1"></i>Invoice
                        </a>
                        <?php endif; ?>
                        <?php if($booking->status === 'cancelled'): ?>
                        <form action="<?php echo e(route('guest.bookings.destroy', $booking->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this cancelled booking?')" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-3 py-2 bg-red-700 text-white text-sm rounded hover:bg-red-800" title="Delete booking">
                                &times;
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="mt-6">
            <?php echo e($bookings->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/guest/bookings/history.blade.php ENDPATH**/ ?>