<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">My Service Requests</h1>
                    <p class="text-gray-400">Track and manage your service requests</p>
                </div>
                <a href="<?php echo e(route('guest.dashboard')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 text-white shadow-lg">
                <div class="text-3xl font-bold mb-1"><?php echo e($pendingRequests); ?></div>
                <div class="text-blue-100 text-sm font-medium">Pending</div>
            </div>
            <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-lg p-6 text-white shadow-lg">
                <div class="text-3xl font-bold mb-1"><?php echo e($inProgressRequests); ?></div>
                <div class="text-orange-100 text-sm font-medium">In Progress</div>
            </div>
            <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-6 text-white shadow-lg">
                <div class="text-3xl font-bold mb-1"><?php echo e($completedRequests); ?></div>
                <div class="text-green-100 text-sm font-medium">Completed</div>
            </div>
            <div class="bg-gradient-to-br from-red-600 to-red-700 rounded-lg p-6 text-white shadow-lg">
                <div class="text-3xl font-bold mb-1"><?php echo e($cancelledRequests); ?></div>
                <div class="text-red-100 text-sm font-medium">Cancelled</div>
            </div>
            <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg p-6 text-white shadow-lg">
                <div class="text-3xl font-bold mb-1"><?php echo e($totalRequests); ?></div>
                <div class="text-gray-100 text-sm font-medium">Total</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-gray-800 rounded-lg p-4 mb-6 shadow-lg">
            <div class="flex flex-wrap gap-3 items-center">
                <select id="filterStatus" class="bg-gray-700 text-white border border-gray-600 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="assigned">Assigned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                
                <button onclick="clearFilters()" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Clear Filters
                </button>
                
                <?php if($cancelledRequests > 0): ?>
                <button onclick="deleteAllCancelledRequests()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors ml-auto">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete All Cancelled (<?php echo e($cancelledRequests); ?>)
                </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Service Requests List -->
        <div class="space-y-4" id="requestsContainer">
            <?php $__empty_1 = true; $__currentLoopData = $serviceRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden request-card hover:shadow-xl transition-shadow" 
                 data-request-id="<?php echo e($request->id); ?>" 
                 data-status="<?php echo e($request->status); ?>">
                
                <div class="p-6">
                    <!-- Request Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-2xl font-bold text-white">
                                    <?php echo e($request->service->name ?? $request->service_name ?? $request->service_type ?? 'Service Request'); ?>

                                </h3>
                                
                                <!-- Price Display -->
                                <div class="text-right">
                                    <?php if($request->service && $request->service->price): ?>
                                    <div class="text-3xl font-bold text-green-400">
                                        ₱<?php echo e(number_format($request->service->price, 2)); ?>

                                    </div>
                                    <?php if($request->quantity && $request->quantity > 1): ?>
                                    <div class="text-sm text-gray-400">
                                        ₱<?php echo e(number_format($request->service->price, 2)); ?> × <?php echo e($request->quantity); ?>

                                    </div>
                                    <div class="text-xl font-semibold text-green-300">
                                        Total: ₱<?php echo e(number_format($request->service->price * $request->quantity, 2)); ?>

                                    </div>
                                    <?php endif; ?>
                                    <?php elseif($request->total_amount): ?>
                                    <div class="text-3xl font-bold text-green-400">
                                        ₱<?php echo e(number_format($request->total_amount, 2)); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Status Badge & Meta Info -->
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <span class="px-4 py-1.5 text-sm rounded-lg font-semibold
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
                                
                                <?php if($request->service && $request->service->category): ?>
                                <span class="px-3 py-1 text-xs bg-gray-700 text-gray-300 rounded-lg border border-gray-600">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $request->service->category))); ?>

                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Request Actions -->
                        <div class="flex gap-2 ml-4">
                            <button onclick="viewRequestDetails(<?php echo e($request->id); ?>)" 
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View
                            </button>
                            
                            <?php if($request->status === 'cancelled'): ?>
                            <button onclick="deleteRequest(<?php echo e($request->id); ?>)" 
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                            <?php else: ?>
                            <button onclick="cancelRequest(<?php echo e($request->id); ?>)" 
                                    class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm rounded-lg font-medium transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Request Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 pt-4 border-t border-gray-700">
                        <!-- Description -->
                        <div>
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Description</label>
                            <p class="text-gray-300 text-sm mt-1">
                                <?php echo e($request->description ? Str::limit($request->description, 100) : 'No description provided'); ?>

                            </p>
                        </div>

                        <!-- Scheduled Date -->
                        <?php if($request->scheduled_date): ?>
                        <div>
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Scheduled Date</label>
                            <p class="text-white font-semibold mt-1">
                                <?php echo e(\Carbon\Carbon::parse($request->scheduled_date)->format('M d, Y - h:i A')); ?>

                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Request Date -->
                        <div>
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Requested On</label>
                            <p class="text-gray-300 text-sm mt-1">
                                <?php echo e($request->created_at->format('M d, Y - h:i A')); ?>

                            </p>
                        </div>
                        
                        <!-- Service Details -->
                        <?php if($request->guests_count): ?>
                        <div>
                            <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Guests</label>
                            <p class="text-white font-semibold mt-1">
                                <?php echo e($request->guests_count); ?> <?php echo e($request->guests_count === 1 ? 'Guest' : 'Guests'); ?>

                            </p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if($request->special_requests): ?>
                    <div class="mt-4 pt-4 border-t border-gray-700">
                        <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Special Requests</label>
                        <p class="text-gray-300 text-sm mt-1"><?php echo e($request->special_requests); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-gray-800 rounded-lg p-12 text-center shadow-lg">
                <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-2xl font-bold text-white mb-2">No Service Requests Yet</h3>
                <p class="text-gray-400 mb-6">You haven't made any service requests yet</p>
                <?php if(Route::has('guest.services.index')): ?>
                <a href="<?php echo e(route('guest.services.index')); ?>" 
                   class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Request a Service
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($serviceRequests->hasPages()): ?>
        <div class="mt-8">
            <?php echo e($serviceRequests->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Request Details Modal -->
<div id="requestModal" class="fixed inset-0 bg-black/70 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-white">Service Request Details</h3>
                <button onclick="closeRequestModal()" 
                        class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="requestModalContent" class="text-gray-300">
                <!-- Content loaded dynamically -->
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-700 flex justify-end">
                <button onclick="closeRequestModal()" 
                        class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black/70 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6 shadow-2xl">
        <div class="text-center">
            <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-xl font-bold text-white mb-4" id="confirmTitle">Confirm Action</h3>
            <p class="text-gray-300 mb-6" id="confirmMessage">
                Are you sure you want to proceed with this action?
            </p>
            <div class="flex gap-3 justify-center">
                <button onclick="closeConfirmModal()" 
                        class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDelete()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors"
                        id="confirmDeleteBtn">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteAction = null;

function viewRequestDetails(requestId) {
    const modalContent = document.getElementById('requestModalContent');
    modalContent.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
            <span class="ml-3 text-white">Loading...</span>
        </div>
    `;
    
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
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-400 text-lg">Failed to load request details</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-400 text-lg">Failed to load request details</p>
            </div>
        `;
    });
}

