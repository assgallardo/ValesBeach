<?php $__env->startSection('content'); ?>
<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Guests Management
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                View and manage guest information and booking history.
            </p>
            <div class="mt-6">
                <a href="<?php echo e(route('manager.dashboard')); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Guest List -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-green-50 mb-4">All Guests</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-green-50">
                    <thead>
                        <tr class="border-b border-green-700">
                            <th class="text-left py-3">Name</th>
                            <th class="text-left py-3">Email</th>
                            <th class="text-left py-3">Phone</th>
                            <th class="text-left py-3">Status</th>
                            <th class="text-left py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="border-b border-green-800">
                            <td class="py-3"><?php echo e($guest->name); ?></td>
                            <td class="py-3"><?php echo e($guest->email); ?></td>
                            <td class="py-3"><?php echo e($guest->phone ?? 'N/A'); ?></td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-green-600 text-green-100 rounded text-sm">
                                    <?php echo e(ucfirst($guest->status ?? 'active')); ?>

                                </span>
                            </td>
                            <td class="py-3"><?php echo e($guest->created_at->format('M d, Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="5" class="text-center py-8 text-green-300">No guests found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if($guests->hasPages()): ?>
        <div class="flex justify-center">
            <?php echo e($guests->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\guests.blade.php ENDPATH**/ ?>