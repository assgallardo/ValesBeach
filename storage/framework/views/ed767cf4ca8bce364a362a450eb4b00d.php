<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            Available Rooms
        </h2>
        <p class="text-xl text-gray-200">
            Choose your perfect stay at Vales Beach Resort
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
            <?php if($room->image_url): ?>
                <img src="<?php echo e($room->image_url); ?>" alt="<?php echo e($room->name); ?>" class="w-full h-48 object-cover">
            <?php else: ?>
                <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                    <span class="text-gray-500">No image available</span>
                </div>
            <?php endif; ?>

            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-2"><?php echo e($room->name); ?></h3>
                <p class="text-gray-300 mb-4"><?php echo e($room->description); ?></p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-300">
                        <span>Price per night:</span>
                        <span class="font-bold"><?php echo e($room->formatted_price); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-300">
                        <span>Max guests:</span>
                        <span><?php echo e($room->max_guests); ?> persons</span>
                    </div>
                </div>

                <a href="<?php echo e(route('guest.rooms.book', $room)); ?>" 
                   class="block w-full text-center bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Book Now
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/guest/rooms/index.blade.php ENDPATH**/ ?>