<?php $__env->startSection('title', 'Shopping Cart - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Menu Cart</h1>
                <p class="text-gray-400">Review your items and proceed to checkout</p>
            </div>
            <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Continue browsing</span>
            </a>
        </div>

        <?php if(empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="bg-gray-800 rounded-2xl shadow-2xl p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="mb-6">
                    <svg class="w-32 h-32 text-gray-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-3">Your cart is empty</h2>
                <p class="text-gray-400 mb-8 text-lg">Add some delicious items from our menu to get started</p>
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
        <?php else: ?>
        <!-- Cart Items -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items List -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900">
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cart Items
                        </h2>
                    </div>
                    
                    <div class="divide-y divide-gray-700">
                        <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cartKey => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-6 cart-item hover:bg-gray-700/50 transition-colors duration-200 relative" data-cart-key="<?php echo e($cartKey); ?>">
                            <!-- Remove Button (X) -->
                            <button class="remove-item-btn absolute top-4 right-4 bg-red-600 hover:bg-red-700 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-all duration-200 hover:scale-110 z-10"
                                    data-cart-key="<?php echo e($cartKey); ?>"
                                    title="Remove item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            
                            <div class="flex items-start space-x-4">
                                <!-- Item Image -->
                                <div class="flex-shrink-0">
                                    <?php if($item['menu_item']->image): ?>
                                    <img src="<?php echo e(asset('storage/' . $item['menu_item']->image)); ?>" 
                                         alt="<?php echo e($item['menu_item']->name); ?>" 
                                         class="w-20 h-20 object-cover rounded-lg shadow-lg">
                                    <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center shadow-lg">
                                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Item Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-xl text-white mb-1"><?php echo e($item['menu_item']->name); ?></h3>
                                    
                                    <?php if($item['menu_item']->description): ?>
                                    <p class="text-gray-400 text-sm mt-1 mb-2"><?php echo e($item['menu_item']->description); ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Dietary Badges -->
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <?php $__currentLoopData = $item['menu_item']->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo e($badge['class']); ?>">
                                            <?php echo e($badge['label']); ?>

                                        </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    
                                    <?php if($item['special_instructions']): ?>
                                    <div class="mt-3 p-3 bg-yellow-900/30 border border-yellow-600/50 rounded-lg">
                                        <p class="text-sm text-yellow-200">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <strong>Special Instructions:</strong> <?php echo e($item['special_instructions']); ?>

                                        </p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Quantity and Price -->
                                <div class="text-right flex-shrink-0">
                                    <div class="flex flex-col items-end space-y-3 mb-3">
                                        <div class="flex items-center space-x-2">
                                            <label class="text-sm font-medium text-gray-300">Qty:</label>
                                            <select class="quantity-select bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" 
                                                    data-cart-key="<?php echo e($cartKey); ?>">
                                                <?php for($i = 0; $i <= 20; $i++): ?>
                                                <option value="<?php echo e($i); ?>" <?php echo e($i == $item['quantity'] ? 'selected' : ''); ?>>
                                                    <?php echo e($i == 0 ? 'Remove' : $i); ?>

                                                </option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-400 mb-2">
                                        <?php echo e($item['menu_item']->formatted_price); ?> each
                                    </div>
                                    <div class="text-2xl font-bold text-green-400 item-total">
                                        ₱<?php echo e(number_format($item['total'], 2)); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 sticky top-4 border border-gray-700">
                    <div class="flex items-center mb-6">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h2 class="text-2xl font-bold text-white">Order Summary</h2>
                    </div>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center p-3 bg-gray-700/50 rounded-lg">
                            <span class="text-gray-300 font-medium">Subtotal</span>
                            <span class="font-bold text-white text-lg" id="subtotal">₱<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-start text-sm text-gray-400 p-2">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Delivery fee will be calculated at checkout</span>
                            </div>
                            <div class="flex items-start text-sm text-gray-400 p-2">
                                <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Tax will be calculated at checkout</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-700 pt-4 mb-6">
                        <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-900/50 to-green-800/50 rounded-lg">
                            <span class="text-lg font-bold text-white">Items Total</span>
                            <span class="text-2xl font-bold text-green-400" id="total">₱<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                    </div>
                    
                    <a href="<?php echo e(route('guest.food-orders.checkout')); ?>" 
                       class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-4 px-4 rounded-lg font-bold text-center block transition-all duration-200 shadow-lg transform hover:scale-105 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Proceed to Checkout
                    </a>
                    
                    <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
                       class="w-full border-2 border-gray-600 hover:border-gray-500 bg-gray-700/30 hover:bg-gray-700/50 text-gray-300 hover:text-white py-3 px-4 rounded-lg font-semibold text-center block transition-all duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Continue browsing
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Cart Update JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity changes
    document.querySelectorAll('.quantity-select').forEach(select => {
        select.addEventListener('change', function() {
            const cartKey = this.dataset.cartKey;
            const quantity = parseInt(this.value);
            
            updateCartItem(cartKey, quantity);
        });
    });
    
    // Handle Remove Item buttons (X)
    const removeButtons = document.querySelectorAll('.remove-item-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartKey = this.dataset.cartKey;
            const itemName = this.closest('.cart-item').querySelector('h3').textContent;
            
            // Show confirmation
            if (confirm(`Remove "${itemName}" from your cart?`)) {
                updateCartItem(cartKey, 0);
            }
        });
    });
});

function updateCartItem(cartKey, quantity) {
    const formData = new FormData();
    formData.append('cart_key', cartKey);
    formData.append('quantity', quantity);
    
    // Show loading state
    const cartItem = document.querySelector(`[data-cart-key="${cartKey}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.5';
        cartItem.style.pointerEvents = 'none';
    }
    
    fetch('<?php echo e(route("guest.food-orders.cart.update")); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (quantity === 0) {
            // Show notification
            showNotification('Item removed from cart', 'success');
            
            // Fade out and remove item
            if (cartItem) {
                cartItem.style.transition = 'all 0.3s ease-out';
                cartItem.style.transform = 'translateX(-100%)';
                cartItem.style.opacity = '0';
                
                setTimeout(() => {
                    cartItem.remove();
                    
                    // Check if cart is empty
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    } else {
                        // Reload to update totals
                        location.reload();
                    }
                }, 300);
            }
        } else {
            // Show notification
            showNotification('Cart updated successfully', 'success');
            
            // Reload to update totals
            setTimeout(() => {
                location.reload();
            }, 500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update cart. Please try again.', 'error');
        
        // Restore item state
        if (cartItem) {
            cartItem.style.opacity = '1';
            cartItem.style.pointerEvents = 'auto';
        }
    });
}

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

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/food-orders/cart.blade.php ENDPATH**/ ?>