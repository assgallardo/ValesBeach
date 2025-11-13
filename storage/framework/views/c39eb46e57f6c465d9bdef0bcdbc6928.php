<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-6">Our Rooms</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if($room->images->isNotEmpty()): ?>
                <img src="<?php echo e(asset('storage/' . $room->images->first()->image_path)); ?>" alt="<?php echo e($room->name); ?>" class="w-full h-48 object-cover">
            <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400">No image available</span>
                </div>
            <?php endif; ?>
            
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2"><?php echo e($room->name); ?></h2>
                <p class="text-gray-600 mb-4"><?php echo e($room->description); ?></p>
                
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user"></i> <?php echo e($room->capacity); ?> guests
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-star text-yellow-400"></i> <?php echo e($room->rating); ?>

                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">₱<?php echo e(number_format((float)$room->price, 2)); ?>/night</span>
                    <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\guest\rooms.blade.php ENDPATH**/ ?>