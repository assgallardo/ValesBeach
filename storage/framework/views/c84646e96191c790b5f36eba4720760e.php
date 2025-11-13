

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        <i class="fas fa-broom mr-3 text-blue-400"></i>Housekeeping Management
                    </h1>
                    <p class="text-gray-400">Manage room cleaning and maintenance requests</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">Last Updated</div>
                    <div class="text-white font-semibold"><?php echo e(now()->format('M d, Y H:i')); ?></div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
        <div class="mb-6 bg-green-900/30 border border-green-600/30 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-3 text-lg"></i>
                <span class="text-green-100"><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($stats['total']); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Total Requests</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($stats['pending']); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Pending</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-blue-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($stats['assigned']); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Assigned</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner text-purple-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($stats['in_progress']); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">In Progress</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-double text-green-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($stats['completed_today']); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Completed Today</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
            <form method="GET" action="<?php echo e(route(auth()->user()->role . '.housekeeping.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" <?php echo e(request('status') == 'all' ? 'selected' : ''); ?>>All Status</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="assigned" <?php echo e(request('status') == 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                        <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Priority</label>
                    <select name="priority" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" <?php echo e(request('priority') == 'all' ? 'selected' : ''); ?>>All Priority</option>
                        <option value="urgent" <?php echo e(request('priority') == 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                        <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>High</option>
                        <option value="normal" <?php echo e(request('priority') == 'normal' ? 'selected' : ''); ?>>Normal</option>
                        <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>Low</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Date From</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-2">Date To</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-4 flex justify-end gap-3">
                    <a href="<?php echo e(route(auth()->user()->role . '.housekeeping.index')); ?>" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Clear Filters
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Housekeeping Requests Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Request ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Triggered</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-white font-semibold">#<?php echo e($request->id); ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white font-medium"><?php echo e($request->room->name ?? 'N/A'); ?></div>
                                <div class="text-gray-400 text-sm"><?php echo e($request->room->room_type ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white"><?php echo e($request->booking->user->name ?? 'N/A'); ?></div>
                                <div class="text-gray-400 text-sm">Booking #<?php echo e($request->booking_id); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    <?php if($request->priority === 'urgent'): ?> bg-red-900/30 text-red-400 border border-red-600/30
                                    <?php elseif($request->priority === 'high'): ?> bg-orange-900/30 text-orange-400 border border-orange-600/30
                                    <?php elseif($request->priority === 'normal'): ?> bg-blue-900/30 text-blue-400 border border-blue-600/30
                                    <?php else: ?> bg-gray-900/30 text-gray-400 border border-gray-600/30
                                    <?php endif; ?>">
                                    <?php echo e($request->formatted_priority); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    <?php if($request->status === 'completed'): ?> bg-green-900/30 text-green-400 border border-green-600/30
                                    <?php elseif($request->status === 'in_progress'): ?> bg-purple-900/30 text-purple-400 border border-purple-600/30
                                    <?php elseif($request->status === 'assigned'): ?> bg-blue-900/30 text-blue-400 border border-blue-600/30
                                    <?php elseif($request->status === 'pending'): ?> bg-yellow-900/30 text-yellow-400 border border-yellow-600/30
                                    <?php else: ?> bg-gray-900/30 text-gray-400 border border-gray-600/30
                                    <?php endif; ?>">
                                    <?php echo e($request->formatted_status); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($request->assignedTo): ?>
                                    <div class="text-white"><?php echo e($request->assignedTo->name); ?></div>
                                <?php else: ?>
                                    <span class="text-gray-500 italic">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">
                                <?php echo e($request->triggered_at->format('M d, Y H:i')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">
                                    <?php if($request->status !== 'completed' && $request->status !== 'cancelled'): ?>
                                        <button onclick="openAssignModal(<?php echo e($request->id); ?>)" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-user-plus mr-1"></i>Assign
                                        </button>
                                        <button onclick="openStatusModal(<?php echo e($request->id); ?>)" class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition-colors">
                                            <i class="fas fa-edit mr-1"></i>Update
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-5xl mb-4 block"></i>
                                <p class="text-lg">No housekeeping requests found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($requests->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-700">
                <?php echo e($requests->links()); ?>

            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">Assign Housekeeping Request</h3>
        <form id="assignForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">Select Staff Member</label>
                <select name="assigned_to" required class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2">
                    <option value="">Choose staff...</option>
                    <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($member->id); ?>"><?php echo e($member->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Assign
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">Update Status</h3>
        <form id="statusForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">New Status</label>
                <select name="status" required class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2">
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">Completion Notes (Optional)</label>
                <textarea name="completion_notes" rows="3" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-4 py-2"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignModal(requestId) {
    const form = document.getElementById('assignForm');
    form.action = `/<?php echo e(auth()->user()->role); ?>/housekeeping/${requestId}/assign`;
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

function openStatusModal(requestId) {
    const form = document.getElementById('statusForm');
    form.action = `/<?php echo e(auth()->user()->role); ?>/housekeeping/${requestId}/status`;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\housekeeping\index.blade.php ENDPATH**/ ?>