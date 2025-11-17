<?php $__env->startSection('title', 'My Food Orders - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">My Food Orders</h1>
                <p class="text-gray-400">Track your current and past food orders</p>
            </div>
            
            <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-bold shadow-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>Browse Menu</span>
            </a>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="flex space-x-2 bg-gray-800 p-2 rounded-lg shadow-xl">
                <a href="<?php echo e(route('guest.food-orders.orders', ['tab' => 'active'])); ?>"
                   class="flex-1 px-6 py-3 text-center font-semibold rounded-lg transition-all duration-200 <?php echo e($tab === 'active' ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700'); ?>">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Active Orders</span>
                        <?php if($activeCount > 0): ?>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full <?php echo e($tab === 'active' ? 'bg-white text-blue-600' : 'bg-blue-600 text-white'); ?>"><?php echo e($activeCount); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <a href="<?php echo e(route('guest.food-orders.orders', ['tab' => 'completed'])); ?>"
                   class="flex-1 px-6 py-3 text-center font-semibold rounded-lg transition-all duration-200 <?php echo e($tab === 'completed' ? 'bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg' : 'text-gray-400 hover:text-white hover:bg-gray-700'); ?>">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Completed Orders</span>
                        <?php if($completedCount > 0): ?>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full <?php echo e($tab === 'completed' ? 'bg-white text-green-600' : 'bg-green-600 text-white'); ?>"><?php echo e($completedCount); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </div>

        <?php if($orders->count() > 0): ?>
        <!-- Orders List -->
        <div class="space-y-6" id="orders-list">
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div id="order-<?php echo e($order->id); ?>" class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden hover:shadow-green-500/10 transition-all duration-300">
                <!-- Order Header -->
                <div class="p-6 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <h2 class="text-2xl font-bold text-white">
                                    Order #<?php echo e($order->order_number); ?>

                                </h2>
                                
                                <!-- Status Badge -->
                                <span class="px-3 py-1.5 text-sm rounded-full font-bold
                                    <?php switch($order->status):
                                        case ('pending'): ?> bg-yellow-600 text-white <?php break; ?>
                                        <?php case ('confirmed'): ?> bg-blue-500 text-white <?php break; ?>
                                        <?php case ('preparing'): ?> bg-blue-600 text-white <?php break; ?>
                                        <?php case ('ready'): ?> bg-purple-600 text-white <?php break; ?>
                                        <?php case ('delivered'): ?> bg-green-600 text-white <?php break; ?>
                                        <?php case ('completed'): ?> bg-green-600 text-white <?php break; ?>
                                        <?php case ('cancelled'): ?> bg-red-600 text-white <?php break; ?>
                                    <?php endswitch; ?>">
                                    <?php echo e(str_replace('_', ' ', ucfirst($order->status))); ?>

                                </span>
                            </div>
                            
                            <div class="text-sm text-gray-300 space-y-1">
                                <p><strong class="text-gray-200">Placed:</strong> <?php echo e($order->created_at->format('M j, Y \a\t g:i A')); ?></p>
                                <p><strong class="text-gray-200">Delivery:</strong> <?php echo e(str_replace('_', ' ', ucfirst($order->delivery_type))); ?>

                                    <?php if($order->delivery_location): ?> - <?php echo e($order->delivery_location); ?> <?php endif; ?>
                                </p>
                                <?php if($order->requested_delivery_time): ?>
                                <p><strong class="text-gray-200">Requested Time:</strong> <?php echo e($order->requested_delivery_time->format('M j \a\t g:i A')); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-3xl font-bold text-green-400">
                                <?php echo e($order->formatted_total_amount); ?>

                            </div>
                            <div class="text-sm text-gray-400">
                                <?php echo e($order->orderItems->sum('quantity')); ?> item<?php echo e($order->orderItems->sum('quantity') !== 1 ? 's' : ''); ?>

                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <?php $__currentLoopData = $order->orderItems->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-700/30 rounded-lg">
                            <?php if($item->menuItem->image): ?>
                            <img src="<?php echo e(asset('storage/' . $item->menuItem->image)); ?>" 
                                 alt="<?php echo e($item->menuItem->name); ?>" 
                                 class="w-14 h-14 object-cover rounded-lg shadow-lg">
                            <?php else: ?>
                            <div class="w-14 h-14 bg-gray-700 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-white truncate"><?php echo e($item->menuItem->name); ?></p>
                                <p class="text-sm text-gray-400">
                                    Qty: <?php echo e($item->quantity); ?> × <?php echo e($item->formatted_price); ?>

                                </p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <?php if($order->orderItems->count() > 6): ?>
                    <p class="text-sm text-gray-400 mb-4">
                        ... and <?php echo e($order->orderItems->count() - 6); ?> more item<?php echo e($order->orderItems->count() - 6 !== 1 ? 's' : ''); ?>

                    </p>
                    <?php endif; ?>
                    
                    <?php if($order->special_instructions): ?>
                    <div class="mb-4 p-4 bg-yellow-900/30 border border-yellow-600/50 rounded-lg">
                        <p class="text-sm text-yellow-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <strong>Special Instructions:</strong> <?php echo e($order->special_instructions); ?>

                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-between items-center gap-3">
                        <div class="flex flex-wrap gap-3">
                            <a href="<?php echo e(route('guest.food-orders.show', $order)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Details
                            </a>
                            
                            <?php if(in_array($order->status, ['delivered', 'completed'])): ?>
                            <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all"
                                    onclick="showReorderModal('<?php echo e($order->id); ?>')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reorder
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($order->status === 'pending'): ?>
                        <button type="button"
                                onclick="cancelOrder('<?php echo e($order->id); ?>', '<?php echo e(route('guest.food-orders.cancel', $order)); ?>')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel Order
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    
    <!-- Pagination -->
    <?php if($orders->hasPages()): ?>
    <div class="mt-8">
        <?php echo e($orders->links()); ?>

    </div>
    <?php endif; ?>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <svg class="w-32 h-32 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-3">No orders yet</h2>
                <p class="text-gray-400 mb-8 text-lg">Start by exploring our delicious menu</p>
                <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-bold text-lg shadow-xl transition-all duration-200 transform hover:scale-105">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Browse Menu</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-xl shadow-2xl max-w-md w-full border border-gray-700">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-white mb-4 flex items-center">
                    <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reorder Items
                </h3>
                <p class="text-gray-300 mb-6">This will add all items from this order to your current cart.</p>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeReorderModal()" 
                            class="px-6 py-2 text-gray-300 hover:text-white border border-gray-600 hover:border-gray-500 rounded-lg font-semibold transition-all">
                        Cancel
                    </button>
                    <button onclick="confirmReorder()" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

function showReorderModal(orderId) {
    currentOrderId = orderId;
    document.getElementById('reorderModal').classList.remove('hidden');
}

function closeReorderModal() {
    currentOrderId = null;
    document.getElementById('reorderModal').classList.add('hidden');
}

function confirmReorder() {
    if (currentOrderId) {
        // In a real implementation, you would make an AJAX call to reorder
        alert('Reorder functionality would be implemented here to add items to cart');
        closeReorderModal();
    }
}

// Close modal when clicking outside
document.getElementById('reorderModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReorderModal();
    }
});

