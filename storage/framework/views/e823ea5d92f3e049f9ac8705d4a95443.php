<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Filter Section -->
    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8">
        <form action="<?php echo e(route('guest.rooms.browse')); ?>" method="GET" class="flex gap-4">
            <div class="w-full">
                <label class="block text-white mb-2">Room Type</label>
                <select name="type" 
                        class="w-full bg-green-800 text-white rounded-lg px-4 py-2"
                        onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php echo e(request('type') == $type ? 'selected' : ''); ?>>
                            <?php echo e($type); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden group hover:bg-green-800/50 transition-colors">
                <a href="<?php echo e(route('guest.rooms.show', $room)); ?>" class="block">
                    <!-- Room Image -->
                    <?php if($room->images->isNotEmpty()): ?>
                        <div class="relative h-64 overflow-hidden">
                            <img src="<?php echo e(asset('storage/' . $room->images->first()->image_path)); ?>"
                                 alt="<?php echo e($room->name); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    <?php endif; ?>

                    <!-- Room Details -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo e($room->name); ?></h3>
                        <p class="text-gray-300 mb-4"><?php echo e(Str::limit($room->description, 100)); ?></p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
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
                No rooms available matching your criteria.
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        <?php echo e($rooms->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php
Route::get('/guest/rooms/{room}', [RoomController::class, 'show'])->name('guest.rooms.show');
?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\valesbeach\resources\views/guest/rooms/browse.blade.php ENDPATH**/ ?>