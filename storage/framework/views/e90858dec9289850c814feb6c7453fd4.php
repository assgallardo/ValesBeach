<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                Book <?php echo e($room->name); ?>

            </h2>
            <p class="text-xl text-gray-200">
                Complete your booking details below
            </p>
        </div>

        <?php if($errors->any()): ?>
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Room Details</h3>
                        <p class="text-gray-300"><?php echo e($room->description); ?></p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-300">
                            <span>Price per night:</span>
                            <span class="font-bold"><?php echo e($room->formatted_price); ?></span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Max guests:</span>
                            <span><?php echo e($room->capacity); ?> persons</span>
                        </div>
                    </div>
                </div>

                <form action="<?php echo e(route('guest.rooms.book.store', $room)); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-in Date -->
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" 
                                   value="<?php echo e(old('check_in')); ?>"
                                   min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>

                        <!-- Check-out Date -->
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" 
                                   value="<?php echo e(old('check_out')); ?>"
                                   min="<?php echo e(date('Y-m-d', strtotime('+2 days'))); ?>"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-300 mb-2">Number of Guests</label>
                            <input type="number" id="guests" name="guests" 
                                   value="<?php echo e(old('guests', 1)); ?>"
                                   min="1" max="<?php echo e($room->capacity); ?>"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label for="special_requests" class="block text-sm font-medium text-gray-300 mb-2">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" rows="4"
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                                  placeholder="Any special requests or requirements?"><?php echo e(old('special_requests')); ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="<?php echo e(route('guest.rooms.browse')); ?>" 
                           class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                    <!-- Total Price Display -->
                    <div class="mt-4 p-4 bg-gray-700 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Total Price:</span>
                            <span id="totalPrice" class="text-2xl font-bold text-white">₱ 0.00</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">* Price will be calculated based on your selected dates</p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const totalPriceDisplay = document.getElementById('totalPrice');
    const pricePerNight = <?php echo e($room->price); ?>;

    function updateTotalPrice() {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);

        if (checkIn && checkOut && checkOut > checkIn) {
            const nights = Math.floor((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            const total = nights * pricePerNight;
            totalPriceDisplay.textContent = '₱ ' + total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        } else {
            totalPriceDisplay.textContent = '₱ 0.00';
        }
    }

    checkInInput.addEventListener('change', updateTotalPrice);
    checkOutInput.addEventListener('change', updateTotalPrice);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\guest\rooms\book.blade.php ENDPATH**/ ?>