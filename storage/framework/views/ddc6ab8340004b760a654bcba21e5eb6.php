<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Staff Management
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Manage staff accounts and permissions
                </p>
                <div class="mt-6">
                    <a href="<?php echo e(route('manager.dashboard')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Staff Management Content -->
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-green-50">Staff Accounts</h3>
                    <div class="text-green-300">
                        Total Staff: <?php echo e($staff->total()); ?>

                    </div>
                </div>

                <!-- Staff Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-green-50">
                        <thead class="bg-green-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left text-green-200 font-medium">Name</th>
                                <th class="px-4 py-3 text-left text-green-200 font-medium">Email</th>
                                <th class="px-4 py-3 text-left text-green-200 font-medium">Phone</th>
                                <th class="px-4 py-3 text-left text-green-200 font-medium">Active Tasks</th>
                                <th class="px-4 py-3 text-left text-green-200 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-green-700/30">
                            <?php $__empty_0 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <tr class="hover:bg-green-800/30">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center mr-3">
                                            <span class="text-white font-medium text-sm"><?php echo e(substr($member->name, 0, 1)); ?></span>
                                        </div>
                                        <?php echo e($member->name); ?>

                                    </div>
                                </td>
                                <td class="px-4 py-3 text-green-300"><?php echo e($member->email); ?></td>
                                <td class="px-4 py-3 text-green-300"><?php echo e($member->phone ?? 'N/A'); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                        $activeTasks = $member->assignedServiceRequests()
                                                             ->whereIn('status', ['assigned', 'in_progress'])
                                                             ->count();
                                    ?>
                                    
                                    <?php if($activeTasks > 0): ?>
                                        <span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-sm">
                                            <?php echo e($activeTasks); ?> tasks
                                        </span>
                                    <?php else: ?>
                                        <span class="text-green-400 text-sm">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            View
                                        </button>
                                        <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Edit
                                        </button>
                                        <a href="<?php echo e(route('manager.staff-assignment.index', ['staff_id' => $member->id])); ?>" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Tasks
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-green-300">
                                    No staff found.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    <?php echo e($staff->links()); ?>

                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\staff.blade.php ENDPATH**/ ?>