<?php $__env->startSection('content'); ?>
<div class="px-6 py-8 min-h-screen bg-gray-900">
<div class="max-w-7xl mx-auto">
    <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
        <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Menu Management</h1>
                    <p class="text-gray-400">Manage food menu items for guest ordering</p>
        </div>
        <a href="<?php echo e(route('staff.menu.create')); ?>" 
           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Menu Item
        </a>
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
    <div class="bg-gray-800 rounded-lg shadow-xl p-6 mb-6 border border-gray-700">
        <form method="GET" action="<?php echo e(route('staff.menu.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>" 
                       placeholder="Search by name..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                <select id="category" 
                        name="category"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="availability" class="block text-sm font-medium text-gray-300 mb-2">Availability</label>
                <select id="availability" 
                        name="availability"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">All Items</option>
                    <option value="1" <?php echo e(request('availability') === '1' ? 'selected' : ''); ?>>Available</option>
                    <option value="0" <?php echo e(request('availability') === '0' ? 'selected' : ''); ?>>Unavailable</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <?php if($menuItems->isEmpty()): ?>
        <!-- Empty State -->
        <div class="bg-gray-800 rounded-lg shadow-xl p-12 text-center border border-gray-700">
            <svg class="w-16 h-16 text-gray-600 mb-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-400 text-lg">No menu items found.</p>
            <p class="text-gray-500 text-sm mt-2">Try adjusting your filters or create a new menu item.</p>
        </div>
                                <?php else: ?>
        <?php
            // Group menu items by category
            $groupedItems = $menuItems->groupBy(function($item) {
                return $item->menuCategory->id ?? 0;
            });
            
            // Define category colors
            $categoryColors = [
                'Breakfast' => ['gradient' => 'from-orange-500 to-orange-600', 'icon' => 'fa-coffee', 'bg' => 'bg-orange-600'],
                'Lunch' => ['gradient' => 'from-green-500 to-green-600', 'icon' => 'fa-hamburger', 'bg' => 'bg-green-600'],
                'Dinner' => ['gradient' => 'from-purple-500 to-purple-600', 'icon' => 'fa-drumstick-bite', 'bg' => 'bg-purple-600'],
                'Dessert' => ['gradient' => 'from-pink-500 to-pink-600', 'icon' => 'fa-ice-cream', 'bg' => 'bg-pink-600'],
                'Snacks' => ['gradient' => 'from-yellow-500 to-yellow-600', 'icon' => 'fa-cookie', 'bg' => 'bg-yellow-600'],
                'Drinks' => ['gradient' => 'from-blue-500 to-blue-600', 'icon' => 'fa-glass-martini-alt', 'bg' => 'bg-blue-600'],
            ];
        ?>

        <!-- Category Cards View -->
        <div id="categoryView">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $groupedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $category = $items->first()->menuCategory ?? null;
                        $categoryName = $category ? $category->name : 'Uncategorized';
                        $colors = $categoryColors[$categoryName] ?? ['gradient' => 'from-gray-500 to-gray-600', 'icon' => 'fa-utensils', 'bg' => 'bg-gray-600'];
                    ?>

                    <!-- Category Card -->
                    <div class="category-card bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-700 hover:border-green-500 transition-all duration-300 transform hover:scale-105 cursor-pointer"
                         onclick="showCategory('<?php echo e($categoryId); ?>', '<?php echo e($categoryName); ?>', '<?php echo e($colors['gradient']); ?>', '<?php echo e($colors['icon']); ?>')">
                        <div class="bg-gradient-to-br <?php echo e($colors['gradient']); ?> p-8 text-center relative overflow-hidden">
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                            
                            <div class="relative z-10">
                                <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                    <i class="fas <?php echo e($colors['icon']); ?> text-white text-3xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-2"><?php echo e($categoryName); ?></h3>
                                <p class="text-white text-sm opacity-90"><?php echo e($items->count()); ?> <?php echo e(Str::plural('item', $items->count())); ?></p>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-gray-750">
                            <div class="flex items-center justify-center text-gray-300 text-sm">
                                <span>Click to view items</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Menu Items View (Hidden by default) -->
        <div id="menuView" class="hidden">
            <!-- Back Button & Category Header -->
            <div class="mb-6">
                <button onclick="showCategories()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Categories
                </button>

                <div id="categoryHeader" class="bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-700">
                    <div id="categoryHeaderGradient" class="p-4 text-center">
                        <div class="flex items-center justify-center">
                            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                                <i id="categoryIcon" class="text-white text-lg"></i>
                            </div>
                            <h2 id="categoryTitle" class="text-xl font-bold text-white"></h2>
                            <span id="categoryCount" class="ml-3 text-white opacity-75 text-sm"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Items Container -->
            <div class="bg-gray-800 rounded-lg shadow-xl p-3 border border-gray-700">
                <div id="menuItemsContainer" class="grid grid-cols-1 gap-1.5">
                    <!-- Items will be loaded here -->
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if($menuItems->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($menuItems->links()); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
    </div>
</div>

<?php
    // Pass all grouped items to JavaScript
    $allItems = [];
    foreach($groupedItems as $categoryId => $items) {
        $allItems[$categoryId] = $items->toArray();
    }
?>

<script>
const allMenuItems = <?php echo json_encode($allItems, 15, 512) ?>;

function showCategory(categoryId, categoryName, gradient, icon) {
    const categoryView = document.getElementById('categoryView');
    const menuView = document.getElementById('menuView');
    const categoryTitle = document.getElementById('categoryTitle');
    const categoryIcon = document.getElementById('categoryIcon');
    const categoryCount = document.getElementById('categoryCount');
    const categoryHeaderGradient = document.getElementById('categoryHeaderGradient');
    const menuItemsContainer = document.getElementById('menuItemsContainer');
    
    // Hide category cards, show menu items
    categoryView.classList.add('hidden');
    menuView.classList.remove('hidden');
    
    // Update header
    categoryTitle.textContent = categoryName;
    categoryIcon.className = `fas ${icon} text-white text-lg`;
    categoryHeaderGradient.className = `bg-gradient-to-br ${gradient} p-4 text-center`;
    
    const items = allMenuItems[categoryId];
    categoryCount.textContent = `(${items.length} ${items.length === 1 ? 'item' : 'items'})`;
    
    // Build items HTML
    let itemsHTML = '';
    
    items.forEach(item => {
        itemsHTML += `
            <div class="bg-gray-750 rounded p-2 border border-gray-700 hover:border-green-500 transition-all duration-200">
                <div class="flex items-center gap-2">
                    <!-- Image -->
                    <div class="flex-shrink-0">
                        ${item.image ? 
                            `<img src="/storage/${item.image}" alt="${item.name}" class="w-12 h-12 object-cover rounded shadow-md">` :
                            `<div class="w-12 h-12 bg-gray-600 rounded flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                            </div>`
                        }
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 mr-2">
                                <h3 class="text-sm font-bold text-white line-clamp-1">${item.name}</h3>
                                <p class="text-gray-400 text-[10px] line-clamp-1">${item.description || ''}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="text-base font-bold text-green-400">₱${parseFloat(item.price).toFixed(2)}</span>
                            </div>
                                    </div>

                        <!-- Badges & Actions -->
                        <div class="flex items-center justify-between ml-2">
                            <div class="flex items-center gap-2">
                                <!-- Availability Toggle -->
                                <form action="/staff/menu/${item.id}/toggle-availability" method="POST" class="inline-block">
                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                    <button type="submit" 
                                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold transition-colors duration-200 ${item.is_available ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-gray-300'}">
                                        ${item.is_available ? 'Available' : 'Unavailable'}
                                    </button>
                                </form>

                                <!-- Featured Toggle -->
                                <form action="/staff/menu/${item.id}/toggle-featured" method="POST" class="inline-block">
                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                    <button type="submit" 
                                            class="p-0.5 rounded transition-colors duration-200 ${item.is_featured ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-gray-600 hover:bg-gray-700 text-gray-400'}">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-1">
                                <a href="/staff/menu/${item.id}/edit" 
                                   class="inline-flex items-center px-1.5 py-0.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-semibold rounded transition-colors duration-200">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    Edit
                                </a>
                                <form action="/staff/menu/${item.id}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this menu item?');" class="inline-block">
                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                    <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" 
                                            class="inline-flex items-center px-1.5 py-0.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-semibold rounded transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        Delete
                                        </button>
                                    </form>
                                </div>
                        </div>
                                </div>
        </div>
            </div>
        `;
    });
    
    menuItemsContainer.innerHTML = itemsHTML;
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showCategories() {
    const categoryView = document.getElementById('categoryView');
    const menuView = document.getElementById('menuView');
    
    // Show category cards, hide menu items
    categoryView.classList.remove('hidden');
    menuView.classList.add('hidden');
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<style>
.bg-gray-750 {
    background-color: #2d3748;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/staff/menu/index.blade.php ENDPATH**/ ?>