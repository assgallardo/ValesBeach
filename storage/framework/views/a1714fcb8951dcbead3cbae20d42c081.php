

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50 mb-2">Service Requests Management</h1>
            
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($pendingRequests); ?></div>
            <div class="text-sm opacity-90">Pending</div>
        </div>
        <div class="bg-blue-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($assignedRequests); ?></div>
            <div class="text-sm opacity-90">Assigned</div>
        </div>
        <div class="bg-green-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($completedRequests); ?></div>
            <div class="text-sm opacity-90">Completed</div>
        </div>
        <div class="bg-orange-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($overdueRequests ?? 0); ?></div>
            <div class="text-sm opacity-90">Overdue</div>
        </div>
    </div>

    <!-- Simple Filter Bar -->
    <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
            <select id="filterStatus" class="bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
            
            <select id="filterStaff" class="bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                <option value="">All Staff</option>
                <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($staff->id); ?>"><?php echo e($staff->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-500">
                Clear Filters
            </button>
            
            <div class="ml-auto flex gap-3">
                <button onclick="toggleCompletedTasks()" id="completedTasksBtn" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Completed Tasks
                    <span class="ml-2 bg-green-800 px-2 py-0.5 rounded-full text-xs font-bold"><?php echo e($completedRequests + $completedHousekeeping); ?></span>
                </button>
                <button onclick="toggleBulkMode()" id="bulkModeBtn" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Bulk Actions
                </button>
            </div>
        </div>
        
        <!-- Bulk Actions Panel (Hidden by default) -->
        <div id="bulkActionsPanel" class="hidden mt-4 p-3 bg-gray-700 rounded">
            <div class="flex gap-3 items-center">
                <span class="text-green-100 text-sm">Selected: <span id="selectedCount">0</span> items</span>
                <select id="bulkStaff" class="bg-gray-600 text-green-100 rounded px-3 py-2 text-sm">
                    <option value="">Assign to...</option>
                    <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($staff->id); ?>"><?php echo e($staff->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <input type="datetime-local" id="bulkDeadline" class="bg-gray-600 text-green-100 rounded px-3 py-2 text-sm">
                <button onclick="bulkAssign()" class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                    Apply
                </button>
                <button onclick="bulkCancel()" class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700">
                    Cancel Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Housekeeping Tasks Section -->
    <?php if($housekeepingTasks->count() > 0): ?>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-purple-100 mb-4 flex items-center">
            <i class="fas fa-broom mr-3"></i>
            Active Housekeeping Tasks
            <span class="ml-4 text-sm font-normal text-gray-400">
                Pending: <?php echo e($pendingHousekeeping); ?> | Assigned: <?php echo e($assignedHousekeeping); ?> | Completed: <?php echo e($completedHousekeeping); ?>

            </span>
        </h2>
        <div class="space-y-4">
            <?php $__currentLoopData = $housekeepingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-gray-800 border-l-4 <?php echo e($task->status === 'completed' ? 'border-green-600 opacity-75' : 'border-purple-600'); ?> rounded-lg p-6 hover:bg-gray-750 transition-colors">
                <!-- Task Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold <?php echo e($task->status === 'completed' ? 'text-green-100' : 'text-purple-100'); ?>">
                                <i class="fas fa-<?php echo e($task->status === 'completed' ? 'check-circle' : 'broom'); ?> mr-2"></i><?php echo e($task->title); ?>

                            </h3>
                            <span class="px-3 py-1 text-xs rounded-full font-medium
                                <?php echo e($task->status === 'pending' ? 'bg-yellow-600 text-yellow-100' : ''); ?>

                                <?php echo e($task->status === 'assigned' ? 'bg-blue-600 text-blue-100' : ''); ?>

                                <?php echo e($task->status === 'in_progress' ? 'bg-indigo-600 text-indigo-100' : ''); ?>

                                <?php echo e($task->status === 'completed' ? 'bg-green-600 text-green-100' : ''); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $task->status))); ?>

                            </span>
                            <?php if($task->status === 'completed'): ?>
                            <span class="text-xs text-green-400">
                                <i class="fas fa-check mr-1"></i>Finished
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-purple-200 text-sm whitespace-pre-line"><?php echo e($task->description); ?></div>
                    </div>
                </div>

                <!-- Task Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <!-- Facility Info -->
                    <div class="space-y-1">
                        <label class="text-xs text-gray-400 uppercase tracking-wide">Facility</label>
                        <?php if($task->booking && $task->booking->room): ?>
                        <p class="text-purple-100 font-medium">
                            <i class="fas fa-door-open mr-1"></i><?php echo e($task->booking->room->name); ?>

                        </p>
                        <p class="text-gray-400 text-sm"><?php echo e($task->booking->room->category); ?></p>
                        <?php else: ?>
                        <p class="text-gray-400">N/A</p>
                        <?php endif; ?>
                    </div>

                    <!-- Guest Info -->
                    <div class="space-y-1">
                        <label class="text-xs text-gray-400 uppercase tracking-wide">Guest</label>
                        <?php if($task->booking && $task->booking->user): ?>
                        <p class="text-purple-100 font-medium"><?php echo e($task->booking->user->name); ?></p>
                        <?php else: ?>
                        <p class="text-gray-400">N/A</p>
                        <?php endif; ?>
                    </div>

                    <!-- Assignment -->
                    <div class="space-y-1">
                        <label class="text-xs text-gray-400 uppercase tracking-wide">Assign To</label>
                        <?php if($task->status === 'completed'): ?>
                        <p class="text-green-100 font-medium">
                            <i class="fas fa-user-check mr-1"></i><?php echo e($task->assignedTo->name ?? 'N/A'); ?>

                        </p>
                        <p class="text-xs text-green-400">Task completed</p>
                        <?php else: ?>
                        <select onchange="updateHousekeepingAssignment(<?php echo e($task->id); ?>, this.value)" 
                                class="w-full bg-gray-700 text-purple-100 rounded px-3 py-2 text-sm">
                            <option value="">Unassigned</option>
                            <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($staff->id); ?>" <?php echo e($task->assigned_to == $staff->id ? 'selected' : ''); ?>>
                                <?php echo e($staff->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php endif; ?>
                    </div>

                    <!-- Due Date (Check-out) -->
                    <div class="space-y-1">
                        <label class="text-xs text-gray-400 uppercase tracking-wide">Due By</label>
                        <p class="text-purple-100 font-medium">
                            <i class="fas fa-clock mr-1"></i>
                            <?php if($task->booking && $task->booking->check_out): ?>
                                <?php echo e(\Carbon\Carbon::parse($task->booking->check_out)->format('M d, Y g:i A')); ?>

                            <?php elseif($task->due_date): ?>
                                <?php echo e($task->due_date->format('M d, Y g:i A')); ?>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <?php if($task->booking && $task->booking->check_out && \Carbon\Carbon::parse($task->booking->check_out)->isPast() && $task->status !== 'completed'): ?>
                        <p class="text-red-400 text-xs">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                        </p>
                        <?php elseif($task->due_date && $task->due_date->isPast() && $task->status !== 'completed'): ?>
                        <p class="text-red-400 text-xs">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                        </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Timeline Footer -->
                <div class="mt-4 pt-3 border-t border-gray-700">
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Created: <?php echo e($task->created_at->format('M d, Y H:i')); ?></span>
                        <?php if($task->status === 'completed'): ?>
                        <span class="text-green-400">
                            <i class="fas fa-check-circle mr-1"></i>Completed: <?php echo e($task->updated_at->format('M d, Y H:i')); ?>

                        </span>
                        <?php elseif($task->assigned_to): ?>
                        <span>Assigned to: <?php echo e($task->assignedTo->name ?? 'N/A'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Service Requests Section -->
    <h2 class="text-2xl font-bold text-green-100 mb-4 flex items-center">
        <i class="fas fa-concierge-bell mr-3"></i>
        Active Service Requests
    </h2>

    <!-- Service Requests Cards -->
    <div class="space-y-4" id="requestsContainer">
        <?php $__empty_1 = true; $__currentLoopData = $serviceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors <?php echo e($request->deadline_status === 'overdue' ? 'border-l-4 border-red-500' : ''); ?>" 
             data-request-id="<?php echo e($request->id); ?>" 
             data-status="<?php echo e($request->status); ?>" 
             data-staff="<?php echo e($request->assigned_to); ?>">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4 w-full">
                    <div class="bulk-checkbox hidden">
                        <input type="checkbox" value="<?php echo e($request->id); ?>" class="request-checkbox rounded mt-1">
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-2xl font-bold text-green-100">
                                <?php echo e($request->service->name ?? $request->service_type ?? 'Service Request'); ?>

                                <?php if(isset($request->service) && $request->service && $request->service->price): ?>
                                    <span class="text-lg font-semibold text-green-400 ml-2">
                                        $<?php echo e(number_format($request->service->price, 2)); ?>

                                    </span>
                                <?php endif; ?>
                            </h3>
                            <!-- Status Badge (dropdown always visible) -->
                <select onchange="updateStatus(<?php echo e($request->id); ?>, this.value)" 
                          class="px-3 py-1 text-xs rounded-full border-none font-medium mr-4
                          <?php echo e($request->status === 'completed' ? 'bg-green-600 text-green-100' : 
                              ($request->status === 'assigned' || $request->status === 'in_progress' ? 'bg-blue-600 text-blue-100' : 
                              ($request->status === 'pending' ? 'bg-yellow-500 text-yellow-100' : 'bg-gray-600 text-gray-100'))); ?>">
                                <option value="pending" <?php echo e($request->status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="confirmed" <?php echo e($request->status === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                                <option value="assigned" <?php echo e($request->status === 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                                <option value="in_progress" <?php echo e($request->status === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                <option value="completed" <?php echo e($request->status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            </select>
                        </div>
                        <?php if($request->status !== 'completed'): ?>
                            <!-- Description - Editable -->
                            <textarea class="description-input bg-transparent text-gray-300 border-none outline-none hover:bg-gray-700 focus:bg-gray-700 rounded px-2 py-1 w-full resize-none"
                                      rows="2"
                                      data-field="description"
                                      data-request-id="<?php echo e($request->id); ?>"
                                      onblur="updateField(this)"
                                      placeholder="Service description..."><?php echo e($request->description); ?></textarea>
                        <?php else: ?>
                            <div class="text-gray-300 text-base mb-2">
                                <?php echo e($request->description); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Actions - Simplified -->
                <div class="flex gap-2">
                    <!-- Cancel Button -->
                    <button onclick="cancelRequest(<?php echo e($request->id); ?>)" 
                            class="inline-flex items-center px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors" 
                            title="Cancel Service">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    
                    <!-- Delete Button -->
                    <button onclick="deleteRequest(<?php echo e($request->id); ?>)" 
                            class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors" 
                            title="Delete Permanently">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </div>
            </div>

            <!-- Card Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Guest Info -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Guest</label>
                    <p class="text-green-100 font-medium"><?php echo e($request->guest->name ?? $request->guest_name ?? 'N/A'); ?></p>
                    <?php if($request->room): ?>
                    <p class="text-gray-400 text-sm">Room: <?php echo e($request->room->name); ?></p>
                    <?php endif; ?>
                    <!-- Editable Guests Count -->
                    <input type="number" 
                           value="<?php echo e($request->guests_count ?? $request->guests ?? 1); ?>" 
                           min="1"
                           placeholder="Guests count"
                           class="w-full bg-gray-700 text-green-100 rounded px-2 py-1 text-sm border-none mt-1"
                           data-field="guests_count"
                           data-request-id="<?php echo e($request->id); ?>"
                           onblur="updateField(this)">
                </div>

                <!-- Assignment -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Assigned To</label>
                    <?php if($request->status === 'completed'): ?>
                        <p class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                            <?php if(empty($request->assigned_to)): ?>
                                Unassigned
                            <?php else: ?>
                                <?php echo e(optional($availableStaff->firstWhere('id', $request->assigned_to))->name ?? 'Staff'); ?>

                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <select onchange="updateAssignment(<?php echo e($request->id); ?>, this.value)" 
                                class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none">
                            <option value="" <?php echo e(empty($request->assigned_to) ? 'selected' : ''); ?>>Unassigned</option>
                            <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($staff->id); ?>" <?php echo e($request->assigned_to == $staff->id ? 'selected' : ''); ?>>
                                <?php echo e($staff->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Deadline -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due On</label>
                    <?php if($request->status === 'completed'): ?>
                        <p class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                            <?php if($request->deadline): ?>
                                <?php echo e($request->deadline->format('M d, Y g:i A')); ?>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                        <?php if($request->deadline): ?>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="px-2 py-1 text-xs rounded <?php echo e($request->deadline_color); ?>">
                                <?php echo e($request->deadline->diffForHumans()); ?>

                            </span>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <input type="datetime-local" 
                               value="<?php echo e($request->deadline ? $request->deadline->format('Y-m-d\TH:i') : ''); ?>"
                               class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none"
                               onchange="updateDeadline(<?php echo e($request->id); ?>, this.value)">
                        <?php if($request->deadline): ?>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded <?php echo e($request->deadline_color); ?>">
                                    <?php echo e($request->deadline->diffForHumans()); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Duration & Notes -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Duration & Notes</label>
                    <?php if($request->status === 'completed'): ?>
                        <p class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                            <?php
                                $durationMap = [
                                    30 => '30 min',
                                    60 => '1 hour',
                                    120 => '2 hours',
                                    240 => '4 hours',
                                    '' => 'No estimate',
                                    null => 'No estimate'
                                ];
                            ?>
                            <?php echo e($durationMap[$request->estimated_duration ?? ''] ?? 'No estimate'); ?>

                        </p>
                        <?php if($request->manager_notes): ?>
                        <div class="w-full bg-gray-700 text-green-100 rounded px-3 py-1 text-sm mt-1">
                            <?php echo e($request->manager_notes); ?>

                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <select onchange="updateDuration(<?php echo e($request->id); ?>, this.value)" 
                                class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none">
                            <option value="">No estimate</option>
                            <option value="30" <?php echo e($request->estimated_duration == 30 ? 'selected' : ''); ?>>30 min</option>
                            <option value="60" <?php echo e($request->estimated_duration == 60 ? 'selected' : ''); ?>>1 hour</option>
                            <option value="120" <?php echo e($request->estimated_duration == 120 ? 'selected' : ''); ?>>2 hours</option>
                            <option value="240" <?php echo e($request->estimated_duration == 240 ? 'selected' : ''); ?>>4 hours</option>
                        </select>
                        <input type="text" 
                               value="<?php echo e($request->manager_notes); ?>" 
                               placeholder="Manager notes..."
                               class="w-full bg-gray-700 text-green-100 rounded px-3 py-1 text-sm border-none mt-1"
                               data-field="manager_notes"
                               data-request-id="<?php echo e($request->id); ?>"
                               onblur="updateField(this)">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Timeline Footer -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Created: <?php echo e($request->created_at->format('M d, Y H:i')); ?></span>
                    <?php if($request->assigned_at): ?>
                    <span>Assigned: <?php echo e($request->assigned_at->format('M d, H:i')); ?></span>
                    <?php endif; ?>
                    <?php if($request->completed_at): ?>
                    <span>Completed: <?php echo e($request->completed_at->format('M d, H:i')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <i class="fas fa-clipboard-list text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">No service requests found</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($serviceRequests->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($serviceRequests->links()); ?>

    </div>
    <?php endif; ?>
</div>

<script>
let bulkMode = false;

// Inline field editing
function updateField(element) {
    const requestId = element.dataset.requestId;
    const field = element.dataset.field;
    const value = element.value;
    
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ [field]: value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Updated successfully', 'success');
        } else {
            showNotification('Update failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Update failed', 'error');
    });
}

// Status update
function updateStatus(requestId, status) {
    const selectElement = event?.target;
    
    fetch(`/manager/staff-assignment/${requestId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Status updated', 'success');
            
            // Update data attribute
            const card = document.querySelector(`[data-request-id="${requestId}"]`);
            card.setAttribute('data-status', status);
            
            // Update the dropdown styling immediately
            if (selectElement) {
                selectElement.className = 'px-3 py-1 text-xs rounded-full border-none font-medium mr-4 ' + 
                    (status === 'completed' ? 'bg-green-600 text-green-100' : 
                     (status === 'assigned' || status === 'in_progress' ? 'bg-blue-600 text-blue-100' : 
                     (status === 'pending' ? 'bg-yellow-500 text-yellow-100' : 'bg-gray-600 text-gray-100')));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Status update failed', 'error');
    });
}

// Assignment update
function updateAssignment(requestId, staffId) {
    const selectElement = event?.target;
    const previousValue = selectElement ? selectElement.getAttribute('data-previous') : '';
    
    if (!staffId) {
        // If unassigning, set status to 'pending' and no confirmation needed
        sendAssignment(requestId, staffId, 'pending', selectElement);
        return;
    }
    const staffName = document.querySelector(`#requestsContainer [data-request-id='${requestId}'] select option[value='${staffId}']`)?.textContent.trim() || 'this staff member';
    if (confirm(`Are you sure you want to assign this task to ${staffName}?`)) {
        sendAssignment(requestId, staffId, 'assigned', selectElement);
    } else {
        // Revert select to previous value if cancelled
        if (selectElement) {
            selectElement.value = previousValue || '';
        }
    }
}

function sendAssignment(requestId, staffId, status, selectElement) {
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            assigned_to: staffId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Assignment updated', 'success');
            
            // Update data attributes and UI
            const card = document.querySelector(`[data-request-id="${requestId}"]`);
            card.setAttribute('data-staff', staffId || '');
            card.setAttribute('data-status', status);
            
            // Update the status dropdown to reflect the new status
            const statusDropdown = card.querySelector('select[onchange^="updateStatus"]');
            if (statusDropdown) {
                statusDropdown.value = status;
                // Update status dropdown styling
                statusDropdown.className = 'px-3 py-1 text-xs rounded-full border-none font-medium mr-4 ' + 
                    (status === 'completed' ? 'bg-green-600 text-green-100' : 
                     (status === 'assigned' || status === 'in_progress' ? 'bg-blue-600 text-blue-100' : 
                     (status === 'pending' ? 'bg-yellow-500 text-yellow-100' : 'bg-gray-600 text-gray-100')));
            }
            
            // Store current value for cancel functionality
            if (selectElement) {
                selectElement.setAttribute('data-previous', staffId || '');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Assignment update failed', 'error');
        // Revert on error
        if (selectElement) {
            selectElement.value = selectElement.getAttribute('data-previous') || '';
        }
    });
}

// Deadline update
function updateDeadline(requestId, deadline) {
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ deadline: deadline })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Deadline updated', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Deadline update failed', 'error');
    });
}

// Duration update
function updateDuration(requestId, duration) {
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ estimated_duration: duration })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Duration updated', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Duration update failed', 'error');
    });
}

