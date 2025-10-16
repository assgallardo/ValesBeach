<?php $__env->startSection('content'); ?>
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Resort Services
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Discover our exclusive spa, dining, transportation, and activity services
            </p>
            <div class="mt-6">
                <a href="<?php echo e(route('guest.dashboard')); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Service Categories Filter -->
        <div class="flex flex-wrap justify-center gap-4 mb-8">
            <a href="<?php echo e(route('guest.services.index')); ?>" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                All Services
            </a>
            <a href="<?php echo e(route('guest.services.index', ['category' => 'spa'])); ?>" 
               class="px-4 py-2 bg-green-900/50 text-green-200 rounded-lg hover:bg-green-600 hover:text-white transition-colors duration-200">
                Spa & Wellness
            </a>
            <a href="<?php echo e(route('guest.services.index', ['category' => 'dining'])); ?>" 
               class="px-4 py-2 bg-green-900/50 text-green-200 rounded-lg hover:bg-green-600 hover:text-white transition-colors duration-200">
                Dining
            </a>
            <a href="<?php echo e(route('guest.services.index', ['category' => 'transportation'])); ?>" 
               class="px-4 py-2 bg-green-900/50 text-green-200 rounded-lg hover:bg-green-600 hover:text-white transition-colors duration-200">
                Transportation
            </a>
            <a href="<?php echo e(route('guest.services.index', ['category' => 'activities'])); ?>" 
               class="px-4 py-2 bg-green-900/50 text-green-200 rounded-lg hover:bg-green-600 hover:text-white transition-colors duration-200">
                Activities
            </a>
        </div>

        <!-- Services Grid -->
        <?php if($services->count() > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden hover:border-green-500/50 transition-all duration-300">
                <!-- Service Image -->
                <div class="h-48 bg-green-800/50 relative">
                    <?php if($service->image): ?>
                    <img src="<?php echo e(asset('storage/' . $service->image)); ?>" 
                         alt="<?php echo e($service->name); ?>" 
                         class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="flex items-center justify-center h-full">
                        <?php if($service->category === 'spa'): ?>
                        <i class="fas fa-spa text-4xl text-green-400"></i>
                        <?php elseif($service->category === 'dining'): ?>
                        <i class="fas fa-utensils text-4xl text-orange-400"></i>
                        <?php elseif($service->category === 'transportation'): ?>
                        <i class="fas fa-car text-4xl text-blue-400"></i>
                        <?php elseif($service->category === 'activities'): ?>
                        <i class="fas fa-swimmer text-4xl text-purple-400"></i>
                        <?php else: ?>
                        <i class="fas fa-concierge-bell text-4xl text-yellow-400"></i>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Category Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-1 bg-green-600/80 text-green-100 rounded-full text-xs font-medium">
                            <?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?>

                        </span>
                    </div>

                    <!-- Price Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 bg-black/60 text-white rounded-full text-sm font-bold">
                            ₱<?php echo e(number_format($service->price, 0)); ?>

                        </span>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="p-6">
                    <h3 class="text-lg font-bold text-green-50 mb-2"><?php echo e($service->name); ?></h3>
                    
                    <p class="text-green-300 text-sm mb-4 line-clamp-3"><?php echo e($service->description); ?></p>
                    
                    <div class="space-y-2 mb-4">
                        <?php if($service->duration): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-green-400">Duration:</span>
                            <span class="text-green-50">
                                <?php if($service->duration >= 60): ?>
                                    <?php echo e(floor($service->duration / 60)); ?>h <?php echo e($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : ''); ?>

                                <?php else: ?>
                                    <?php echo e($service->duration); ?>m
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if($service->capacity): ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-green-400">Capacity:</span>
                            <span class="text-green-50"><?php echo e($service->capacity); ?> <?php echo e($service->capacity === 1 ? 'person' : 'people'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm transition-colors">
                            View Details
                        </a>
                        <?php if($service->is_available): ?>
                        <a href="<?php echo e(route('guest.services.request', $service)); ?>" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm transition-colors">
                            Book Now
                        </a>
                        <?php else: ?>
                        <span class="flex-1 bg-gray-600 text-gray-300 text-center py-2 px-3 rounded text-sm">
                            Unavailable
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            <?php echo e($services->links()); ?>

        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="text-xl font-medium text-green-200 mb-2">No Services Available</h3>
            <p class="text-green-400 mb-6">Check back later for available services.</p>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/guest/services/index.blade.php ENDPATH**/ ?>