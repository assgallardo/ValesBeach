<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-green-50 mb-2">Guest Management</h1>
                <p class="text-green-100">View guest information and booking history</p>
            </div>

            <!-- Guests List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__empty_0 = true; $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($guest->name); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?php echo e($guest->email); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo e($guest->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo e(ucfirst($guest->status)); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($guest->created_at->format('M d, Y')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No guests found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($guests->hasPages()): ?>
                    <div class="bg-gray-50 px-6 py-4">
                        <?php echo e($guests->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\guests\index.blade.php ENDPATH**/ ?>