// Housekeeping task assignment
function updateHousekeepingAssignment(taskId, staffId) {
    fetch(`/manager/staff-assignment/housekeeping/${taskId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
            assigned_to: staffId,
            status: staffId ? 'assigned' : 'pending'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Housekeeping task assigned successfully', 'success');
            // Optionally reload to update UI
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Assignment failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Assignment update failed', 'error');
    });
}

// SIMPLIFIED CANCEL REQUEST
function cancelRequest(requestId) {
    console.log('Cancelling request:', requestId); // Debug log
    
    if (confirm('Cancel this service request? It will be removed from the active list.')) {
        fetch(`/manager/staff-assignment/${requestId}/cancel`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Cancel response:', response); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Cancel data:', data); // Debug log
            if (data.success) {
                document.querySelector(`[data-request-id="${requestId}"]`).remove();
                showNotification('Service request cancelled', 'success');
            } else {
                showNotification(data.message || 'Cancel failed', 'error');
            }
        })
        .catch(error => {
            console.error('Cancel error:', error);
            showNotification('Cancel failed: ' + error.message, 'error');
        });
    }
}

// SIMPLIFIED DELETE REQUEST
function deleteRequest(requestId) {
    console.log('Deleting request:', requestId); // Debug log
    
    if (confirm('Permanently delete this service request? This cannot be undone!')) {
        fetch(`/manager/staff-assignment/${requestId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Delete response:', response); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Delete data:', data); // Debug log
            if (data.success) {
                document.querySelector(`[data-request-id="${requestId}"]`).remove();
                showNotification('Request deleted permanently', 'success');
            } else {
                showNotification(data.message || 'Delete failed', 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showNotification('Delete failed: ' + error.message, 'error');
        });
    }
}

