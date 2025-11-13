<?php $__env->startSection('title', 'Food Menu - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #category-grid-view, #category-items-view {
        animation: fadeInUp 0.4s ease-out;
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    /* Custom scrollbar for better UX */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
    
    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
    <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
        <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Food Menu</h1>
                    <p class="text-gray-400">Delicious meals delivered to your room or pickup at our restaurant</p>
        </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <!-- My Orders Button -->
                    <a href="<?php echo e(route('guest.food-orders.orders')); ?>" 
                       class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        My Orders
                    </a>
        
        <!-- Cart Button -->
        <div class="relative">
            <a href="<?php echo e(route('guest.food-orders.cart')); ?>" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                            Cart
                            <span id="cart-count" class="ml-2 bg-red-500 text-white rounded-full px-2.5 py-0.5 text-xs font-bold min-w-[24px] text-center">0</span>
            </a>
                    </div>
                </div>
        </div>
    </div>

        <!-- Main Content Container -->
        <div id="main-content">
            <!-- Category Grid View (Default) -->
            <div id="category-grid-view">
                <!-- Featured Items Section -->
    <?php if($featuredItems->count() > 0): ?>
    <div class="mb-12">
                    <div class="mb-6 text-center">
                        <div class="inline-flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <h2 class="text-3xl font-bold text-white">Featured Items</h2>
                        </div>
                        <p class="text-gray-400">Try our chef's special recommendations</p>
                    </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $featuredItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gradient-to-br from-yellow-900/30 to-orange-900/30 backdrop-blur-sm border-2 border-yellow-500/50 rounded-xl shadow-2xl overflow-hidden hover:shadow-yellow-500/20 hover:border-yellow-400 transition-all duration-300 transform hover:scale-105 relative">
                            <!-- Featured Badge -->
                            <div class="absolute top-2 right-2 z-10">
                                <div class="bg-yellow-500 text-gray-900 px-2 py-0.5 rounded-full text-xs font-bold flex items-center shadow-lg">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    FEATURED
                                </div>
                            </div>

                <?php if($item->image): ?>
                            <div class="relative h-40 overflow-hidden">
                                <img src="<?php echo e(asset('storage/' . $item->image)); ?>" alt="<?php echo e($item->name); ?>" class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
                            </div>
                <?php else: ?>
                            <div class="h-40 bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                                <svg class="w-16 h-16 text-gray-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <?php endif; ?>
                
                            <div class="p-4 bg-gray-800/90 backdrop-blur-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-bold text-lg text-white"><?php echo e($item->name); ?></h3>
                                    <span class="text-xl font-bold text-yellow-400 ml-2">₱<?php echo e(number_format($item->price, 2)); ?></span>
                    </div>

                    <?php if($item->description): ?>
                                <p class="text-gray-300 text-xs mb-2 line-clamp-2"><?php echo e($item->description); ?></p>
                    <?php endif; ?>
                    
                    <!-- Dietary Badges -->
                                <div class="flex flex-wrap gap-1.5 mb-3">
                        <?php $__currentLoopData = $item->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="px-2 py-0.5 text-xs rounded-full <?php echo e($badge['class']); ?>">
                            <?php echo e($badge['label']); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($item->calories): ?>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-700 text-gray-300">
                                        <?php echo e($item->calories); ?> cal
                                    </span>
                                    <?php endif; ?>
                                    <?php if($item->preparation_time): ?>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-600 text-blue-100">
                                        <?php echo e($item->preparation_time); ?> min
                                    </span>
                                    <?php endif; ?>
                    </div>
                    
                    <!-- Add to Cart Form -->
                    <form class="add-to-cart-form" data-item-id="<?php echo e($item->id); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="menu_item_id" value="<?php echo e($item->id); ?>">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-medium text-gray-300">Qty:</label>
                                        <select name="quantity" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-yellow-500 flex-shrink-0">
                                <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                <?php endfor; ?>
                            </select>
                                        <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-gray-900 py-1.5 px-3 rounded-lg text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-105">
                            Add to Cart
                        </button>
                                    </div>
                                    <input type="text" name="special_instructions" placeholder="Special instructions (optional)" 
                                        class="w-full mt-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-500 rounded-lg px-2 py-1.5 text-xs focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

                    <!-- Divider -->
                    <div class="my-12 flex items-center">
                        <div class="flex-1 border-t border-gray-700"></div>
                        <span class="px-4 text-gray-500 font-semibold">OR BROWSE BY CATEGORY</span>
                        <div class="flex-1 border-t border-gray-700"></div>
                    </div>
    </div>
    <?php endif; ?>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-white mb-2">Browse Menu by Category</h2>
                    <p class="text-gray-400">Click on a category to view available items</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                        $categoryData = [
                            ['icon' => '🍳', 'gradient' => 'from-orange-500 to-amber-600'],
                            ['icon' => '🍽️', 'gradient' => 'from-blue-500 to-cyan-600'],
                            ['icon' => '🍗', 'gradient' => 'from-purple-500 to-pink-600'],
                            ['icon' => '🍰', 'gradient' => 'from-pink-500 to-rose-600'],
                            ['icon' => '🍿', 'gradient' => 'from-yellow-500 to-orange-500'],
                            ['icon' => '🥤', 'gradient' => 'from-indigo-500 to-blue-600'],
                        ];
                    ?>

                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $data = $categoryData[$index % count($categoryData)];
                    ?>
                    <div onclick="showCategory(<?php echo e($category->id); ?>, '<?php echo e($category->name); ?>')" 
                         class="group cursor-pointer bg-gradient-to-br <?php echo e($data['gradient']); ?> rounded-2xl p-8 shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute inset-0" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>
                        </div>
                        
                        <div class="relative z-10">
                            <!-- Icon -->
                            <div class="text-6xl mb-4 transform group-hover:scale-110 transition-transform duration-300">
                                <?php echo e($data['icon']); ?>

                            </div>
                            
                            <!-- Category Name -->
                            <h3 class="text-2xl font-bold text-white mb-2"><?php echo e($category->name); ?></h3>
                            
                            <!-- Description -->
                            <?php if($category->description): ?>
                            <p class="text-white/90 text-sm mb-4"><?php echo e($category->description); ?></p>
                            <?php endif; ?>
                            
                            <!-- Stats -->
                            <div class="flex items-center justify-between text-white/80 text-sm">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <?php echo e($category->menuItems->count()); ?> Items
                                </span>
                                <span class="flex items-center font-semibold">
                                    View Menu
                                    <svg class="w-5 h-5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <!-- Category Items View (Hidden by default) -->
            <div id="category-items-view" class="hidden">
                <!-- Back Button -->
                <button onclick="showAllCategories()" 
                        class="mb-6 inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Categories
                </button>

                <!-- Category Header -->
                <div id="category-header" class="mb-8">
                    <h2 class="text-4xl font-bold text-white mb-2">Category Name</h2>
                    <p class="text-gray-400">Browse items in this category</p>
                </div>

                <!-- Menu Items Container -->
                <div id="menu-items-container" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Menu items will be dynamically loaded here -->
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden bg-gray-800 rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-gray-400 text-lg">No items available in this category.</p>
                </div>
            </div>
        </div>

        <!-- Hidden Menu Items Data -->
        <div id="menu-data" class="hidden">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div data-category-id="<?php echo e($category->id); ?>" data-category-name="<?php echo e($category->name); ?>">
            <?php $__currentLoopData = $category->menuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="menu-item-data" data-item-id="<?php echo e($item->id); ?>">
                    <input type="hidden" name="item_name" value="<?php echo e($item->name); ?>">
                    <input type="hidden" name="item_description" value="<?php echo e($item->description ?? ''); ?>">
                    <input type="hidden" name="item_price" value="<?php echo e($item->price); ?>">
                    <input type="hidden" name="item_calories" value="<?php echo e($item->calories ?? ''); ?>">
                    <input type="hidden" name="item_prep_time" value="<?php echo e($item->preparation_time ?? ''); ?>">
                    <input type="hidden" name="item_ingredients" value="<?php echo e(is_array($item->ingredients) ? implode(', ', $item->ingredients) : ''); ?>">
                    <input type="hidden" name="item_allergens" value="<?php echo e(is_array($item->allergens) ? implode(', ', $item->allergens) : ''); ?>">
                    <input type="hidden" name="item_badges" value="<?php echo e(json_encode($item->dietary_badges ?? [])); ?>">
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<!-- Add to Cart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // Attach add to cart listeners
    attachAddToCartListeners();
});

