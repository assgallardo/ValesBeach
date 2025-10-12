<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-green-50 mb-2">My Service Requests</h1>
                <p class="text-green-200">Track and manage your service requests</p>
            </div>
            <a href="<?php echo e(route('guest.dashboard')); ?>" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($pendingRequests); ?></div>
            <div class="text-sm opacity-90">Pending</div>
        </div>
        <div class="bg-orange-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($inProgressRequests); ?></div>
            <div class="text-sm opacity-90">In Progress</div>
        </div>
        <div class="bg-green-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($completedRequests); ?></div>
            <div class="text-sm opacity-90">Completed</div>
        </div>
        <div class="bg-red-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($cancelledRequests); ?></div>
            <div class="text-sm opacity-90">Cancelled</div>
        </div>
        <div class="bg-gray-600 rounded-lg p-4 text-white text-center">
            <div class="text-2xl font-bold"><?php echo e($totalRequests); ?></div>
            <div class="text-sm opacity-90">Total</div>
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
                <option value="cancelled">Cancelled</option>
            </select>
            
            <button onclick="clearFilters()" class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-500">
                Clear Filters
            </button>
            
            <!-- Bulk Delete for Cancelled Requests -->
            <?php if($cancelledRequests > 0): ?>
            <button onclick="deleteAllCancelledRequests()" 
                    class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800 ml-auto">
                <i class="fas fa-trash mr-1"></i>
                Delete All Cancelled (<?php echo e($cancelledRequests); ?>)
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Service Requests List -->
    <div class="space-y-4" id="requestsContainer">
        <?php $__empty_1 = true; $__currentLoopData = $serviceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors request-card" 
             data-request-id="<?php echo e($request->id); ?>" 
             data-status="<?php echo e($request->status); ?>">
            
            <!-- Request Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-green-100 mb-2">
                        <?php echo e($request->service_name ?? $request->service_type ?? 'Service Request'); ?>

                        <?php if($request->status === 'cancelled'): ?>
                        <span class="text-red-400 text-sm ml-2">[CANCELLED]</span>
                        <?php endif; ?>
                    </h3>
                    
                    <!-- Status Badge -->
                    <div class="flex items-center space-x-3 mb-3">
                        <span class="px-3 py-1 text-sm rounded-full font-medium
                            <?php switch($request->status):
                                case ('pending'): ?>
                                    bg-yellow-600 text-yellow-100
                                    <?php break; ?>
                                <?php case ('confirmed'): ?>
                                    bg-blue-600 text-blue-100
                                    <?php break; ?>
                                <?php case ('assigned'): ?>
                                    bg-purple-600 text-purple-100
                                    <?php break; ?>
                                <?php case ('in_progress'): ?>
                                    bg-orange-600 text-orange-100
                                    <?php break; ?>
                                <?php case ('completed'): ?>
                                    bg-green-600 text-green-100
                                    <?php break; ?>
                                <?php case ('cancelled'): ?>
                                    bg-red-600 text-red-100
                                    <?php break; ?>
                                <?php default: ?>
                                    bg-gray-600 text-gray-100
                            <?php endswitch; ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $request->status))); ?>

                        </span>
                        
                        <span class="text-sm text-gray-400">
                            Request #<?php echo e($request->id); ?>

                        </span>
                    </div>
                </div>
                
                <!-- Request Actions -->
                <div class="flex gap-2">
                    <button onclick="viewRequestDetails(<?php echo e($request->id); ?>)" 
                            class="bg-blue-600 text-white px-3 py-2 text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i>
                        View
                    </button>
                    
                    <?php if($request->status === 'cancelled'): ?>
                        <!-- Cancel option for cancelled requests (to show it was cancelled) -->
                        <button onclick="cancelRequest(<?php echo e($request->id); ?>)" 
                                class="bg-gray-600 text-white px-3 py-2 text-sm rounded-lg hover:bg-gray-700 transition-colors"
                                title="Request already cancelled" disabled>
                            <i class="fas fa-times mr-1"></i>
                            Cancelled
                        </button>
                        <!-- Delete option for cancelled requests -->
                        <button onclick="deleteRequest(<?php echo e($request->id); ?>)" 
                                class="bg-red-700 text-white px-3 py-2 text-sm rounded-lg hover:bg-red-800 transition-colors"
                                title="Permanently delete this request">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                    <?php else: ?>
                        <!-- Cancel option for all other requests -->
                        <button onclick="cancelRequest(<?php echo e($request->id); ?>)" 
                                class="bg-red-600 text-white px-3 py-2 text-sm rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Cancel
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Request Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Description -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Description</label>
                    <p class="text-gray-300 text-sm">
                        <?php echo e($request->description ? Str::limit($request->description, 100) : 'No description provided'); ?>

                    </p>
                </div>

                <!-- Scheduled Date -->
                <?php if($request->scheduled_date): ?>
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Scheduled Date</label>
                    <p class="text-green-100 font-medium">
                        <?php echo e(\Carbon\Carbon::parse($request->scheduled_date)->format('M d, Y')); ?>

                    </p>
                </div>
                <?php endif; ?>

                <!-- Request Date -->
                <div class="space-y-1">
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Requested</label>
                    <p class="text-gray-400 text-sm">
                        <?php echo e($request->created_at->format('M d, Y H:i')); ?>

                    </p>
                </div>
            </div>

            <?php if($request->special_requests): ?>
            <div class="mt-4 pt-3 border-t border-gray-700">
                <label class="text-xs text-gray-400 uppercase tracking-wide">Special Requests</label>
                <p class="text-gray-300 text-sm mt-1"><?php echo e($request->special_requests); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <i class="fas fa-concierge-bell text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg mb-4">No service requests found</p>
            <p class="text-gray-500 text-sm mb-6">You haven't made any service requests yet</p>
            <?php if(Route::has('guest.services.index')): ?>
            <a href="<?php echo e(route('guest.services.index')); ?>" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Request a Service
            </a>
            <?php endif; ?>
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