// Bulk mode toggle
function toggleBulkMode() {
    bulkMode = !bulkMode;
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    const panel = document.getElementById('bulkActionsPanel');
    const btn = document.getElementById('bulkModeBtn');
    
    if (bulkMode) {
        checkboxes.forEach(cb => cb.classList.remove('hidden'));
        panel.classList.remove('hidden');
        btn.textContent = 'Exit Bulk Mode';
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    } else {
        checkboxes.forEach(cb => cb.classList.add('hidden'));
        panel.classList.add('hidden');
        btn.textContent = 'Bulk Actions';
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    }
}

// Update selected count
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('request-checkbox')) {
        const selected = document.querySelectorAll('.request-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected;
    }
});

// Bulk assign
function bulkAssign() {
    const staffId = document.getElementById('bulkStaff').value;
    const deadline = document.getElementById('bulkDeadline').value;
    const selectedRequests = Array.from(document.querySelectorAll('.request-checkbox:checked')).map(cb => cb.value);
    
    if (!staffId) {
        showNotification('Please select a staff member', 'error');
        return;
    }
    
    if (selectedRequests.length === 0) {
        showNotification('Please select at least one request', 'error');
        return;
    }
    
    fetch('/manager/staff-assignment/bulk-assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            service_requests: selectedRequests,
            assigned_to: staffId,
            deadline: deadline,
            estimated_duration: 60
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Bulk assignment completed', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Bulk assignment failed', 'error');
    });
}

