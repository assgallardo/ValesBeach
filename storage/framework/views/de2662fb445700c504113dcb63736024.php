<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Resort Services</h1>
                    <p class="text-gray-400">Discover our exclusive spa, dining, room service, transportation, and activity services</p>
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

        <!-- Service Categories Filter -->
        <div class="bg-gray-800 rounded-lg p-4 mb-8">
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('guest.services.index')); ?>" 
                   class="px-4 py-2 <?php echo e(!request('category') ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    All Services
                </a>
                <a href="<?php echo e(route('guest.services.index', ['category' => 'spa'])); ?>" 
                   class="px-4 py-2 <?php echo e(request('category') === 'spa' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-spa inline-block mr-2"></i>
                    Spa & Wellness
                </a>
                <a href="<?php echo e(route('guest.services.index', ['category' => 'dining'])); ?>" 
                   class="px-4 py-2 <?php echo e(request('category') === 'dining' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-utensils inline-block mr-2"></i>
                    Dining
                </a>
                <a href="<?php echo e(route('guest.services.index', ['category' => 'room_service'])); ?>" 
                   class="px-4 py-2 <?php echo e(request('category') === 'room_service' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-concierge-bell inline-block mr-2"></i>
                    Room Service
                </a>
                <a href="<?php echo e(route('guest.services.index', ['category' => 'transportation'])); ?>" 
                   class="px-4 py-2 <?php echo e(request('category') === 'transportation' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-car inline-block mr-2"></i>
                    Transportation
                </a>
                <a href="<?php echo e(route('guest.services.index', ['category' => 'activities'])); ?>" 
                   class="px-4 py-2 <?php echo e(request('category') === 'activities' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-volleyball-ball inline-block mr-2"></i>
                    Activities
                </a>
            </div>
        </div>

        <!-- Services Grid -->
        <?php if($services->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden hover:shadow-2xl hover:transform hover:scale-105 transition-all duration-300">
                <!-- Service Image -->
                <div class="h-56 bg-gray-700 relative">
                    <?php if($service->image): ?>
                    <img src="<?php echo e(asset('storage/' . $service->image)); ?>" 
                         alt="<?php echo e($service->name); ?>" 
                         class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="flex items-center justify-center h-full">
                        <?php if($service->category === 'spa'): ?>
                        <i class="fas fa-spa text-purple-400 text-7xl"></i>
                        <?php elseif($service->category === 'dining'): ?>
                        <i class="fas fa-utensils text-green-400 text-7xl"></i>
                        <?php elseif($service->category === 'room_service'): ?>
                        <i class="fas fa-concierge-bell text-red-400 text-7xl"></i>
                        <?php elseif($service->category === 'transportation'): ?>
                        <i class="fas fa-car text-blue-400 text-7xl"></i>
                        <?php elseif($service->category === 'activities'): ?>
                        <i class="fas fa-volleyball-ball text-yellow-400 text-7xl"></i>
                        <?php else: ?>
                        <svg class="w-20 h-20 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Status & Category Badges -->
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 bg-gray-900/80 text-gray-200 rounded-lg text-xs font-semibold shadow-lg">
                            <?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?>

                        </span>
                    </div>

                    <!-- Price Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-4 py-2 bg-green-600 text-white rounded-lg text-lg font-bold shadow-lg">
                            ₱<?php echo e(number_format($service->price, 0)); ?>

                        </span>
                    </div>

                    <?php if(!$service->is_available): ?>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-semibold shadow-lg">
                            Unavailable
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Service Details -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-2"><?php echo e($service->name); ?></h3>
                    
                    <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo e($service->description); ?></p>
                    
                    <?php if($service->duration || $service->capacity): ?>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-700">
                        <?php if($service->duration): ?>
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-gray-300">
                                <?php if($service->duration >= 60): ?>
                                    <?php echo e(floor($service->duration / 60)); ?>h <?php echo e($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : ''); ?>

                                <?php else: ?>
                                    <?php echo e($service->duration); ?> min
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if($service->capacity): ?>
                        <div class="flex items-center text-sm">
                            <svg class="w-4 h-4 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-gray-300">Up to <?php echo e($service->capacity); ?> <?php echo e($service->capacity === 1 ? 'person' : 'people'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                           class="flex-1 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <?php if($service->is_available): ?>
                        <a href="<?php echo e(route('guest.services.request', $service)); ?>" 
                           class="flex-1 inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white text-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Book
                        </a>
                        <?php else: ?>
                        <span class="flex-1 flex items-center justify-center bg-gray-600 text-gray-400 text-center py-2.5 px-4 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Unavailable
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <?php if($services->hasPages()): ?>
        <div class="flex justify-center">
            <?php echo e($services->links()); ?>

        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="bg-gray-800 rounded-lg p-12 text-center">
            <svg class="w-20 h-20 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-2xl font-bold text-white mb-2">No Services Available</h3>
            <p class="text-gray-400 mb-6">We don't have any services matching your criteria at the moment.</p>
            <?php if(request('category')): ?>
            <a href="<?php echo e(route('guest.services.index')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                View All Services
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/services/index.blade.php ENDPATH**/ ?>