<!-- Request Details Modal -->
<div id="requestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-green-100">Service Request Details</h3>
                    <button onclick="closeRequestModal()" 
                            class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-full p-2 transition-colors duration-200"
                            title="Close">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="requestModalContent">
                    <!-- Request details will be loaded here -->
                </div>
                
                <!-- Additional Close Button at Bottom -->
                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-end">
                    <button onclick="closeRequestModal()" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-4" id="confirmTitle">Confirm Delete</h3>
                <p class="text-gray-300 mb-6" id="confirmMessage">
                    Are you sure you want to permanently delete this service request? This action cannot be undone.
                </p>
                <div class="flex gap-3 justify-center">
                    <button onclick="closeConfirmModal()" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        Cancel
                    </button>
                    <button onclick="confirmDelete()" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                            id="confirmDeleteBtn">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let deleteAction = null;

function viewRequestDetails(requestId) {
    // Show loading state
    const modalContent = document.getElementById('requestModalContent');
    modalContent.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
            <span class="ml-3 text-green-100">Loading request details...</span>
        </div>
    `;
    
    // Show modal immediately with loading state
    document.getElementById('requestModal').classList.remove('hidden');
    
    fetch(`/guest/service-requests/${requestId}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRequestDetails(data.request);
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                    <p class="text-red-100 text-lg mb-4">Failed to load request details</p>
                    <button onclick="closeRequestModal()" 
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
                <p class="text-red-100 text-lg mb-4">Failed to load request details</p>
                <button onclick="closeRequestModal()" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                    Close
                </button>
            </div>
        `;
    });
}

// Display request details in modal
function displayRequestDetails(request) {
    const modalContent = document.getElementById('requestModalContent');
    
    modalContent.innerHTML = `
        <div class="space-y-4">
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Service Type</label>
                <p class="text-green-100 font-medium">${request.service_type || request.service_name || 'N/A'}</p>
            </div>
            
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Status</label>
                <p class="font-medium capitalize">
                    <span class="px-3 py-1 text-sm rounded-full
                        ${getStatusColor(request.status)}">
                        ${request.status.replace('_', ' ')}
                    </span>
                </p>
            </div>
            
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Description</label>
                <p class="text-gray-300">${request.description || 'No description provided'}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Request ID</label>
                    <p class="text-green-100 font-medium">#${request.id}</p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide">Created Date</label>
                    <p class="text-green-100 font-medium">${new Date(request.created_at).toLocaleDateString()}</p>
                </div>
            </div>
            
            ${request.scheduled_date ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Scheduled Date</label>
                <p class="text-green-100 font-medium">${new Date(request.scheduled_date).toLocaleDateString()}</p>
            </div>
            ` : ''}
            
            ${request.guests_count ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Number of Guests</label>
                <p class="text-green-100 font-medium">${request.guests_count}</p>
            </div>
            ` : ''}
            
            ${request.special_requests ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Special Requests</label>
                <p class="text-gray-300">${request.special_requests}</p>
            </div>
            ` : ''}
            
            ${request.manager_notes ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Manager Notes</label>
                <p class="text-gray-300">${request.manager_notes}</p>
            </div>
            ` : ''}
            
            ${request.cancelled_at ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Cancelled Date</label>
                <p class="text-red-100 font-medium">${new Date(request.cancelled_at).toLocaleDateString()}</p>
            </div>
            ` : ''}
            
            ${request.completed_at ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide">Completed Date</label>
                <p class="text-green-100 font-medium">${new Date(request.completed_at).toLocaleDateString()}</p>
            </div>
            ` : ''}
        </div>
    `;
}

// Close request modal
function closeRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.classList.add('hidden');
    
    // Clear the modal content
    document.getElementById('requestModalContent').innerHTML = '';
}

// Get status color class
function getStatusColor(status) {
    switch (status) {
        case 'pending':
            return 'bg-yellow-600 text-yellow-100';
        case 'confirmed':
            return 'bg-blue-600 text-blue-100';
        case 'assigned':
            return 'bg-purple-600 text-purple-100';
        case 'in_progress':
            return 'bg-orange-600 text-orange-100';
        case 'completed':
            return 'bg-green-600 text-green-100';
        case 'cancelled':
            return 'bg-red-600 text-red-100';
        default:
            return 'bg-gray-600 text-gray-100';
    }
}

function cancelRequest(requestId) {
    if (confirm('Are you sure you want to cancel this service request?')) {
        fetch(`/guest/service-requests/${requestId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Service request cancelled successfully', 'success');
                
                // Remove the cancelled request from the UI immediately
                const requestCard = document.querySelector(`[data-request-id="${requestId}"]`);
                if (requestCard) {
                    requestCard.style.opacity = '0';
                    requestCard.style.transform = 'translateX(-100%)';
                    requestCard.style.transition = 'all 0.3s ease-out';
                    
                    setTimeout(() => {
                        requestCard.remove();
                        updateStats(); // Update the stats counters
                    }, 300);
                }
            } else {
                showNotification(data.message || 'Failed to cancel request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to cancel request', 'error');
        });
    }
}

