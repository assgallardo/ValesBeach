<?php $__env->startSection('title', 'Shopping Cart - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
           class="text-blue-600 hover:text-blue-800 font-semibold flex items-center space-x-1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Continue Shopping</span>
        </a>
    </div>

    <?php if(empty($cartItems)): ?>
    <!-- Empty Cart -->
    <div class="text-center py-12">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
        </svg>
        <h2 class="text-2xl font-bold text-gray-500 mb-2">Your cart is empty</h2>
        <p class="text-gray-400 mb-6">Add some delicious items from our menu</p>
        <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Browse Menu</span>
        </a>
    </div>
    <?php else: ?>
    <!-- Cart Items -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Cart Items</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cartKey => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-6 cart-item" data-cart-key="<?php echo e($cartKey); ?>">
                        <div class="flex items-start space-x-4">
                            <!-- Item Image -->
                            <div class="flex-shrink-0">
                                <?php if($item['menu_item']->image): ?>
                                <img src="<?php echo e(asset('storage/' . $item['menu_item']->image)); ?>" 
                                     alt="<?php echo e($item['menu_item']->name); ?>" 
                                     class="w-16 h-16 object-cover rounded">
                                <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Item Details -->
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900"><?php echo e($item['menu_item']->name); ?></h3>
                                
                                <?php if($item['menu_item']->description): ?>
                                <p class="text-gray-600 text-sm mt-1"><?php echo e($item['menu_item']->description); ?></p>
                                <?php endif; ?>
                                
                                <!-- Dietary Badges -->
                                <div class="flex flex-wrap gap-1 mt-2">
                                    <?php $__currentLoopData = $item['menu_item']->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo e($badge['class']); ?>">
                                        <?php echo e($badge['label']); ?>

                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                
                                <?php if($item['special_instructions']): ?>
                                <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Special Instructions:</strong> <?php echo e($item['special_instructions']); ?>

                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Quantity and Price -->
                            <div class="text-right">
                                <div class="flex items-center space-x-2 mb-2">
                                    <label class="text-sm font-medium text-gray-700">Qty:</label>
                                    <select class="quantity-select border border-gray-300 rounded px-2 py-1" 
                                            data-cart-key="<?php echo e($cartKey); ?>">
                                        <?php for($i = 0; $i <= 20; $i++): ?>
                                        <option value="<?php echo e($i); ?>" <?php echo e($i == $item['quantity'] ? 'selected' : ''); ?>>
                                            <?php echo e($i == 0 ? 'Remove' : $i); ?>

                                        </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div class="text-sm text-gray-600">
                                    <?php echo e($item['menu_item']->formatted_price); ?> each
                                </div>
                                <div class="text-lg font-bold text-gray-900 item-total">
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
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold" id="subtotal">₱<?php echo e(number_format($subtotal, 2)); ?></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Delivery fee will be calculated at checkout</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Tax will be calculated at checkout</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Items Total</span>
                        <span id="total">₱<?php echo e(number_format($subtotal, 2)); ?></span>
                    </div>
                </div>
                
                <a href="<?php echo e(route('guest.food-orders.checkout')); ?>" 
                   class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold text-center block transition duration-200">
                    Proceed to Checkout
                </a>
                
                <a href="<?php echo e(route('guest.food-orders.menu')); ?>" 
                   class="w-full mt-3 border border-gray-300 hover:border-gray-400 text-gray-700 py-3 px-4 rounded-lg font-semibold text-center block transition duration-200">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
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
});

function updateCartItem(cartKey, quantity) {
    const formData = new FormData();
    formData.append('cart_key', cartKey);
    formData.append('quantity', quantity);
    
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
            // Remove item from DOM
            document.querySelector(`[data-cart-key="${cartKey}"]`).remove();
            
            // Check if cart is empty
            if (document.querySelectorAll('.cart-item').length === 0) {
                location.reload();
            }
        }
        
        // Update totals (will be calculated properly in next iteration)
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update cart. Please try again.');
        location.reload();
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/food-orders/cart.blade.php ENDPATH**/ ?>