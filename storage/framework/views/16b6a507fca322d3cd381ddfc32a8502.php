<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-5xl">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2"><?php echo e($service->name); ?></h1>
                    <p class="text-gray-400"><?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?> Service</p>
                </div>
                <div class="flex gap-3">
                    <a href="<?php echo e(route('guest.services.index')); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Services
                    </a>
                    <?php if($service->is_available): ?>
                    <a href="<?php echo e(route('guest.services.request', $service)); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Book This Service
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Service Card -->
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden mb-8">
            <!-- Service Image -->
            <div class="h-96 bg-gray-700 relative">
                <?php if($service->image): ?>
                <img src="<?php echo e(asset('storage/' . $service->image)); ?>" 
                     alt="<?php echo e($service->name); ?>" 
                     class="w-full h-full object-cover">
                <?php else: ?>
                <div class="flex items-center justify-center h-full">
                    <?php if($service->category === 'spa'): ?>
                    <svg class="w-32 h-32 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php elseif($service->category === 'dining'): ?>
                    <svg class="w-32 h-32 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <?php elseif($service->category === 'transportation'): ?>
                    <svg class="w-32 h-32 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    <?php elseif($service->category === 'activities'): ?>
                    <svg class="w-32 h-32 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php else: ?>
                    <svg class="w-32 h-32 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Status Badge -->
                <div class="absolute top-4 left-4">
                    <span class="px-4 py-2 rounded-lg text-sm font-semibold shadow-lg <?php echo e($service->is_available ? 'bg-green-600 text-white' : 'bg-red-600 text-white'); ?>">
                        <?php echo e($service->is_available ? '✓ Available' : '✗ Unavailable'); ?>

                    </span>
                </div>

                <!-- Category Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-4 py-2 bg-gray-900/90 text-gray-200 rounded-lg text-sm font-semibold shadow-lg">
                        <?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?>

                    </span>
                </div>

                <!-- Price Badge -->
                <div class="absolute bottom-4 right-4">
                    <span class="px-6 py-3 bg-green-600 text-white rounded-lg text-2xl font-bold shadow-lg">
                        ₱<?php echo e(number_format($service->price, 2)); ?>

                    </span>
                </div>
            </div>

            <!-- Service Information -->
            <div class="p-8">
                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-white mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        About This Service
                    </h3>
                    <p class="text-gray-300 text-lg leading-relaxed"><?php echo e($service->description); ?></p>
                </div>

                <!-- Service Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-700 rounded-lg p-6">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h4 class="text-gray-400 text-sm font-medium">Price</h4>
                        </div>
                        <p class="text-white text-2xl font-bold">₱<?php echo e(number_format($service->price, 2)); ?></p>
                    </div>

                    <?php if($service->duration): ?>
                    <div class="bg-gray-700 rounded-lg p-6">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h4 class="text-gray-400 text-sm font-medium">Duration</h4>
                        </div>
                        <p class="text-white text-2xl font-bold">
                            <?php if($service->duration >= 60): ?>
                                <?php echo e(floor($service->duration / 60)); ?>h <?php echo e($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : ''); ?>

                            <?php else: ?>
                                <?php echo e($service->duration); ?> min
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if($service->capacity): ?>
                    <div class="bg-gray-700 rounded-lg p-6">
                        <div class="flex items-center mb-3">
                            <svg class="w-6 h-6 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h4 class="text-gray-400 text-sm font-medium">Capacity</h4>
                        </div>
                        <p class="text-white text-2xl font-bold"><?php echo e($service->capacity); ?> <?php echo e($service->capacity === 1 ? 'person' : 'people'); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Booking Information -->
                <?php if($service->is_available): ?>
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-400 mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-blue-200 font-semibold text-lg mb-2">Booking Information</h4>
                            <ul class="text-gray-300 space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Book in advance to secure your preferred time slot
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Cancellations must be made at least 24 hours in advance
                                </li>
                                <?php if($service->duration): ?>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Service duration: <?php echo e($service->duration >= 60 ? floor($service->duration / 60) . 'h ' . ($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '') : $service->duration . ' minutes'); ?>

                                </li>
                                <?php endif; ?>
                                <?php if($service->capacity): ?>
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Maximum capacity: <?php echo e($service->capacity); ?> <?php echo e($service->capacity === 1 ? 'person' : 'people'); ?>

                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center pt-6 border-t border-gray-700">
                    <?php if($service->is_available): ?>
                    <a href="<?php echo e(route('guest.services.request', $service)); ?>" 
                       class="inline-flex items-center px-8 py-4 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Book This Service
                    </a>
                    <?php else: ?>
                    <div class="inline-flex items-center px-8 py-4 bg-gray-600 text-gray-300 font-semibold rounded-lg shadow-lg">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Currently Unavailable
                    </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo e(route('guest.services.index')); ?>" 
                       class="inline-flex items-center px-8 py-4 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-lg transition-all duration-200">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Browse All Services
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        <?php if(isset($relatedServices) && $relatedServices && $relatedServices->count() > 0): ?>
        <div>
            <h3 class="text-2xl font-bold text-white mb-6">More <?php echo e(ucfirst($service->category)); ?> Services</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $relatedServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedService): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden hover:shadow-2xl hover:transform hover:scale-105 transition-all duration-300">
                    <div class="h-40 bg-gray-700 relative">
                        <?php if($relatedService->image): ?>
                        <img src="<?php echo e(asset('storage/' . $relatedService->image)); ?>" 
                             alt="<?php echo e($relatedService->name); ?>" 
                             class="w-full h-full object-cover">
                        <?php else: ?>
                        <div class="flex items-center justify-center h-full">
                            <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm font-bold shadow-lg">
                                ₱<?php echo e(number_format($relatedService->price, 0)); ?>

                            </span>
                        </div>
                    </div>
                    
                    <div class="p-5">
                        <h4 class="text-lg font-bold text-white mb-2"><?php echo e($relatedService->name); ?></h4>
                        <p class="text-gray-400 text-sm mb-4 line-clamp-2"><?php echo e($relatedService->description); ?></p>
                        <a href="<?php echo e(route('guest.services.show', $relatedService)); ?>" 
                           class="block w-full bg-gray-700 hover:bg-gray-600 text-white text-center py-2 px-4 rounded-lg text-sm font-semibold transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/services/show.blade.php ENDPATH**/ ?>