function updateCartCount() {
    fetch('<?php echo e(route("guest.food-orders.cart.count")); ?>')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        })
        .catch(error => console.error('Error updating cart count:', error));
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

// Show category items
function showCategory(categoryId, categoryName) {
    // Hide category grid
    document.getElementById('category-grid-view').classList.add('hidden');
    
    // Show category items view
    document.getElementById('category-items-view').classList.remove('hidden');
    
    // Update header
    document.getElementById('category-header').innerHTML = `
        <h2 class="text-4xl font-bold text-white mb-2">${categoryName}</h2>
        <p class="text-gray-400">Browse items in this category</p>
    `;
    
    // Get menu items for this category
    const categoryData = document.querySelector(`#menu-data [data-category-id="${categoryId}"]`);
    const menuItems = categoryData ? categoryData.querySelectorAll('.menu-item-data') : [];
    
    const container = document.getElementById('menu-items-container');
    const emptyState = document.getElementById('empty-state');
    
    // Clear container
    container.innerHTML = '';
    
    if (menuItems.length === 0) {
        container.classList.add('hidden');
        emptyState.classList.remove('hidden');
    } else {
        container.classList.remove('hidden');
        emptyState.classList.add('hidden');
        
        // Create menu item cards
        menuItems.forEach(itemData => {
            const itemId = itemData.getAttribute('data-item-id');
            const name = itemData.querySelector('[name="item_name"]').value;
            const description = itemData.querySelector('[name="item_description"]').value;
            const price = parseFloat(itemData.querySelector('[name="item_price"]').value);
            const calories = itemData.querySelector('[name="item_calories"]').value;
            const prepTime = itemData.querySelector('[name="item_prep_time"]').value;
            const ingredients = itemData.querySelector('[name="item_ingredients"]').value;
            const allergens = itemData.querySelector('[name="item_allergens"]').value;
            const badgesJson = itemData.querySelector('[name="item_badges"]').value;
            let badges = [];
            try {
                badges = JSON.parse(badgesJson);
            } catch(e) {}
            
            // Build badges HTML
            let badgesHTML = '';
            if (badges && Array.isArray(badges)) {
                badges.forEach(badge => {
                    badgesHTML += `<span class="px-2 py-1 text-xs rounded-full ${badge.class}">${badge.label}</span>`;
                });
            }
            
            if (calories) {
                badgesHTML += `<span class="px-2 py-1 text-xs rounded-full bg-gray-700 text-gray-300">${calories} cal</span>`;
            }
            
            if (prepTime) {
                badgesHTML += `<span class="px-2 py-1 text-xs rounded-full bg-blue-600 text-blue-100">${prepTime} min</span>`;
            }
            
            const card = document.createElement('div');
            card.className = 'bg-gray-800 rounded-lg shadow-xl p-6 hover:shadow-2xl transition-all duration-300';
            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-xl text-white mb-2">${name}</h3>
                        ${description ? `<p class="text-gray-400 text-sm mb-3">${description}</p>` : ''}
                        
                        <!-- Dietary and Info Badges -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            ${badgesHTML}
                        </div>
                        
                        ${ingredients ? `<p class="text-xs text-gray-500 mb-2"><strong class="text-gray-400">Ingredients:</strong> ${ingredients}</p>` : ''}
                        ${allergens ? `<p class="text-xs text-red-400 mb-2"><strong>Allergens:</strong> ${allergens}</p>` : ''}
                    </div>

                    <div class="ml-4 text-right">
                        <span class="text-2xl font-bold text-green-400">₱${price.toFixed(2)}</span>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form class="add-to-cart-form" data-item-id="${itemId}">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="menu_item_id" value="${itemId}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-300">Qty:</label>
                            <select name="quantity" class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                ${[...Array(10)].map((_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
                            </select>
                        </div>
                        
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-lg font-semibold shadow-lg transition-all duration-200">
                            Add to Cart
                        </button>
                    </div>
                    
                    <textarea name="special_instructions" placeholder="Special instructions (optional)" 
                            class="w-full bg-gray-700 border border-gray-600 text-white placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent" rows="1"></textarea>
                </form>
            `;
            
            container.appendChild(card);
        });
        
        // Re-attach add to cart event listeners
        attachAddToCartListeners();
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Show all categories
function showAllCategories() {
    document.getElementById('category-items-view').classList.add('hidden');
    document.getElementById('category-grid-view').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Attach add to cart event listeners
function attachAddToCartListeners() {
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.removeEventListener('submit', handleAddToCart);
        form.addEventListener('submit', handleAddToCart);
    });
}

// Handle add to cart
function handleAddToCart(e) {
            e.preventDefault();
            
    const form = e.target;
    const formData = new FormData(form);
    const button = form.querySelector('button[type="submit"]');
            const originalText = button.textContent;
    const isFeaturedItem = button.classList.contains('from-yellow-500');
            
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
            showNotification(data.error, 'error');
            button.textContent = originalText;
            button.disabled = false;
                } else {
                    // Show success message
            button.textContent = '✓ Added!';
            
            if (isFeaturedItem) {
                button.classList.remove('from-yellow-500', 'to-orange-500', 'hover:from-yellow-600', 'hover:to-orange-600');
                button.classList.add('bg-green-600');
            } else {
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-green-800');
            }
                    
                    // Update cart count
                    document.getElementById('cart-count').textContent = data.cart_count;
            
            // Show success notification
            showNotification('Item added to cart!', 'success');
                    
                    // Reset form
            form.reset();
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.textContent = originalText;
                
                if (isFeaturedItem) {
                    button.classList.remove('bg-green-600');
                    button.classList.add('from-yellow-500', 'to-orange-500', 'hover:from-yellow-600', 'hover:to-orange-600');
                } else {
                        button.classList.remove('bg-green-800');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                }
                
                        button.disabled = false;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
        showNotification('Failed to add item to cart. Please try again.', 'error');
                button.textContent = originalText;
                button.disabled = false;
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\food-orders\menu.blade.php ENDPATH**/ ?>