// SIMPLIFIED BULK CANCEL
function bulkCancel() {
    const selectedRequests = Array.from(document.querySelectorAll('.request-checkbox:checked')).map(cb => cb.value);
    
    if (selectedRequests.length === 0) {
        showNotification('Please select at least one request', 'error');
        return;
    }
    
    if (confirm(`Cancel ${selectedRequests.length} selected requests?`)) {
        Promise.all(selectedRequests.map(requestId => 
            fetch(`/manager/staff-assignment/${requestId}/cancel`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
        ))
        .then(() => {
            selectedRequests.forEach(requestId => {
                document.querySelector(`[data-request-id="${requestId}"]`).remove();
            });
            showNotification('Selected requests cancelled', 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Bulk cancel failed', 'error');
        });
    }
}

// Filtering
function clearFilters() {
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterStaff').value = '';
    filterRequests();
}

function filterRequests() {
    const statusFilter = document.getElementById('filterStatus').value;
    const staffFilter = document.getElementById('filterStaff').value;
    const cards = document.querySelectorAll('[data-request-id]');
    
    cards.forEach(card => {
        let show = true;
        
        if (statusFilter && card.dataset.status !== statusFilter) {
            show = false;
        }
        
        if (staffFilter && card.dataset.staff !== staffFilter) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Add event listeners for filters
document.getElementById('filterStatus').addEventListener('change', filterRequests);
document.getElementById('filterStaff').addEventListener('change', filterRequests);

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Toggle completed tasks view
let showingCompleted = false;

function toggleCompletedTasks() {
    showingCompleted = !showingCompleted;
    const btn = document.getElementById('completedTasksBtn');
    const activeTasksSection = document.getElementById('requestsContainer');
    const activeHousekeepingSection = document.querySelector('.mb-8'); // Housekeeping section
    
    if (showingCompleted) {
        // Hide active tasks and show only completed ones
        btn.innerHTML = '<i class="fas fa-list mr-2"></i>View Active Tasks<span class="ml-2 bg-blue-800 px-2 py-0.5 rounded-full text-xs font-bold">Back</span>';
        btn.classList.remove('bg-green-600', 'hover:bg-green-700');
        btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        
        // Filter to show only completed tasks
        filterByCompletion(true);
    } else {
        // Show all active tasks
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Completed Tasks<span class="ml-2 bg-green-800 px-2 py-0.5 rounded-full text-xs font-bold"><?php echo e($completedRequests + $completedHousekeeping); ?></span>';
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        btn.classList.add('bg-green-600', 'hover:bg-green-700');
        
        // Show all tasks
        filterByCompletion(false);
    }
}

function filterByCompletion(showOnlyCompleted) {
    // Filter service requests
    const serviceCards = document.querySelectorAll('[data-request-id]');
    serviceCards.forEach(card => {
        const status = card.dataset.status;
        if (showOnlyCompleted) {
            card.style.display = status === 'completed' ? 'block' : 'none';
        } else {
            card.style.display = status !== 'completed' ? 'block' : 'none';
        }
    });
    
    // Filter housekeeping tasks
    const housekeepingTasks = document.querySelectorAll('.bg-gray-800.border-l-4');
    housekeepingTasks.forEach(task => {
        const isCompleted = task.classList.contains('border-green-600');
        if (showOnlyCompleted) {
            task.style.display = isCompleted ? 'block' : 'none';
        } else {
            task.style.display = !isCompleted ? 'block' : 'none';
        }
    });
    
    // Update section headers
    const serviceHeader = document.querySelector('h2.text-green-100');
    const housekeepingHeader = document.querySelector('h2.text-purple-100');
    
    if (showOnlyCompleted) {
        if (serviceHeader) serviceHeader.innerHTML = '<i class="fas fa-check-circle mr-3"></i>Completed Service Requests';
        if (housekeepingHeader) housekeepingHeader.innerHTML = '<i class="fas fa-check-circle mr-3"></i>Completed Housekeeping Tasks';
    } else {
        if (serviceHeader) serviceHeader.innerHTML = '<i class="fas fa-concierge-bell mr-3"></i>Active Service Requests';
        if (housekeepingHeader) housekeepingHeader.innerHTML = '<i class="fas fa-broom mr-3"></i>Active Housekeeping Tasks';
    }
}

// Initialize view on page load - hide completed tasks by default
document.addEventListener('DOMContentLoaded', function() {
    filterByCompletion(false); // Show only active tasks
    
    // Initialize data-previous attributes for assignment dropdowns
    document.querySelectorAll('select[onchange^="updateAssignment"]').forEach(select => {
        select.setAttribute('data-previous', select.value);
    });
});
</script>

<!-- Add CSRF token for AJAX requests -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/staff-assignment/index.blade.php ENDPATH**/ ?>