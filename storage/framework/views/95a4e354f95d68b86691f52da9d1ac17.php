<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-green-50 mb-2">Maintenance Management</h1>
                <p class="text-green-100">Track maintenance requests and facility status</p>
            </div>

            <!-- Maintenance Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Pending Maintenance -->
                <div class="bg-yellow-600 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-yellow-100 text-sm font-medium">Pending</p>
                            <p class="text-3xl font-bold text-white">
                                <?php echo e(\App\Models\ServiceRequest::where('status', 'pending')->where('service_id', function($query) {
                                    $query->select('id')->from('services')->where('category', 'maintenance');
                                })->count()); ?>

                            </p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="bg-blue-600 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">In Progress</p>
                            <p class="text-3xl font-bold text-white">
                                <?php echo e(\App\Models\ServiceRequest::whereIn('status', ['confirmed', 'in_progress'])->where('service_id', function($query) {
                                    $query->select('id')->from('services')->where('category', 'maintenance');
                                })->count()); ?>

                            </p>
                        </div>
                        <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-green-600 rounded-lg p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Completed</p>
                            <p class="text-3xl font-bold text-white">
                                <?php echo e(\App\Models\ServiceRequest::where('status', 'completed')->where('service_id', function($query) {
                                    $query->select('id')->from('services')->where('category', 'maintenance');
                                })->count()); ?>

                            </p>
                        </div>
                        <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Maintenance Requests List -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Maintenance Requests</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Request
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requested
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php
                                $maintenanceRequests = \App\Models\ServiceRequest::with(['service', 'user', 'guest'])
                                    ->where('service_id', function($query) {
                                        $query->select('id')->from('services')->where('category', 'maintenance');
                                    })
                                    ->orderBy('created_at', 'desc')
                                    ->limit(20)
                                    ->get();
                            ?>
                            
                            <?php $__empty_1 = true; $__currentLoopData = $maintenanceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($request->service->name ?? 'N/A'); ?></div>
                                        <?php if($request->description): ?>
                                            <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo e($request->description); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Room <?php echo e($request->room_number ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php echo e($request->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                               ($request->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')); ?>">
                                            <?php echo e(ucfirst($request->priority)); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php echo e($request->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $request->status))); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($request->created_at->format('M d, Y')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?php echo e(route('manager.service-requests.show', $request->id)); ?>" 
                                           class="text-green-600 hover:text-green-900">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No maintenance requests found
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-green-50 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="<?php echo e(route('manager.service-requests.index')); ?>" 
                       class="bg-green-800 hover:bg-green-700 text-white rounded-lg p-6 shadow-lg transition-colors">
                        <h3 class="font-semibold text-lg mb-2">View All Service Requests</h3>
                        <p class="text-green-100 text-sm">Browse and manage all service requests including maintenance</p>
                    </a>
                    <a href="<?php echo e(route('manager.staff-assignment.index')); ?>" 
                       class="bg-green-800 hover:bg-green-700 text-white rounded-lg p-6 shadow-lg transition-colors">
                        <h3 class="font-semibold text-lg mb-2">Assign Staff</h3>
                        <p class="text-green-100 text-sm">Assign maintenance tasks to staff members</p>
                    </a>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/manager/maintenance/index.blade.php ENDPATH**/ ?>