function deleteRequest(requestId) {
    document.getElementById('confirmTitle').textContent = 'Delete Service Request';
    document.getElementById('confirmMessage').textContent = 
        'Are you sure you want to permanently delete this service request? This action cannot be undone.';
    
    deleteAction = () => {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Deleting...';
        
        fetch(`/guest/service-requests/${requestId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Service request deleted successfully', 'success');
                
                // Remove the card from the UI
                const card = document.querySelector(`[data-request-id="${requestId}"]`);
                if (card) {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        updateStats(); // Update the stats counters
                    }, 300);
                }
                closeConfirmModal();
            } else {
                showNotification(data.message || 'Failed to delete request', 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Delete';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to delete request', 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Delete';
        });
    };
    
    document.getElementById('confirmModal').classList.remove('hidden');
}

function deleteAllCancelledRequests() {
    const cancelledCount = document.querySelectorAll('[data-status="cancelled"]').length;
    
    document.getElementById('confirmTitle').textContent = 'Delete All Cancelled Requests';
    document.getElementById('confirmMessage').textContent = 
        `Are you sure you want to permanently delete all ${cancelledCount} cancelled service requests? This action cannot be undone.`;
    
    deleteAction = () => {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Deleting...';
        
        fetch('/guest/service-requests/delete-cancelled', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${data.deleted_count} cancelled requests deleted successfully`, 'success');
                
                // Remove all cancelled cards from the UI
                const cancelledCards = document.querySelectorAll('[data-status="cancelled"]');
                cancelledCards.forEach(card => {
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                });
                
                // Update stats immediately
                setTimeout(() => {
                    updateStats();
                    // Hide the bulk delete button since there are no more cancelled requests
                    const bulkDeleteBtn = document.querySelector('button[onclick="deleteAllCancelledRequests()"]');
                    if (bulkDeleteBtn) {
                        bulkDeleteBtn.style.display = 'none';
                    }
                }, 400);
                
                closeConfirmModal();
            } else {
                showNotification(data.message || 'Failed to delete cancelled requests', 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Delete';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to delete cancelled requests', 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Delete';
        });
    };
    
    document.getElementById('confirmModal').classList.remove('hidden');
}

