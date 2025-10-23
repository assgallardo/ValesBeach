<?php $__env->startSection('content'); ?>
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Service Requests Management
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                View and manage all guest service requests
            </p>
            <div class="mt-6">
                <a href="<?php echo e(route('manager.dashboard')); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
        <div class="bg-green-600 text-white px-6 py-4 rounded-lg mb-6 shadow-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
        <?php endif; ?>

        <!-- Filters Section -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8 border border-green-700/30">
            <form method="GET" action="<?php echo e(route('manager.service-requests.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-green-50 text-sm font-medium mb-2">Status</label>
                    <select name="status" class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="">All Statuses</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst($status)); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Service Filter -->
                <div>
                    <label class="block text-green-50 text-sm font-medium mb-2">Service</label>
                    <select name="service_id" class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                        <option value="">All Services</option>
                        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($service->id); ?>" <?php echo e(request('service_id') == $service->id ? 'selected' : ''); ?>>
                            <?php echo e($service->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-green-50 text-sm font-medium mb-2">Date</label>
                    <input type="date" name="date" value="<?php echo e(request('date')); ?>"
                           class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-600/80 backdrop-blur-sm rounded-lg p-6 border border-blue-500/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Pending</p>
                        <p class="text-3xl font-bold text-white"><?php echo e($serviceRequests->where('status', 'pending')->count()); ?></p>
                    </div>
                    <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-yellow-600/80 backdrop-blur-sm rounded-lg p-6 border border-yellow-500/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm">In Progress</p>
                        <p class="text-3xl font-bold text-white"><?php echo e($serviceRequests->where('status', 'in_progress')->count()); ?></p>
                    </div>
                    <svg class="w-12 h-12 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-green-600/80 backdrop-blur-sm rounded-lg p-6 border border-green-500/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Completed</p>
                        <p class="text-3xl font-bold text-white"><?php echo e($serviceRequests->where('status', 'completed')->count()); ?></p>
                    </div>
                    <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-purple-600/80 backdrop-blur-sm rounded-lg p-6 border border-purple-500/30">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Total Requests</p>
                        <p class="text-3xl font-bold text-white"><?php echo e($serviceRequests->total()); ?></p>
                    </div>
                    <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Service Requests Table -->
        <?php if($serviceRequests->count() > 0): ?>
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-green-800/70">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Guest</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Service</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Room</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Priority</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Requested At</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-green-50">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-700/30">
                        <?php $__currentLoopData = $serviceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-green-800/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-green-50">#<?php echo e($request->id); ?></td>
                            <td class="px-6 py-4">
                                <div class="text-green-50 font-medium"><?php echo e($request->guest_name); ?></div>
                                <div class="text-green-200 text-sm"><?php echo e($request->guest_email); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-50"><?php echo e($request->service ? $request->service->name : 'N/A'); ?></div>
                                <?php if($request->service_type): ?>
                                <div class="text-green-200 text-sm"><?php echo e(ucfirst($request->service_type)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-green-50"><?php echo e($request->room_number); ?></td>
                            <td class="px-6 py-4">
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-blue-600 text-blue-100',
                                        'assigned' => 'bg-purple-600 text-purple-100',
                                        'in_progress' => 'bg-yellow-600 text-yellow-100',
                                        'completed' => 'bg-green-600 text-green-100',
                                        'cancelled' => 'bg-red-600 text-red-100'
                                    ];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo e($statusColors[$request->status] ?? 'bg-gray-600 text-gray-100'); ?>">
                                    <?php echo e(ucfirst($request->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $priorityColors = [
                                        'low' => 'bg-gray-600 text-gray-100',
                                        'normal' => 'bg-blue-600 text-blue-100',
                                        'high' => 'bg-orange-600 text-orange-100',
                                        'urgent' => 'bg-red-600 text-red-100'
                                    ];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo e($priorityColors[$request->priority] ?? 'bg-gray-600 text-gray-100'); ?>">
                                    <?php echo e(ucfirst($request->priority)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-green-50 text-sm">
                                <?php echo e($request->requested_at ? $request->requested_at->format('M d, Y H:i') : 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="<?php echo e(route('manager.service-requests.show', $request)); ?>" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition-colors duration-200">
                                        View
                                    </a>
                                    <?php if($request->status !== 'completed' && $request->status !== 'cancelled'): ?>
                                    <button onclick="openStatusModal(<?php echo e($request->id); ?>, '<?php echo e($request->status); ?>')"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm transition-colors duration-200">
                                        Update
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-green-800/50">
                <?php echo e($serviceRequests->links()); ?>

            </div>
        </div>
        <?php else: ?>
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-12 text-center border border-green-700/30">
            <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-xl font-semibold text-green-50 mb-2">No Service Requests Found</h3>
            <p class="text-green-200">No service requests match your current filters.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-green-900 rounded-lg p-8 max-w-md w-full mx-4 border border-green-700">
        <h3 class="text-2xl font-bold text-green-50 mb-6">Update Service Request</h3>
        
        <form id="statusForm" method="POST" action="">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            
            <!-- Status -->
            <div class="mb-4">
                <label class="block text-green-50 text-sm font-medium mb-2">Status</label>
                <select name="status" id="statusSelect" required
                        class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Assign Staff -->
            <div class="mb-4">
                <label class="block text-green-50 text-sm font-medium mb-2">Assign to Staff (Optional)</label>
                <select name="assigned_to" class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500">
                    <option value="">None</option>
                    <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($member->id); ?>"><?php echo e($member->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-green-50 text-sm font-medium mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" 
                          class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="button" onclick="closeStatusModal()"
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openStatusModal(requestId, currentStatus) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('statusSelect');
    
    form.action = `/manager/service-requests/${requestId}/status`;
    statusSelect.value = currentStatus;
    modal.style.display = 'flex';
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.style.display = 'none';
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeStatusModal();
    }
});

// Close modal on outside click
document.getElementById('statusModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeStatusModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/manager/service-requests/index.blade.php ENDPATH**/ ?>