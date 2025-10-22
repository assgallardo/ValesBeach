<?php $__env->startSection('title', 'Food Menu - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Food Menu</h1>
            <p class="text-gray-600 mt-2">Delicious meals delivered to your room or pickup at our restaurant</p>
        </div>
        
        <!-- Cart Button -->
        <div class="relative">
            <a href="<?php echo e(route('guest.food-orders.cart')); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                </svg>
                <span>Cart</span>
                <span id="cart-count" class="bg-red-500 text-white rounded-full px-2 py-1 text-xs min-w-[20px] text-center">0</span>
            </a>
        </div>
    </div>

    <!-- Featured Items -->
    <?php if($featuredItems->count() > 0): ?>
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $featuredItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                <?php if($item->image): ?>
                <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-lg text-gray-900"><?php echo e($item->name); ?></h3>
                        <span class="text-lg font-bold text-blue-600"><?php echo e($item->formatted_price); ?></span>
                    </div>

                    <?php if($item->description): ?>
                    <p class="text-gray-600 text-sm mb-3"><?php echo e($item->description); ?></p>
                    <?php endif; ?>
                    
                    <!-- Dietary Badges -->
                    <div class="flex flex-wrap gap-1 mb-3">
                        <?php $__currentLoopData = $item->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-2 py-1 text-xs rounded-full <?php echo e($badge['class']); ?>">
                            <?php echo e($badge['label']); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <!-- Add to Cart Form -->
                    <form class="add-to-cart-form" data-item-id="<?php echo e($item->id); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="menu_item_id" value="<?php echo e($item->id); ?>">
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="text-sm font-medium text-gray-700">Quantity:</label>
                            <select name="quantity" class="border border-gray-300 rounded px-3 py-1">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <textarea name="special_instructions" placeholder="Special instructions (optional)" 
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3" rows="2"></textarea>
                        
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-semibold transition duration-200">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Menu Categories -->
    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 mr-3 text-blue-600">
                <?php echo $category->icon; ?>

            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900"><?php echo e($category->name); ?></h2>
                <?php if($category->description): ?>
                <p class="text-gray-600"><?php echo e($category->description); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if($category->menuItems->count() > 0): ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php $__currentLoopData = $category->menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1"><?php echo e($item->name); ?></h3>
                        <?php if($item->description): ?>
                        <p class="text-gray-600 text-sm mb-2"><?php echo e($item->description); ?></p>
                        <?php endif; ?>
                        
                        <!-- Dietary and Info Badges -->
                        <div class="flex flex-wrap gap-1 mb-3">
                            <?php $__currentLoopData = $item->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($badge['class']); ?>">
                                <?php echo e($badge['label']); ?>

                            </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if($item->calories): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                <?php echo e($item->calories); ?> cal
                            </span>
                            <?php endif; ?>
                            
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                <?php echo e($item->preparation_time); ?> min
                            </span>
                        </div>
                        
                        <?php if($item->ingredients && count($item->ingredients) > 0): ?>
                        <p class="text-xs text-gray-500 mb-2">
                            <strong>Ingredients:</strong> <?php echo e(implode(', ', $item->ingredients)); ?>

                        </p>
                        <?php endif; ?>
                        
                        <?php if($item->allergens && count($item->allergens) > 0): ?>
                        <p class="text-xs text-red-600 mb-2">
                            <strong>Allergens:</strong> <?php echo e(implode(', ', $item->allergens)); ?>

                        </p>
                        <?php endif; ?>
                    </div>

                    <div class="ml-4 text-right">
                        <span class="text-xl font-bold text-blue-600"><?php echo e($item->formatted_price); ?></span>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form class="add-to-cart-form" data-item-id="<?php echo e($item->id); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="menu_item_id" value="<?php echo e($item->id); ?>">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Qty:</label>
                            <select name="quantity" class="border border-gray-300 rounded px-2 py-1">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-semibold transition duration-200">
                            Add to Cart
                        </button>
                    </div>
                    
                    <textarea name="special_instructions" placeholder="Special instructions (optional)" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm mt-2" rows="1"></textarea>
                </form>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php else: ?>
        <p class="text-gray-500 italic">No items available in this category.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- Add to Cart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // Handle add to cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            button.textContent = 'Adding...';
            button.disabled = true;
            
            fetch('<?php echo e(route("guest.food-orders.cart.add")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    // Show success message
                    button.textContent = 'Added!';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-green-800');
                    
                    // Update cart count
                    document.getElementById('cart-count').textContent = data.cart_count;
                    
                    // Reset form
                    this.reset();
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-800');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.disabled = false;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add item to cart. Please try again.');
                button.textContent = originalText;
                button.disabled = false;
            });
        });
    });
});

function updateCartCount() {
    fetch('<?php echo e(route("guest.food-orders.cart.count")); ?>')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        })
        .catch(error => console.error('Error updating cart count:', error));
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/food-orders/menu.blade.php ENDPATH**/ ?>