function confirmDelete() {
    if (deleteAction) {
        deleteAction();
    }
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    deleteAction = null;
}

function clearFilters() {
    document.getElementById('filterStatus').value = '';
    filterRequests();
}

function filterRequests() {
    const statusFilter = document.getElementById('filterStatus').value;
    const cards = document.querySelectorAll('.request-card');
    
    cards.forEach(card => {
        let show = true;
        
        if (statusFilter && card.dataset.status !== statusFilter) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

function updateStats() {
    // Count the remaining cards by status
    const allCards = document.querySelectorAll('.request-card');
    const pendingCards = document.querySelectorAll('[data-status="pending"], [data-status="confirmed"]');
    const inProgressCards = document.querySelectorAll('[data-status="assigned"], [data-status="in_progress"]');
    const completedCards = document.querySelectorAll('[data-status="completed"]');
    const cancelledCards = document.querySelectorAll('[data-status="cancelled"]');
    
    // Update the stat counters
    const pendingCounter = document.querySelector('.bg-blue-600 .text-2xl');
    const inProgressCounter = document.querySelector('.bg-orange-600 .text-2xl');
    const completedCounter = document.querySelector('.bg-green-600 .text-2xl');
    const cancelledCounter = document.querySelector('.bg-red-600 .text-2xl');
    const totalCounter = document.querySelector('.bg-gray-600 .text-2xl');
    
    if (pendingCounter) pendingCounter.textContent = pendingCards.length;
    if (inProgressCounter) inProgressCounter.textContent = inProgressCards.length;
    if (completedCounter) completedCounter.textContent = completedCards.length;
    if (cancelledCounter) cancelledCounter.textContent = cancelledCards.length;
    if (totalCounter) totalCounter.textContent = allCards.length;
    
    // Update the bulk delete button text
    const bulkDeleteBtn = document.querySelector('button[onclick="deleteAllCancelledRequests()"]');
    if (bulkDeleteBtn) {
        if (cancelledCards.length > 0) {
            bulkDeleteBtn.innerHTML = `<i class="fas fa-trash mr-1"></i>Delete All Cancelled (${cancelledCards.length})`;
            bulkDeleteBtn.style.display = 'inline-block';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }
    
    // Show empty state if no cards remain
    if (allCards.length === 0) {
        const container = document.getElementById('requestsContainer');
        container.innerHTML = `
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-concierge-bell text-4xl text-gray-600 mb-4"></i>
                <p class="text-gray-400 text-lg mb-4">No service requests found</p>
                <p class="text-gray-500 text-sm mb-6">You haven't made any service requests yet</p>
                <a href="<?php echo e(route('guest.services.index')); ?>" 
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Request a Service
                </a>
            </div>
        `;
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Event listeners
document.getElementById('filterStatus').addEventListener('change', filterRequests);

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRequestModal();
    }
});

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRequestModal();
        closeConfirmModal();
    }
});

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/services/history.blade.php ENDPATH**/ ?>