

<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Rooms & Facilities
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Update room availability, rates, and facility details.
                </p>
                <div class="mt-6">
                    <a href="<?php echo e(route('manager.dashboard')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 mr-4">
                        Back to Dashboard
                    </a>
                    <a href="#" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Add New Room
                    </a>
                </div>
            </div>

            <!-- Rooms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden hover:bg-green-900/70 transition-all duration-300">
                    <!-- Room Image -->
                    <div class="h-48 bg-gray-600 flex items-center justify-center">
                        <?php if($room->images && $room->images->count() > 0): ?>
                        <img src="<?php echo e(asset('storage/' . $room->images->first()->image_path)); ?>" 
                             alt="<?php echo e($room->name); ?>" 
                             class="w-full h-full object-cover">
                        <?php else: ?>
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        <?php endif; ?>
                    </div>

                    <!-- Room Details -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-green-50"><?php echo e($room->name); ?></h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                <?php echo e($room->status === 'available' ? 'bg-green-600 text-green-100' : ''); ?>

                                <?php echo e($room->status === 'occupied' ? 'bg-red-600 text-red-100' : ''); ?>

                                <?php echo e($room->status === 'maintenance' ? 'bg-yellow-600 text-yellow-100' : ''); ?>">
                                <?php echo e(ucfirst($room->status ?? 'available')); ?>

                            </span>
                        </div>
                        
                        <div class="text-green-300 space-y-2 mb-4">
                            <p><strong>Type:</strong> <?php echo e($room->type ?? 'Standard'); ?></p>
                            <p><strong>Capacity:</strong> <?php echo e($room->capacity ?? 2); ?> guests</p>
                            <p><strong>Price:</strong> ₱<?php echo e(number_format($room->price ?? 0, 2)); ?>/night</p>
                        </div>

                        <div class="flex gap-2">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                View Details
                            </button>
                            <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                Edit
                            </button>
                            <?php if($room->status === 'available'): ?>
                            <a href="<?php echo e(route('manager.bookings.quick-book', $room->id)); ?>" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                Book Room
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-16">
                    <div class="text-green-300">
                        <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        <h3 class="text-xl font-medium text-green-200 mb-2">No rooms found</h3>
                        <p class="text-green-400 mb-6">Start by adding your first room.</p>
                        <a href="#" 
                           class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add First Room
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($rooms->hasPages()): ?>
            <div class="flex justify-center">
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-4">
                    <?php echo e($rooms->links()); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/rooms.blade.php ENDPATH**/ ?>