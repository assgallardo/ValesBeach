<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Food Orders Management</h1>
            <p class="text-gray-400 mt-1">Monitor and manage customer food orders</p>
        </div>
        <a href="<?php echo e(route('staff.orders.statistics')); ?>" 
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            View Statistics
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg shadow-xl p-6 transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 text-sm font-medium uppercase">Total Orders</p>
                    <p class="text-white text-3xl font-bold mt-2"><?php echo e($stats['total']); ?></p>
                </div>
                <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-lg shadow-xl p-6 transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-200 text-sm font-medium uppercase">Pending Orders</p>
                    <p class="text-white text-3xl font-bold mt-2"><?php echo e($stats['pending']); ?></p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-lg shadow-xl p-6 transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-200 text-sm font-medium uppercase">Today's Orders</p>
                    <p class="text-white text-3xl font-bold mt-2"><?php echo e($stats['today_orders']); ?></p>
                </div>
                <div class="bg-purple-500 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-lg shadow-xl p-6 transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-200 text-sm font-medium uppercase">Today's Revenue</p>
                    <p class="text-white text-3xl font-bold mt-2">₱<?php echo e(number_format($stats['today_revenue'], 2)); ?></p>
                </div>
                <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg mb-6 shadow-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><?php echo e(session('success')); ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg shadow-xl p-6 mb-6">
        <form method="GET" action="<?php echo e(route('staff.orders.index')); ?>" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>" 
                       placeholder="Order # or customer..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select id="status" 
                        name="status"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="preparing" <?php echo e(request('status') === 'preparing' ? 'selected' : ''); ?>>Preparing</option>
                    <option value="ready" <?php echo e(request('status') === 'ready' ? 'selected' : ''); ?>>Ready</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-300 mb-2">Date From</label>
                <input type="date" 
                       id="date_from" 
                       name="date_from" 
                       value="<?php echo e(request('date_from')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-300 mb-2">Date To</label>
                <input type="date" 
                       id="date_to" 
                       name="date_to" 
                       value="<?php echo e(request('date_to')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Filter
                </button>
                <a href="<?php echo e(route('staff.orders.index')); ?>" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <span class="text-white font-semibold text-lg"><?php echo e($order->order_number); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium"><?php echo e($order->customer_name); ?></div>
                                <div class="text-gray-400 text-sm mt-1"><?php echo e($order->customer_email); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-700 text-gray-300">
                                    <?php echo e($order->orderItems->count()); ?> item(s)
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-white font-bold text-lg">₱<?php echo e(number_format($order->total_amount, 2)); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($order->status === 'cancelled'): ?>
                                    <!-- Cancelled orders cannot be changed -->
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-600 text-white">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Cancelled
                                    </span>
                                <?php else: ?>
                                    <!-- Status Dropdown -->
                                    <select onchange="updateOrderStatus('<?php echo e($order->id); ?>', this.value, '<?php echo e($order->order_number); ?>')"
                                            class="px-4 py-2 rounded-lg text-sm font-bold text-white border-2 transition-all duration-200 cursor-pointer
                                                   <?php echo e($order->status === 'pending' ? 'bg-yellow-600 border-yellow-500 hover:bg-yellow-700' : 
                                                      ($order->status === 'preparing' ? 'bg-blue-600 border-blue-500 hover:bg-blue-700' : 
                                                       ($order->status === 'ready' ? 'bg-purple-600 border-purple-500 hover:bg-purple-700' : 
                                                        ($order->status === 'completed' ? 'bg-green-600 border-green-500 hover:bg-green-700' : 'bg-gray-600 border-gray-500 hover:bg-gray-700')))); ?>">
                                        <option value="pending" <?php echo e($order->status === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                        <option value="preparing" <?php echo e($order->status === 'preparing' ? 'selected' : ''); ?>>Preparing</option>
                                        <option value="ready" <?php echo e($order->status === 'ready' ? 'selected' : ''); ?>>Ready</option>
                                        <option value="completed" <?php echo e($order->status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                                    </select>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium"><?php echo e($order->created_at->format('M d, Y')); ?></div>
                                <div class="text-gray-400 text-sm mt-1"><?php echo e($order->created_at->format('h:i A')); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('staff.orders.show', $order)); ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View Details
                                    </a>
                                    
                                    <?php if($order->status === 'cancelled'): ?>
                                    <button onclick="deleteOrder('<?php echo e($order->id); ?>', '<?php echo e($order->order_number); ?>')"
                                            class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105"
                                            title="Delete this cancelled order">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-gray-400 text-lg">No orders found.</p>
                                    <p class="text-gray-500 text-sm mt-2">Try adjusting your filters or check back later.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($orders->hasPages()): ?>
            <div class="bg-gray-700 px-6 py-4 border-t border-gray-600">
                <?php echo e($orders->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden form for deletion -->
<form id="delete-order-form" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<script>
function updateOrderStatus(orderId, newStatus, orderNumber) {
    if (!confirm(`Change order ${orderNumber} status to "${newStatus.toUpperCase()}"?`)) {
        // Reset the dropdown to original value if cancelled
        location.reload();
        return;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '';
    
    // Show loading indicator
    const statusSelect = event.target;
    const originalBg = statusSelect.className;
    statusSelect.disabled = true;
    statusSelect.style.opacity = '0.6';
    
    // Send AJAX request
    fetch(`/staff/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('Order status updated successfully!', 'success');
            
            // Update dropdown styling based on new status
            statusSelect.className = 'px-4 py-2 rounded-lg text-sm font-bold text-white border-2 transition-all duration-200 cursor-pointer ';
            switch(newStatus) {
                case 'pending':
                    statusSelect.className += 'bg-yellow-600 border-yellow-500 hover:bg-yellow-700';
                    break;
                case 'preparing':
                    statusSelect.className += 'bg-blue-600 border-blue-500 hover:bg-blue-700';
                    break;
                case 'ready':
                    statusSelect.className += 'bg-purple-600 border-purple-500 hover:bg-purple-700';
                    break;
                case 'completed':
                    statusSelect.className += 'bg-green-600 border-green-500 hover:bg-green-700';
                    break;
                default:
                    statusSelect.className += 'bg-gray-600 border-gray-500 hover:bg-gray-700';
            }
            
            statusSelect.disabled = false;
            statusSelect.style.opacity = '1';
        } else {
            showNotification('Failed to update order status', 'error');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        location.reload();
    });
}

function deleteOrder(orderId, orderNumber) {
    if (!confirm(`Are you sure you want to permanently delete order ${orderNumber}?\n\nThis action cannot be undone and will also delete all related order items and payment records.`)) {
        return;
    }
    
    const form = document.getElementById('delete-order-form');
    form.action = `/staff/orders/${orderId}/delete`;
    form.submit();
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-2xl z-50 flex items-center gap-3 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    } text-white`;
    
    notification.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            ${type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            }
        </svg>
        <span class="font-semibold">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Fade out and remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Auto-hide success message after 5 seconds
setTimeout(() => {
    const successAlert = document.querySelector('.bg-green-500');
    if (successAlert) {
        successAlert.style.transition = 'opacity 0.5s ease-out';
        successAlert.style.opacity = '0';
        setTimeout(() => successAlert.remove(), 500);
    }
}, 5000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/staff/orders/index.blade.php ENDPATH**/ ?>