// Cancel Order Function
function cancelOrder(orderId, cancelUrl) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    const orderCard = document.getElementById('order-' + orderId);
    const cancelButton = event.target.closest('button');
    
    // Disable button and show loading state
    if (cancelButton) {
        cancelButton.disabled = true;
        cancelButton.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Cancelling...
        `;
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '';
    
    // Create form data
    const formData = new FormData();
    formData.append('_token', token);
    
    // Send cancel request
    fetch(cancelUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        // Try to parse as JSON, if it fails, just return success
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                // If not JSON, assume success if status is 200
                return { success: true };
            }
        });
    })
    .then(data => {
        // Show success notification
        showNotification('Order cancelled successfully', 'success');
        
        // Remove all cancelled order cards (including this one)
        removeAllCancelledOrders(orderId);
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to cancel order. Please try again.', 'error');
        
        // Re-enable button
        if (cancelButton) {
            cancelButton.disabled = false;
            cancelButton.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancel Order
            `;
        }
    });
}

// Remove all cancelled orders from the DOM
function removeAllCancelledOrders(currentOrderId) {
    // First, remove the current order
    const currentOrderCard = document.getElementById('order-' + currentOrderId);
    
    if (currentOrderCard) {
        currentOrderCard.style.transition = 'all 0.5s ease-out';
        currentOrderCard.style.opacity = '0';
        currentOrderCard.style.transform = 'translateX(-100%)';
        
        setTimeout(() => {
            currentOrderCard.remove();
            
            // Also remove any other cancelled orders
            const allOrders = document.querySelectorAll('[id^="order-"]');
            let removedCount = 0;
            
            allOrders.forEach(order => {
                const statusBadge = order.querySelector('.px-3.py-1\\.5');
                if (statusBadge && statusBadge.textContent.trim().toLowerCase() === 'cancelled') {
                    order.style.transition = 'all 0.3s ease-out';
                    order.style.opacity = '0';
                    order.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        order.remove();
                        removedCount++;
                        
                        // Check if there are no more orders
                        checkIfEmpty();
                    }, 300);
                }
            });
            
            // Check if there are no more orders
            checkIfEmpty();
        }, 500);
    }
}

// Check if orders list is empty
function checkIfEmpty() {
    setTimeout(() => {
        const ordersList = document.getElementById('orders-list');
        if (ordersList && ordersList.children.length === 0) {
            // Reload page to show empty state
            location.reload();
        }
    }, 600);
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg text-white z-50 shadow-2xl transition-all duration-300 transform ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            ${type === 'success' ? `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            ` : type === 'error' ? `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            ` : `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            `}
            <span class="font-semibold">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/food-orders/orders.blade.php ENDPATH**/ ?>