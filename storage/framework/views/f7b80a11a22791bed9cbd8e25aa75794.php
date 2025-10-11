<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50 mb-2">My Tasks</h1>
        <p class="text-green-200">View and manage your assigned tasks.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($pendingTasks); ?></div>
            <div class="text-sm opacity-90">Pending</div>
        </div>
        <div class="bg-orange-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($inProgressTasks); ?></div>
            <div class="text-sm opacity-90">In Progress</div>
        </div>
        <div class="bg-green-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($completedTasks); ?></div>
            <div class="text-sm opacity-90">Completed Today</div>
        </div>
        <div class="bg-red-800 rounded-lg p-4 text-white text-center animate-pulse">
            <div class="text-2xl font-bold"><?php echo e($overdueTasks); ?></div>
            <div class="text-sm opacity-90">⚠️ OVERDUE</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
            <select id="filterStatus" class="bg-gray-700 text-green-100 rounded px-3 py-2 text-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="assigned">Assigned</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="overdue">⚠️ Overdue</option>
            </select>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-500">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4" id="tasksContainer">
        <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $isOverdue = $task->due_date < now() && !in_array($task->status, ['completed', 'cancelled']);
            $hoursOverdue = $isOverdue ? $task->due_date->diffInHours(now()) : 0;
        ?>
        
        <div class="rounded-lg p-6 hover:bg-gray-750 transition-colors 
                    <?php echo e($isOverdue ? 'bg-red-900 border-2 border-red-500 animate-pulse' : 'bg-gray-800'); ?>" 
             data-task-id="<?php echo e($task->id); ?>" 
             data-status="<?php echo e($task->status); ?>"
             data-overdue="<?php echo e($isOverdue ? 'true' : 'false'); ?>">
            
            <!-- Overdue Alert Banner -->
            <?php if($isOverdue): ?>
            <div class="bg-red-600 text-white px-4 py-2 rounded-lg mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-xl mr-3 animate-bounce"></i>
                    <div>
                        <div class="font-bold text-lg">⚠️ TASK OVERDUE!</div>
                        <div class="text-sm">
                            This task was due <?php echo e($task->due_date->diffForHumans()); ?>

                            <?php if($hoursOverdue > 24): ?>
                                (<?php echo e(floor($hoursOverdue / 24)); ?> day<?php echo e(floor($hoursOverdue / 24) > 1 ? 's' : ''); ?> overdue)
                            <?php else: ?>
                                (<?php echo e($hoursOverdue); ?> hour<?php echo e($hoursOverdue > 1 ? 's' : ''); ?> overdue)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs opacity-90">Due Date:</div>
                    <div class="font-medium"><?php echo e($task->due_date->format('M d, Y H:i')); ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Task Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-green-100 mb-2">
                        <?php echo e($task->title); ?>

                        <?php if($isOverdue): ?>
                            <span class="text-red-400 text-sm ml-2">[OVERDUE]</span>
                        <?php endif; ?>
                    </h3>
                    
                    <!-- Status Badge -->
                    <div class="flex items-center space-x-3 mb-3">
                        <select onchange="updateTaskStatus(<?php echo e($task->id); ?>, this.value)" 
                                class="px-3 py-1 text-xs rounded-full border-none font-medium <?php echo e($task->status_color); ?>">
                            <option value="pending" <?php echo e($task->status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="confirmed" <?php echo e($task->status === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                            <option value="assigned" <?php echo e($task->status === 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                            <option value="in_progress" <?php echo e($task->status === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                            <option value="completed" <?php echo e($task->status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                        </select>
                        
                        <?php if($task->status === 'confirmed'): ?>
                        <span class="px-2 py-1 text-xs rounded bg-green-600 text-green-100 flex items-center">
                            <i class="fas fa-check-circle mr-1"></i>
                            Manager Confirmed
                        </span>
                        <?php endif; ?>
                        
                        <?php if($isOverdue): ?>
                        <span class="px-3 py-1 text-sm font-bold rounded-full bg-red-600 text-white animate-pulse">
                            <i class="fas fa-clock mr-1"></i>
                            OVERDUE
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Task Actions -->
                <div class="flex gap-2">
                    <button onclick="viewTaskDetails(<?php echo e($task->id); ?>)" 
                            class="inline-flex items-center px-3 py-2 <?php echo e($isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'); ?> text-white text-sm rounded-lg transition-colors" 
                            title="View Details">
                        <i class="fas fa-eye mr-2"></i>
                        View
                    </button>
                </div>
            </div>

            <!-- Task Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Description -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Description</label>
                    <p class="text-gray-300 text-sm"><?php echo e(Str::limit($task->description, 100)); ?></p>
                </div>

                <!-- Assigned By -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Assigned By</label>
                    <p class="text-green-100 font-medium"><?php echo e($task->assignedBy->name ?? 'System'); ?></p>
                </div>

                <!-- Due Date with Enhanced Overdue Display -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due Date</label>
                    <p class="font-medium <?php echo e($isOverdue ? 'text-red-400 font-bold' : 'text-green-100'); ?>">
                        <?php echo e($task->due_date->format('M d, Y H:i')); ?>

                    </p>
                    <p class="text-sm <?php echo e($isOverdue ? 'text-red-300 font-medium' : 'text-gray-400'); ?>">
                        <?php echo e($task->due_date->diffForHumans()); ?>

                        <?php if($isOverdue): ?>
                            <span class="text-red-400 font-bold ml-1">⚠️</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <!-- Task Notes -->
            <?php if($task->notes): ?>
            <div class="mt-4 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide">My Notes</label>
                <p class="text-gray-300 text-sm mt-1"><?php echo e($task->notes); ?></p>
            </div>
            <?php endif; ?>

            <!-- Notes Input -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide">Add/Update Notes</label>
                <div class="flex gap-2 mt-2">
                    <input type="text" 
                           value="<?php echo e($task->notes); ?>" 
                           placeholder="Add notes about this task..."
                           class="flex-1 bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none"
                           data-task-id="<?php echo e($task->id); ?>"
                           onkeypress="if(event.key==='Enter') updateTaskNotes(<?php echo e($task->id); ?>, this.value)">
                    <button onclick="updateTaskNotes(<?php echo e($task->id); ?>, document.querySelector('[data-task-id=\'<?php echo e($task->id); ?>\']').value)" 
                            class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                        Save
                    </button>
                </div>
            </div>

            <!-- Timeline Footer -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Created: <?php echo e($task->created_at->format('M d, Y H:i')); ?></span>
                    <?php if($task->completed_at): ?>
                    <span>Completed: <?php echo e($task->completed_at->format('M d, H:i')); ?></span>
                    <?php elseif($isOverdue): ?>
                    <span class="text-red-400 font-medium">
                        ⚠️ OVERDUE BY: <?php echo e($task->due_date->diffForHumans(null, true)); ?>

                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <i class="fas fa-tasks text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">No tasks assigned to you</p>
            <p class="text-gray-500 text-sm">Tasks will appear here when managers assign them to you</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($tasks->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($tasks->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Task Details Modal -->
<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-green-100">Task Details</h3>
                    <button onclick="closeTaskModal()" 
                            class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-full p-2 transition-colors duration-200"
                            title="Close">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="taskModalContent">
                    <!-- Task details will be loaded here -->
                </div>
                
                <!-- Additional Close Button at Bottom -->
                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-end">
                    <button onclick="closeTaskModal()" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update task status
function updateTaskStatus(taskId, status) {
    fetch(`/staff/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Task status updated successfully', 'success');
            // Update the task card status
            const taskCard = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskCard) {
                taskCard.setAttribute('data-status', status);
                // Update status badge color
                const statusSelect = taskCard.querySelector('select');
                if (statusSelect) {
                    statusSelect.className = `px-3 py-1 text-xs rounded-full border-none font-medium ${getStatusColor(status)}`;
                }
            }
        } else {
            showNotification(data.message || 'Failed to update task status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update task status', 'error');
    });
}

// Update task notes
function updateTaskNotes(taskId, notes) {
    fetch(`/staff/tasks/${taskId}/notes`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Task notes updated successfully', 'success');
            // Reload the page to show updated notes
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Failed to update task notes', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update task notes', 'error');
    });
}

// View task details
function viewTaskDetails(taskId) {
    // Show loading state
    const modalContent = document.getElementById('taskModalContent');
    modalContent.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
            <span class="ml-3 text-green-100">Loading task details...</span>
        </div>
    `;
    
    // Show modal immediately with loading state
    document.getElementById('taskModal').classList.remove('hidden');
    
    fetch(`/staff/tasks/${taskId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTaskDetails(data.task);
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                    <p class="text-red-100 text-lg mb-4">Failed to load task details</p>
                    <button onclick="closeTaskModal()" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Close
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-red-100 text-lg mb-4">Failed to load task details</p>
                <button onclick="closeTaskModal()" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Close
                </button>
            </div>
        `;
    });
}

// Display task details in modal
function displayTaskDetails(task) {
    const modalContent = document.getElementById('taskModalContent');
    const isOverdue = new Date(task.due_date) < new Date() && !['completed', 'cancelled'].includes(task.status);
    
    modalContent.innerHTML = `
        ${isOverdue ? `
        <div class="bg-red-600 text-white p-4 rounded-lg mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle text-xl mr-3 animate-bounce"></i>
            <div>
                <div class="font-bold">⚠️ THIS TASK IS OVERDUE!</div>
                <div class="text-sm">Due: ${new Date(task.due_date).toLocaleString()}</div>
            </div>
        </div>
        ` : ''}
        
        <div class="space-y-4">
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Title</label>
                <p class="text-green-100 font-medium">
                    ${task.title}
                    ${isOverdue ? '<span class="text-red-400 ml-2">[OVERDUE]</span>' : ''}
                </p>
            </div>
            
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Description</label>
                <p class="text-gray-300">${task.description}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Status</label>
                    <p class="text-green-100 font-medium capitalize">
                        ${task.status.replace('_', ' ')}
                        ${isOverdue ? '<span class="text-red-400 ml-2">⚠️ OVERDUE</span>' : ''}
                    </p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due Date</label>
                    <p class="font-medium ${isOverdue ? 'text-red-400' : 'text-green-100'}">
                        ${new Date(task.due_date).toLocaleDateString()}
                        ${isOverdue ? ' ⚠️' : ''}
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Assigned By</label>
                    <p class="text-green-100 font-medium">${task.assigned_by ? task.assigned_by.name : 'System'}</p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Created</label>
                    <p class="text-green-100 font-medium">${new Date(task.created_at).toLocaleDateString()}</p>
                </div>
            </div>
            
            ${task.service_request ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Related Service Request</label>
                <p class="text-blue-100 font-medium">${task.service_request.service_type || 'N/A'}</p>
            </div>
            ` : ''}
            
            ${task.notes ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">My Notes</label>
                <p class="text-gray-300">${task.notes}</p>
            </div>
            ` : ''}
            
            <!-- Quick Actions in Modal -->
            <div class="pt-4 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide mb-2 block">Quick Actions</label>
                <div class="flex gap-2">
                    ${task.status !== 'completed' ? `
                    <button onclick="updateTaskStatusFromModal(${task.id}, 'completed')" 
                            class="${isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'} text-white px-3 py-2 rounded text-sm">
                        <i class="fas fa-check mr-1"></i>
                        ${isOverdue ? 'Complete Overdue Task' : 'Mark Complete'}
                    </button>
                    ` : ''}
                    
                    ${task.status === 'pending' ? `
                    <button onclick="updateTaskStatusFromModal(${task.id}, 'in_progress')" 
                            class="bg-orange-600 text-white px-3 py-2 rounded text-sm hover:bg-orange-700">
                        <i class="fas fa-play mr-1"></i>
                        Start Work
                    </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// Update task status from modal and close
function updateTaskStatusFromModal(taskId, status) {
    updateTaskStatus(taskId, status);
    setTimeout(() => {
        closeTaskModal();
        // Optional: Refresh the page to show updated status
        location.reload();
    }, 1000);
}

// Close task modal
function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    modal.classList.add('hidden');
    
    // Clear the modal content
    document.getElementById('taskModalContent').innerHTML = '';
    
    // Optional: Add a subtle animation effect
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.opacity = '1';
    }, 150);
    
    // Ensure we're back to the main tasks view
    console.log('Returned to My Tasks module');
}

// Filtering
function clearFilters() {
    document.getElementById('filterStatus').value = '';
    filterTasks();
}

function filterTasks() {
    const statusFilter = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('[data-task-id]');
    
    cards.forEach(card => {
        let show = true;
        
        if (statusFilter) {
            if (statusFilter === 'overdue') {
                // Show only overdue tasks
                show = card.dataset.overdue === 'true';
            } else {
                // Show by status
                show = card.dataset.status === statusFilter;
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Get status color class
function getStatusColor(status) {
    switch (status) {
        case 'pending':
            return 'bg-red-600 text-red-100';
        case 'confirmed':
            return 'bg-blue-600 text-blue-100';
        case 'assigned':
            return 'bg-yellow-600 text-yellow-100';
        case 'in_progress':
            return 'bg-orange-600 text-orange-100';
        case 'completed':
            return 'bg-green-600 text-green-100';
        default:
            return 'bg-gray-600 text-gray-100';
    }
}

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

// Add event listeners for filters
document.getElementById('filterStatus').addEventListener('change', filterTasks);

// Close modal when clicking outside
document.getElementById('taskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTaskModal();
    }
});

// Add ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('taskModal');
        if (!modal.classList.contains('hidden')) {
            closeTaskModal();
        }
    }
});
</script>

<!-- Add custom CSS for enhanced overdue styling -->
<style>
@keyframes urgent-glow {
    0%, 100% { box-shadow: 0 0 5px rgba(239, 68, 68, 0.5); }
    50% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.8); }
}

[data-overdue="true"] {
    animation: urgent-glow 2s infinite;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/staff/tasks/index.blade.php ENDPATH**/ ?>