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
        <div class="bg-red-800 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold">{{ $overdueTasks }}</div>
            <div class="text-sm opacity-90">Overdue</div>
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
            </select>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-500">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="space-y-4" id="tasksContainer">
        @forelse($tasks as $task)
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors {{ $task->is_overdue ? 'border-l-4 border-red-500' : '' }}" 
             data-task-id="{{ $task->id }}" 
             data-status="{{ $task->status }}">
            
            <!-- Task Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-green-100 mb-2">{{ $task->title }}</h3>
                    
                    <!-- Status Badge -->
                    <div class="flex items-center space-x-3 mb-3">
                        <select onchange="updateTaskStatus({{ $task->id }}, this.value)" 
                                class="px-3 py-1 text-xs rounded-full border-none font-medium {{ $task->status_color }}">
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $task->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="assigned" {{ $task->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        
                        @if($task->status === 'confirmed')
                        <span class="px-2 py-1 text-xs rounded bg-green-600 text-green-100 flex items-center">
                            <i class="fas fa-check-circle mr-1"></i>
                            Manager Confirmed
                        </span>
                        @endif
                        
                        @if($task->is_overdue)
                        <span class="px-2 py-1 text-xs rounded bg-red-600 text-red-100">
                            Overdue
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Task Actions -->
                <div class="flex gap-2">
                    <button onclick="viewTaskDetails({{ $task->id }})" 
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors" 
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
                    <p class="text-gray-300 text-sm">{{ Str::limit($task->description, 100) }}</p>
                </div>

                <!-- Assigned By -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Assigned By</label>
                    <p class="text-green-100 font-medium">{{ $task->assignedBy->name ?? 'System' }}</p>
                </div>

                <!-- Due Date -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due Date</label>
                    <p class="text-green-100 font-medium">{{ $task->due_date->format('M d, Y H:i') }}</p>
                    <p class="text-gray-400 text-sm">{{ $task->due_date->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Task Notes -->
            @if($task->notes)
            <div class="mt-4 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide">My Notes</label>
                <p class="text-gray-300 text-sm mt-1">{{ $task->notes }}</p>
            </div>
            @endif

            <!-- Notes Input -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide">Add/Update Notes</label>
                <div class="flex gap-2 mt-2">
                    <input type="text" 
                           value="{{ $task->notes }}" 
                           placeholder="Add notes about this task..."
                           class="flex-1 bg-gray-700 text-green-100 rounded px-3 py-2 text-sm border-none"
                           data-task-id="{{ $task->id }}"
                           onkeypress="if(event.key==='Enter') updateTaskNotes({{ $task->id }}, this.value)">
                    <button onclick="updateTaskNotes({{ $task->id }}, document.querySelector('[data-task-id=\'{{ $task->id }}\']').value)" 
                            class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                        Save
                    </button>
                </div>
            </div>

            <!-- Timeline Footer -->
            <div class="mt-4 pt-3 border-t border-gray-700">
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Created: {{ $task->created_at->format('M d, Y H:i') }}</span>
                    @if($task->completed_at)
                    <span>Completed: {{ $task->completed_at->format('M d, H:i') }}</span>
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
                    <button onclick="closeTaskModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="taskModalContent">
                    <!-- Task details will be loaded here -->
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
            document.getElementById('taskModal').classList.remove('hidden');
        } else {
            showNotification(data.message || 'Failed to load task details', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to load task details', 'error');
    });
}

// Display task details in modal
function displayTaskDetails(task) {
    const modalContent = document.getElementById('taskModalContent');
    modalContent.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Title</label>
                <p class="text-green-100 font-medium">${task.title}</p>
            </div>
            
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Description</label>
                <p class="text-gray-300">${task.description}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Status</label>
                    <p class="text-green-100 font-medium capitalize">${task.status}</p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Due Date</label>
                    <p class="text-green-100 font-medium">${new Date(task.due_date).toLocaleDateString()}</p>
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
        </div>
    `;
}

// Close task modal
function closeTaskModal() {
    document.getElementById('taskModal').classList.add('hidden');
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
        
        if (statusFilter && card.dataset.status !== statusFilter) {
            show = false;
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
</script>
@endsection
