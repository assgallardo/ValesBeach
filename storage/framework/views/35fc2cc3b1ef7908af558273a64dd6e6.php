<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Filter Section -->
    <div class="relative bg-gradient-to-br from-emerald-900 via-teal-800 to-cyan-900 rounded-xl shadow-lg p-4 mb-4 overflow-hidden">
        <!-- Decorative background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-0 w-32 h-32 bg-cyan-300 rounded-full mix-blend-overlay filter blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        
        <div class="relative flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <div class="p-1 bg-white/20 backdrop-blur-sm rounded">
                    <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white drop-shadow-lg">Filter by Category</h3>
            </div>
            <?php if(request('category')): ?>
                <a href="<?php echo e(route('guest.rooms.browse')); ?>" 
                   class="px-2.5 py-1 bg-white/10 backdrop-blur-sm hover:bg-white/20 rounded text-xs text-white font-medium transition-all duration-300 flex items-center space-x-1 border border-white/20">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Clear</span>
                </a>
            <?php endif; ?>
        </div>
        
        <form action="<?php echo e(route('guest.rooms.browse')); ?>" method="GET">
            <div class="relative grid grid-cols-1 md:grid-cols-4 gap-2">
                <!-- All Categories Button -->
                <button type="submit" 
                        name="category" 
                        value=""
                        class="group relative overflow-hidden rounded-lg p-3 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 <?php echo e(!request('category') ? 'bg-gradient-to-br from-white to-gray-50 shadow-lg' : 'bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20'); ?>">
                    <div class="flex flex-col items-center space-y-1.5">
                        <div class="p-2 rounded-lg <?php echo e(!request('category') ? 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-md' : 'bg-white/20 group-hover:bg-white/30'); ?> transition-all duration-300">
                            <svg class="w-5 h-5 <?php echo e(!request('category') ? 'text-white' : 'text-cyan-100'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h4 class="font-bold <?php echo e(!request('category') ? 'text-gray-900' : 'text-white'); ?> text-sm">All Categories</h4>
                            <p class="text-xs <?php echo e(!request('category') ? 'text-gray-600' : 'text-cyan-200'); ?> font-medium">View All</p>
                        </div>
                    </div>
                    <?php if(!request('category')): ?>
                        <div class="absolute top-1.5 right-1.5 bg-emerald-500 rounded-full p-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </button>

                <!-- Rooms Category -->
                <button type="submit" 
                        name="category" 
                        value="Rooms"
                        class="group relative overflow-hidden rounded-lg p-3 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 <?php echo e(request('category') == 'Rooms' ? 'bg-gradient-to-br from-white to-gray-50 shadow-lg' : 'bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20'); ?>">
                    <div class="flex flex-col items-center space-y-1.5">
                        <div class="p-2 rounded-lg <?php echo e(request('category') == 'Rooms' ? 'bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md' : 'bg-white/20 group-hover:bg-white/30'); ?> transition-all duration-300">
                            <svg class="w-5 h-5 <?php echo e(request('category') == 'Rooms' ? 'text-white' : 'text-cyan-100'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h4 class="font-bold <?php echo e(request('category') == 'Rooms' ? 'text-gray-900' : 'text-white'); ?> text-sm">Rooms</h4>
                            <p class="text-xs <?php echo e(request('category') == 'Rooms' ? 'text-gray-600' : 'text-cyan-200'); ?> font-medium">Comfort</p>
                        </div>
                    </div>
                    <?php if(request('category') == 'Rooms'): ?>
                        <div class="absolute top-1.5 right-1.5 bg-blue-500 rounded-full p-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </button>

                <!-- Cottages Category -->
                <button type="submit" 
                        name="category" 
                        value="Cottages"
                        class="group relative overflow-hidden rounded-lg p-3 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 <?php echo e(request('category') == 'Cottages' ? 'bg-gradient-to-br from-white to-gray-50 shadow-lg' : 'bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20'); ?>">
                    <div class="flex flex-col items-center space-y-1.5">
                        <div class="p-2 rounded-lg <?php echo e(request('category') == 'Cottages' ? 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-md' : 'bg-white/20 group-hover:bg-white/30'); ?> transition-all duration-300">
                            <svg class="w-5 h-5 <?php echo e(request('category') == 'Cottages' ? 'text-white' : 'text-cyan-100'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h4 class="font-bold <?php echo e(request('category') == 'Cottages' ? 'text-gray-900' : 'text-white'); ?> text-sm">Cottages</h4>
                            <p class="text-xs <?php echo e(request('category') == 'Cottages' ? 'text-gray-600' : 'text-cyan-200'); ?> font-medium">Beach</p>
                        </div>
                    </div>
                    <?php if(request('category') == 'Cottages'): ?>
                        <div class="absolute top-1.5 right-1.5 bg-amber-500 rounded-full p-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </button>

                <!-- Event and Dining Category -->
                <button type="submit" 
                        name="category" 
                        value="Event and Dining"
                        class="group relative overflow-hidden rounded-lg p-3 transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 <?php echo e(request('category') == 'Event and Dining' ? 'bg-gradient-to-br from-white to-gray-50 shadow-lg' : 'bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20'); ?>">
                    <div class="flex flex-col items-center space-y-1.5">
                        <div class="p-2 rounded-lg <?php echo e(request('category') == 'Event and Dining' ? 'bg-gradient-to-br from-purple-500 to-pink-600 shadow-md' : 'bg-white/20 group-hover:bg-white/30'); ?> transition-all duration-300">
                            <svg class="w-5 h-5 <?php echo e(request('category') == 'Event and Dining' ? 'text-white' : 'text-cyan-100'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h4 class="font-bold <?php echo e(request('category') == 'Event and Dining' ? 'text-gray-900' : 'text-white'); ?> text-sm">Event & Dining</h4>
                            <p class="text-xs <?php echo e(request('category') == 'Event and Dining' ? 'text-gray-600' : 'text-cyan-200'); ?> font-medium">Celebrate</p>
                        </div>
                    </div>
                    <?php if(request('category') == 'Event and Dining'): ?>
                        <div class="absolute top-1.5 right-1.5 bg-purple-500 rounded-full p-0.5">
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Facilities Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden group hover:bg-green-800/50 transition-colors">
                <a href="<?php echo e(route('guest.rooms.show', $room)); ?>" class="block">
                    <!-- Facility Image -->
                    <?php if(isset($room->images) && $room->images && $room->images->isNotEmpty()): ?>
                        <div class="relative h-64 overflow-hidden">
                            <img src="<?php echo e(asset('storage/' . $room->images->first()->image_path)); ?>"
                                 alt="<?php echo e($room->name); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    <?php endif; ?>

                    <!-- Facility Details -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo e($room->name); ?></h3>
                        <p class="text-gray-300 mb-4"><?php echo e(Str::limit($room->description, 100)); ?></p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-gray-300">
                                <span class="block text-sm">Category</span>
                                <span class="font-semibold text-white"><?php echo e($room->category ?? 'Rooms'); ?></span>
                            </div>
                            <div class="text-gray-300">
                                <span class="block text-sm">Type</span>
                                <span class="font-semibold text-white"><?php echo e($room->type); ?></span>
                            </div>
                            <div class="text-gray-300">
                                <span class="block text-sm">Capacity</span>
                                <span class="font-semibold text-white"><?php echo e($room->capacity); ?> persons</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-gray-300 text-sm">Price per night</span>
                                <p class="text-2xl font-bold text-white">₱<?php echo e(number_format($room->price, 2)); ?></p>
                            </div>
                            <span class="inline-flex items-center text-green-400 group-hover:translate-x-2 transition-transform">
                                View Details
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-3 text-center text-gray-400 py-8">
                No facilities available matching your criteria.
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        <?php echo e($rooms->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/guest/rooms/browse.blade.php ENDPATH**/ ?>