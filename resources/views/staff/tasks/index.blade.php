@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50 mb-2">My Tasks</h1>
        <p class="text-green-200">View and manage your assigned tasks.</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-red-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold">{{ $pendingTasks }}</div>
            <div class="text-sm opacity-90">Pending</div>
        </div>
        <div class="bg-orange-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold">{{ $inProgressTasks }}</div>
            <div class="text-sm opacity-90">In Progress</div>
        </div>
        <div class="bg-green-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold">{{ $completedTasks }}</div>
            <div class="text-sm opacity-90">Completed Today</div>
        </div>
        <div class="bg-red-800 rounded-lg p-4 text-white text-center animate-pulse">
            <div class="text-2xl font-bold">{{ $overdueTasks }}</div>
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
                <option value="overdue">Overdue</option>
            </select>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-500">
                Clear Filters
            </button>
            
            <div class="ml-auto">
                <button onclick="toggleCompletedTasks()" id="completedTasksBtn" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Completed Tasks
                    <span class="ml-2 bg-green-800 px-2 py-0.5 rounded-full text-xs font-bold">{{ $completedTasks }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="flex justify-end mb-4">
        <div class="bg-gray-800 rounded-lg p-2 flex gap-2">
            <button onclick="setViewMode('compact')" id="compactViewBtn" 
                    class="px-3 py-1 text-xs rounded bg-green-600 text-white">
                <i class="fas fa-th mr-1"></i>Compact
            </button>
            <button onclick="setViewMode('list')" id="listViewBtn" 
                    class="px-3 py-1 text-xs rounded bg-gray-600 text-gray-300 hover:bg-gray-500">
                <i class="fas fa-list mr-1"></i>List
            </button>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4" id="tasksContainer">
        @forelse($tasks as $task)
        @php
            $isOverdue = $task->due_date < now() && !in_array($task->status, ['completed', 'cancelled']);
            $hoursOverdue = $isOverdue ? $task->due_date->diffInHours(now()) : 0;
        @endphp
        
        <div class="rounded-lg p-4 hover:bg-gray-750 transition-colors 
                    {{ $isOverdue ? 'bg-red-900 border-2 border-red-500 animate-pulse' : 'bg-gray-800' }}" 
             data-task-id="{{ $task->id }}" 
             data-status="{{ $task->status }}"
             data-overdue="{{ $isOverdue ? 'true' : 'false' }}">
            
            <!-- Overdue Alert Banner -->
            @if($isOverdue)
            <div class="bg-red-600 text-white px-3 py-2 rounded-lg mb-3 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-lg mr-2 animate-bounce"></i>
                    <div>
                        <div class="font-bold text-sm">⚠️ OVERDUE!</div>
                        <div class="text-xs">
                            Due {{ $task->due_date->diffForHumans() }}
                            @if($hoursOverdue > 24)
                                ({{ floor($hoursOverdue / 24) }}d)
                            @else
                                ({{ $hoursOverdue }}h)
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right text-xs">
                    <div class="opacity-90">Due:</div>
                    <div class="font-medium">{{ $task->due_date->format('M d, H:i') }}</div>
                </div>
            </div>
            @endif
            
            <!-- Task Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-green-100 mb-2 truncate">
                        @if($task->task_type === 'housekeeping')
                            <i class="fas fa-broom text-purple-400 mr-2"></i>
                        @endif
                        {{ $task->title }}
                        @if($isOverdue)
                            <span class="text-red-400 text-xs ml-2">[OVERDUE]</span>
                        @endif
                    </h3>
                    
                    <!-- Task Type Badge -->
                    @if($task->task_type === 'housekeeping')
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-purple-900 text-purple-200 mb-2">
                        <i class="fas fa-broom mr-1"></i>
                        Housekeeping
                    </span>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="flex items-center space-x-2 mb-2">
                        <select onchange="updateTaskStatus({{ $task->id }}, this.value)" 
                                class="px-2 py-1 text-xs rounded-full border-none font-medium {{ $task->status_color }}">
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $task->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="assigned" {{ $task->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        
                        @if($task->status === 'confirmed')
                        <span class="px-2 py-1 text-xs rounded bg-green-600 text-green-100 flex items-center">
                            <i class="fas fa-check-circle mr-1"></i>
                            Confirmed
                        </span>
                        @endif
                        
                        @if($isOverdue)
                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-600 text-white animate-pulse">
                            <i class="fas fa-clock mr-1"></i>
                            OVERDUE
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Task Actions -->
                <div class="flex gap-2 ml-2">
                    <button onclick="viewTaskDetails({{ $task->id }})" 
                            class="inline-flex items-center px-2 py-1 {{ $isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white text-xs rounded-lg transition-colors" 
                            title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    
                    @if(!in_array($task->status, ['completed', 'cancelled']))
                    <button onclick="cancelTask({{ $task->id }})" 
                            class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition-colors" 
                            title="Cancel Task">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
            </div>

            <!-- Task Content -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                <!-- Description -->
                <div class="col-span-2 space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Description</label>
                    <p class="text-gray-300 text-xs line-clamp-2">{{ Str::limit($task->description, 80) }}</p>
                </div>

                <!-- Assigned By -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Assigned By</label>
                    <p class="text-green-100 text-xs font-medium truncate">{{ $task->assignedBy->name ?? 'System' }}</p>
                </div>

                <!-- Guest/Requestor Information -->
                @if($task->task_type === 'housekeeping' && $task->booking)
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">
                        <i class="fas fa-broom mr-1"></i>Facility
                    </label>
                    <p class="text-purple-100 text-xs font-medium truncate">
                        <i class="fas fa-door-open text-purple-400 mr-1"></i>
                        {{ $task->booking->room->name ?? 'N/A' }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $task->booking->room->category ?? '' }}</p>
                </div>
                @elseif($task->serviceRequest)
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Guest</label>
                    <p class="text-blue-100 text-xs font-medium truncate">
                        <i class="fas fa-user text-blue-400 mr-1"></i>
                        {{ $task->serviceRequest->guest_name ?? $task->serviceRequest->guest->name ?? 'N/A' }}
                    </p>
                </div>
                @else
                <!-- Due Date with Enhanced Overdue Display -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Due Date</label>
                    <p class="text-xs font-medium {{ $isOverdue ? 'text-red-400 font-bold' : 'text-green-100' }}">
                        {{ $task->due_date->format('M d, H:i') }}
                    </p>
                    <p class="text-xs {{ $isOverdue ? 'text-red-300 font-medium' : 'text-gray-400' }}">
                        {{ $task->due_date->diffForHumans() }}
                        @if($isOverdue)
                            <span class="text-red-400 font-bold ml-1">⚠️</span>
                        @endif
                    </p>
                </div>
                @endif
                
                <!-- Guest Name for Housekeeping -->
                @if($task->task_type === 'housekeeping' && $task->booking)
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Guest</label>
                    <p class="text-purple-100 text-xs font-medium truncate">
                        <i class="fas fa-user text-purple-400 mr-1"></i>
                        {{ $task->booking->user->name ?? 'N/A' }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Due Date for service requests (moved to second row) -->
            @if($task->serviceRequest)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs mt-3">
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Due Date</label>
                    <p class="text-xs font-medium {{ $isOverdue ? 'text-red-400 font-bold' : 'text-green-100' }}">
                        {{ $task->due_date->format('M d, H:i') }}
                    </p>
                    <p class="text-xs {{ $isOverdue ? 'text-red-300 font-medium' : 'text-gray-400' }}">
                        {{ $task->due_date->diffForHumans() }}
                        @if($isOverdue)
                            <span class="text-red-400 font-bold ml-1">⚠️</span>
                        @endif
                    </p>
                </div>
                @if($task->serviceRequest->guest_email)
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Guest Email</label>
                    <p class="text-gray-400 text-xs truncate">
                        <i class="fas fa-envelope text-gray-500 mr-1"></i>
                        {{ $task->serviceRequest->guest_email }}
                    </p>
                </div>
                @endif
            </div>
            @endif

            <!-- Compact Notes Section -->
            @if($task->notes)
            <div class="mt-3 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Notes</label>
                <p class="text-gray-300 text-xs mt-1 line-clamp-2">{{ $task->notes }}</p>
            </div>
            @endif

            <!-- Compact Footer with Quick Actions -->
            <div class="mt-3 pt-3 border-t border-gray-700 flex items-center justify-between">
                <div class="text-xs text-gray-400">
                    Created: {{ $task->created_at->format('M d, H:i') }}
                </div>
                <div class="flex gap-2">
                    @if($task->status !== 'completed')
                    <button onclick="updateTaskStatus({{ $task->id }}, 'completed')" 
                            class="px-2 py-1 {{ $isOverdue ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-xs rounded transition-colors" 
                            title="Mark Complete">
                        <i class="fas fa-check mr-1"></i>Complete
                    </button>
                    @endif
                    @if($task->status === 'pending')
                    <button onclick="updateTaskStatus({{ $task->id }}, 'in_progress')" 
                            class="px-2 py-1 bg-orange-600 hover:bg-orange-700 text-white text-xs rounded transition-colors" 
                            title="Start Work">
                        <i class="fas fa-play mr-1"></i>Start
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <i class="fas fa-tasks text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">No tasks assigned to you</p>
            <p class="text-gray-500 text-sm">Tasks will appear here when managers assign them to you</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
    @endif
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
// Cancel task
function cancelTask(taskId) {
    if (confirm('Are you sure you want to cancel this task? This action cannot be undone.')) {
        fetch(`/staff/tasks/${taskId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Task cancelled successfully', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Failed to cancel task', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to cancel task', 'error');
        });
    }
}

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
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Assigned By</label>
                    <p class="text-green-100 font-medium">${task.assigned_by ? task.assigned_by.name : 'System'}</p>
                </div>
                
                ${task.service_request && (task.service_request.guest_name || (task.service_request.guest && task.service_request.guest.name)) ? `
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Requested By (Guest)</label>
                    <p class="text-blue-100 font-medium">
                        <i class="fas fa-user text-blue-400 mr-1"></i>
                        ${task.service_request.guest_name || task.service_request.guest.name}
                    </p>
                    ${task.service_request.guest_email ? `
                    <p class="text-gray-400 text-xs mt-1">
                        <i class="fas fa-envelope text-gray-500 mr-1"></i>
                        ${task.service_request.guest_email}
                    </p>
                    ` : ''}
                </div>
                ` : `
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Created</label>
                    <p class="text-green-100 font-medium">${new Date(task.created_at).toLocaleDateString()}</p>
                </div>
                `}
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

// View mode toggle
function setViewMode(mode) {
    const container = document.getElementById('tasksContainer');
    const compactBtn = document.getElementById('compactViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (mode === 'compact') {
        container.className = 'grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4';
        compactBtn.className = 'px-3 py-1 text-xs rounded bg-green-600 text-white';
        listBtn.className = 'px-3 py-1 text-xs rounded bg-gray-600 text-gray-300 hover:bg-gray-500';
    } else {
        container.className = 'space-y-4';
        listBtn.className = 'px-3 py-1 text-xs rounded bg-green-600 text-white';
        compactBtn.className = 'px-3 py-1 text-xs rounded bg-gray-600 text-gray-300 hover:bg-gray-500';
    }
    
    // Save preference
    localStorage.setItem('taskViewMode', mode);
}

// Load saved view mode on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('taskViewMode') || 'compact';
    setViewMode(savedMode);
    
    // Hide completed tasks by default (show only active tasks)
    filterTasksByCompletion(false);
});

// Filtering
function clearFilters() {
    document.getElementById('filterStatus').value = '';
    
    // Reapply the current view mode (active or completed)
    filterTasksByCompletion(showingCompleted);
}

function filterTasks() {
    const statusFilter = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('[data-task-id]');
    
    cards.forEach(card => {
        const taskStatus = card.dataset.status;
        let show = true;
        
        // First, check if we should show based on completed view mode
        if (showingCompleted) {
            // In completed view, only show completed tasks
            show = taskStatus === 'completed';
        } else {
            // In active view, only show non-completed tasks
            show = taskStatus !== 'completed';
        }
        
        // Then apply status filter if any
        if (show && statusFilter) {
            if (statusFilter === 'overdue') {
                show = card.dataset.overdue === 'true';
            } else {
                show = taskStatus === statusFilter;
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
    
    updateEmptyState();
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

// Toggle completed tasks view
let showingCompleted = false;

function toggleCompletedTasks() {
    showingCompleted = !showingCompleted;
    filterTasksByCompletion(showingCompleted);
}

function filterTasksByCompletion(showOnlyCompleted) {
    const btn = document.getElementById('completedTasksBtn');
    const allTasks = document.querySelectorAll('[data-task-id]');
    const header = document.querySelector('h1.text-3xl');
    
    // Update button appearance
    if (showOnlyCompleted) {
        btn.innerHTML = '<i class="fas fa-list mr-2"></i>View Active Tasks<span class="ml-2 bg-blue-800 px-2 py-0.5 rounded-full text-xs font-bold">Back</span>';
        btn.className = 'bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 flex items-center';
        
        // Update header
        if (header) {
            header.innerHTML = 'Completed Tasks <span class="text-green-400 text-xl ml-2">✓</span>';
        }
    } else {
        btn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Completed Tasks<span class="ml-2 bg-green-800 px-2 py-0.5 rounded-full text-xs font-bold">{{ $completedTasks }}</span>';
        btn.className = 'bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 flex items-center';
        
        // Restore header
        if (header) {
            header.innerHTML = 'My Tasks';
        }
    }
    
    // Filter tasks based on completion status
    allTasks.forEach(task => {
        const status = task.dataset.status;
        if (showOnlyCompleted) {
            task.style.display = status === 'completed' ? 'block' : 'none';
        } else {
            task.style.display = status !== 'completed' ? 'block' : 'none';
        }
    });
    
    // Update empty state message
    updateEmptyState();
}

// Ensure a dedicated empty state element exists after the tasks container
function ensureEmptyStateElement() {
    let emptyEl = document.getElementById('tasksEmptyState');
    if (!emptyEl) {
        emptyEl = document.createElement('div');
        emptyEl.id = 'tasksEmptyState';
        emptyEl.className = 'bg-gray-800 rounded-lg p-8 text-center mt-4';
        emptyEl.style.display = 'none';
        const container = document.getElementById('tasksContainer');
        if (container && container.parentNode) {
            container.parentNode.insertBefore(emptyEl, container.nextSibling);
        }
    }
    return emptyEl;
}

function updateEmptyState() {
    const container = document.getElementById('tasksContainer');
    const visibleTasks = Array
        .from(container.querySelectorAll('[data-task-id]'))
        .filter(task => task.style.display !== 'none');

    const emptyEl = ensureEmptyStateElement();

    if (visibleTasks.length === 0) {
        // Set appropriate message
        emptyEl.innerHTML = showingCompleted
            ? '<i class="fas fa-check-circle text-4xl text-gray-600 mb-4"></i><p class="text-gray-400 text-lg">No completed tasks yet</p><p class="text-gray-500 text-sm">Completed tasks will appear here</p>'
            : '<i class="fas fa-tasks text-4xl text-gray-600 mb-4"></i><p class="text-gray-400 text-lg">No active tasks assigned to you</p><p class="text-gray-500 text-sm">Tasks will appear here when managers assign them to you</p>';

        // Show empty state element and optionally hide container
        emptyEl.style.display = 'block';
        container.style.display = 'none';
    } else {
        // Hide empty state element and show container
        emptyEl.style.display = 'none';
        container.style.display = '';
    }
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

/* Text truncation utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Compact card height consistency */
#tasksContainer > div {
    height: fit-content;
    min-height: 200px;
}

/* Smooth transitions for view mode changes */
#tasksContainer {
    transition: all 0.3s ease;
}
</style>
@endsection