function displayRequestDetails(request) {
    const modalContent = document.getElementById('requestModalContent');
    
    modalContent.innerHTML = `
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Service Type</label>
                    <p class="text-white font-semibold text-lg mt-1">${request.service_type || request.service_name || 'N/A'}</p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Status</label>
                    <p class="mt-1">
                        <span class="px-3 py-1 text-sm rounded-lg font-semibold ${getStatusColor(request.status)}">
                            ${request.status.replace('_', ' ').toUpperCase()}
                        </span>
                    </p>
                </div>
            </div>
            
            ${request.service ? `
            <div class="bg-gray-700 p-6 rounded-lg">
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium mb-3 block">Service Details & Pricing</label>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-white font-semibold text-lg">${request.service.name}</p>
                        ${request.service.category ? `<p class="text-gray-400 text-sm mt-1">${request.service.category}</p>` : ''}
                        ${request.service.duration ? `<p class="text-gray-400 text-sm mt-1">Duration: ${request.service.duration} minutes</p>` : ''}
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-green-400">₱${parseFloat(request.service.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                        ${request.quantity && request.quantity > 1 ? `
                            <p class="text-sm text-gray-400 mt-1">Quantity: ${request.quantity}</p>
                            <p class="text-xl font-semibold text-green-300 mt-1">Total: ₱${(parseFloat(request.service.price) * request.quantity).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                        ` : ''}
                    </div>
                </div>
            </div>
            ` : ''}
            
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Description</label>
                <p class="text-white mt-1">${request.description || 'No description provided'}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Request ID</label>
                    <p class="text-white font-semibold mt-1">#${request.id}</p>
                </div>
                
                <div>
                    <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Created Date</label>
                    <p class="text-white font-semibold mt-1">${new Date(request.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
            </div>
            
            ${request.scheduled_date ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Scheduled Date</label>
                <p class="text-white font-semibold mt-1">${new Date(request.scheduled_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
            </div>
            ` : ''}
            
            ${request.guests_count ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Number of Guests</label>
                <p class="text-white font-semibold mt-1">${request.guests_count} ${request.guests_count === 1 ? 'Guest' : 'Guests'}</p>
            </div>
            ` : ''}
            
            ${request.special_requests ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Special Requests</label>
                <p class="text-white mt-1">${request.special_requests}</p>
            </div>
            ` : ''}
            
            ${request.manager_notes ? `
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wide font-medium">Manager Notes</label>
                <p class="text-white mt-1">${request.manager_notes}</p>
            </div>
            ` : ''}
        </div>
    `;
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-600 text-yellow-100',
        'confirmed': 'bg-blue-600 text-blue-100',
        'assigned': 'bg-purple-600 text-purple-100',
        'in_progress': 'bg-orange-600 text-orange-100',
        'completed': 'bg-green-600 text-green-100',
        'cancelled': 'bg-red-600 text-red-100'
    };
    return colors[status] || 'bg-gray-600 text-gray-100';
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
                setTimeout(() => location.reload(), 1500);
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
                closeConfirmModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to delete request', 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to delete request', 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm';
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
                closeConfirmModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to delete cancelled requests', 'error');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to delete cancelled requests', 'error');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm';
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
        const show = !statusFilter || card.dataset.status === statusFilter;
        card.style.display = show ? 'block' : 'none';
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 shadow-lg transition-opacity duration-300 ${
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

// Close modals on outside click
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) closeRequestModal();
});

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});

// ESC key to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRequestModal();
        closeConfirmModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/guest/services/history.blade.php ENDPATH**/ ?>