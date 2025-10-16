<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50 mb-2">Service Requests Management</h1>
        <p class="text-green-200">Manage and assign service requests efficiently.</p>
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
            
            <div class="ml-auto">
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

    <!-- Service Requests Cards -->
    <div class="space-y-4" id="requestsContainer">
        <?php $__empty_1 = true; $__currentLoopData = $serviceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors <?php echo e($request->deadline_status === 'overdue' ? 'border-l-4 border-red-500' : ''); ?>" 
             data-request-id="<?php echo e($request->id); ?>" 
             data-status="<?php echo e($request->status); ?>" 
             data-staff="<?php echo e($request->assigned_to); ?>">
            
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start space-x-4">
                    <div class="bulk-checkbox hidden">
                        <input type="checkbox" value="<?php echo e($request->id); ?>" class="request-checkbox rounded mt-1">
                    </div>
                    
                    <div class="flex-1">
                        <!-- Service Type - Editable Dropdown -->
                        <div class="flex items-center space-x-2 mb-2">
                            <select data-field="service_type"
                                    data-request-id="<?php echo e($request->id); ?>"
                                    onchange="updateField(this)"
                                    class="service-type-input bg-transparent text-lg font-semibold text-green-100 border-none outline-none hover:bg-gray-700 focus:bg-gray-700 rounded px-2 py-1">
                                <option value="">Select Service Type</option>
                                <?php $__currentLoopData = $availableServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($service->name); ?>" <?php echo e($request->service_type === $service->name ? 'selected' : ''); ?>>
                                    <?php echo e($service->name); ?> - $<?php echo e($service->price); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            
                            <!-- Status Badge -->
                            <select onchange="updateStatus(<?php echo e($request->id); ?>, this.value)" 
                                    class="px-3 py-1 text-xs rounded-full border-none font-medium
                                    <?php echo e($request->status === 'completed' ? 'bg-green-600 text-green-100' : 
                                       ($request->status === 'assigned' || $request->status === 'in_progress' ? 'bg-blue-600 text-blue-100' : 
                                       ($request->status === 'pending' ? 'bg-red-600 text-red-100' : 'bg-gray-600 text-gray-100'))); ?>">
                                <option value="pending" <?php echo e($request->status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="confirmed" <?php echo e($request->status === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                                <option value="assigned" <?php echo e($request->status === 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                                <option value="in_progress" <?php echo e($request->status === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                                <option value="completed" <?php echo e($request->status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            </select>
                        </div>
                        
                        <!-- Description - Editable -->
                        <textarea class="description-input bg-transparent text-gray-300 border-none outline-none hover:bg-gray-700 focus:bg-gray-700 rounded px-2 py-1 w-full resize-none"
                                  rows="2"
                                  data-field="description"
                                  data-request-id="<?php echo e($request->id); ?>"
                                  onblur="updateField(this)"
                                  placeholder="Service description..."><?php echo e($request->description); ?></textarea>
                    </div>
                </div>
                
                <!-- Actions - Simplified -->
                <div class="flex gap-2">
                    <!-- Confirm Task Button (only show if assigned but not confirmed) -->
                    <?php if($request->assigned_to && $request->status !== 'confirmed'): ?>
                    <button onclick="confirmTask(<?php echo e($request->id); ?>)" 
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors" 
                            title="Confirm Task Assignment">
                        <i class="fas fa-check-circle mr-2"></i>
                        Confirm Task
                    </button>
                    <?php endif; ?>
                    
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
                    <select id="assignment-<?php echo e($request->id); ?>" onchange="selectStaff(<?php echo e($request->id); ?>, this.value)" 
                            class="w-full bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none">
                        <option value="">Unassigned</option>
                        <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($staff->id); ?>" <?php echo e($request->assigned_to == $staff->id ? 'selected' : ''); ?>>
                            <?php echo e($staff->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    
                    <!-- Confirm Assignment Button (Hidden by default) -->
                    <div id="confirm-assignment-<?php echo e($request->id); ?>" class="hidden mt-2">
                        <button onclick="confirmAssignment(<?php echo e($request->id); ?>)" 
                                class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Confirm Assignment
                        </button>
                        <button onclick="cancelAssignment(<?php echo e($request->id); ?>)" 
                                class="w-full bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700 transition-colors mt-1">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                    </div>
                    
                    <!-- Assignment Status -->
                    <?php if($request->assigned_to): ?>
                    <div class="mt-2 text-xs text-green-300">
                        <i class="fas fa-check-circle mr-1"></i>
                        Assigned to <?php echo e($request->assignedTo->name ?? 'Unknown'); ?>

                    </div>
                    <?php endif; ?>
                </div>

                <!-- Deadline -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due On</label>
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
                </div>

                <!-- Duration & Notes -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Duration & Notes</label>
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
            document.querySelector(`[data-request-id="${requestId}"]`).setAttribute('data-status', status);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Status update failed', 'error');
    });
}

// Select staff member (show confirmation buttons)
function selectStaff(requestId, staffId) {
    const confirmDiv = document.getElementById(`confirm-assignment-${requestId}`);
    
    if (staffId && staffId !== '') {
        // Show confirmation buttons
        confirmDiv.classList.remove('hidden');
    } else {
        // Hide confirmation buttons and immediately unassign
        confirmDiv.classList.add('hidden');
        updateAssignment(requestId, staffId);
    }
}

// Confirm assignment
function confirmAssignment(requestId) {
    const selectElement = document.getElementById(`assignment-${requestId}`);
    const staffId = selectElement.value;
    
    if (staffId && staffId !== '') {
        updateAssignment(requestId, staffId);
        
        // Hide confirmation buttons
        document.getElementById(`confirm-assignment-${requestId}`).classList.add('hidden');
    }
}

// Cancel assignment selection
function cancelAssignment(requestId) {
    const selectElement = document.getElementById(`assignment-${requestId}`);
    const originalValue = selectElement.dataset.originalValue || '';
    
    // Reset to original value
    selectElement.value = originalValue;
    
    // Hide confirmation buttons
    document.getElementById(`confirm-assignment-${requestId}`).classList.add('hidden');
}

// Assignment update
function updateAssignment(requestId, staffId) {
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            assigned_to: staffId || null
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(staffId ? 'Task assigned successfully!' : 'Assignment removed successfully!', 'success');
            document.querySelector(`[data-request-id="${requestId}"]`).setAttribute('data-staff', staffId);
            
            // Store the current value as original for future reference
            const selectElement = document.getElementById(`assignment-${requestId}`);
            selectElement.dataset.originalValue = staffId;
            
            // Reload the page to show updated assignment status
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Assignment update failed', 'error');
        }
    })
    .catch(error => {
        console.error('Assignment error:', error);
        showNotification('Assignment update failed: ' + error.message, 'error');
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

// CONFIRM TASK ASSIGNMENT
function confirmTask(requestId) {
    console.log('Confirming task for request:', requestId);
    
    if (confirm('Confirm this task assignment? This will notify the staff member and make the task active.')) {
        fetch(`/manager/staff-assignment/${requestId}/confirm-task`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Confirm task response:', response);
            return response.json();
        })
        .then(data => {
            console.log('Confirm task data:', data);
            if (data.success) {
                showNotification('Task confirmed successfully! Staff has been notified.', 'success');
                // Reload the page to show updated status
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Task confirmation failed', 'error');
            }
        })
        .catch(error => {
            console.error('Confirm task error:', error);
            showNotification('Task confirmation failed: ' + error.message, 'error');
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

// Initialize assignment dropdowns with original values
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="assignment-"]').forEach(select => {
        select.dataset.originalValue = select.value;
    });
});

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

// Update scheduled date (which is the guest's requested service time)
function updateScheduledDate(requestId, scheduledDate) {
    console.log('Updating scheduled date for request:', requestId, 'to:', scheduledDate);
    
    if (!scheduledDate) {
        showNotification('Please select a valid date and time', 'error');
        return;
    }
    
    // Confirm with manager before changing guest's scheduled service time
    if (!confirm('This will change the guest\'s scheduled service time. Are you sure?')) {
        // Reset the input to original value
        location.reload();
        return;
    }
    
    fetch(`/manager/staff-assignment/${requestId}/quick-update`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            scheduled_date: scheduledDate,
            deadline: scheduledDate // Keep both in sync
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Guest\'s scheduled service time updated successfully', 'success');
            
            // Check if now overdue and refresh if needed
            if (new Date(scheduledDate) < new Date()) {
                showNotification('Warning: This service is now overdue!', 'error');
                setTimeout(() => location.reload(), 2000);
            } else {
                // Refresh to show updated timing
                setTimeout(() => location.reload(), 1000);
            }
            
        } else {
            showNotification(data.message || 'Failed to update scheduled date', 'error');
        }
    })
    .catch(error => {
        console.error('Scheduled date update error:', error);
        showNotification(`Failed to update scheduled date: ${error.message}`, 'error');
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/manager/staff-assignment/index.blade.php ENDPATH**/ ?>