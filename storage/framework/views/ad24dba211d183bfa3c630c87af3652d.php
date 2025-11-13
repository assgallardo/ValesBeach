<?php $__env->startSection('title', 'Checkout - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Header -->
        <div class="flex items-center mb-8">
            <a href="<?php echo e(route('guest.food-orders.cart')); ?>"
               class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-4xl font-bold text-white">Checkout</h1>
                <p class="text-gray-400 mt-1">Complete your order and enjoy your meal</p>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if($errors->any()): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-200 px-6 py-4 rounded-lg mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="list-disc list-inside mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="bg-red-900/50 border border-red-600 text-red-200 px-6 py-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <?php echo e(session('error')); ?>

        </div>
        <?php endif; ?>

        <form action="<?php echo e(route('guest.food-orders.place-order')); ?>" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <?php echo csrf_field(); ?>
            <!-- Order Details Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Information -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Delivery Information
                    </h2>
                    
                    <!-- Delivery Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-3">Delivery Type</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors">
                                <input type="radio" name="delivery_type" value="room_service"
                                       class="mr-3 w-4 h-4 text-green-600 focus:ring-green-500" <?php echo e(old('delivery_type', 'room_service') == 'room_service' ? 'checked' : ''); ?>

                                       onchange="toggleDeliveryLocation()">
                                <div class="flex-1">
                                    <span class="font-medium text-white">Room Service</span>
                                    <span class="text-sm text-green-400 ml-2">(+₱5.00 delivery fee)</span>
                                </div>
                            </label>
                            <label class="flex items-center p-4 bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors">
                                <input type="radio" name="delivery_type" value="pickup" 
                                       class="mr-3 w-4 h-4 text-green-600 focus:ring-green-500" <?php echo e(old('delivery_type') == 'pickup' ? 'checked' : ''); ?>

                                       onchange="toggleDeliveryLocation()">
                                <div class="flex-1">
                                    <span class="font-medium text-white">Pickup at Restaurant</span>
                                    <span class="text-sm text-gray-400 ml-2">(No delivery fee)</span>
                                </div>
                            </label>
                            <label class="flex items-center p-4 bg-gray-700/50 rounded-lg cursor-pointer hover:bg-gray-700 transition-colors">
                                <input type="radio" name="delivery_type" value="dining_room" 
                                       class="mr-3 w-4 h-4 text-green-600 focus:ring-green-500" <?php echo e(old('delivery_type') == 'dining_room' ? 'checked' : ''); ?>

                                       onchange="toggleDeliveryLocation()">
                                <div class="flex-1">
                                    <span class="font-medium text-white">Serve in Dining Room</span>
                                    <span class="text-sm text-gray-400 ml-2">(No delivery fee)</span>
                                </div>
                            </label>
                        </div>
                        <?php $__errorArgs = ['delivery_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Delivery Location -->
                    <div class="mb-6" id="delivery-location-section">
                        <label for="delivery_location" class="block text-sm font-medium text-gray-300 mb-2">
                            <span id="location-label">Room Number</span>
                        </label>
                        <input type="text" name="delivery_location" id="delivery_location" 
                               value="<?php echo e(old('delivery_location', $currentBooking ? $currentBooking->room->room_number ?? '' : '')); ?>"
                               class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent placeholder-gray-400" 
                               placeholder="Enter room number">
                        <?php $__errorArgs = ['delivery_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Requested Delivery Time -->
                    <div class="mb-6">
                        <label for="requested_delivery_time" class="block text-sm font-medium text-gray-300 mb-2">
                            Preferred Delivery Time (Optional)
                        </label>
                        <?php if($currentBooking): ?>
                            <?php
                                // Set minimum time to 30 minutes from now, but not before check-in date
                                $minTime = now()->addMinutes(30);
                                $checkInStart = $currentBooking->check_in_date->startOfDay();
                                $checkOutEnd = $currentBooking->check_out_date->endOfDay();
                                
                                // If current time + 30 min is before check-in, use check-in date
                                if ($minTime->lt($checkInStart)) {
                                    $minTime = $checkInStart;
                                }
                                
                                // If current time + 30 min is after check-out, show warning
                                $isAfterCheckout = $minTime->gt($checkOutEnd);
                            ?>
                            
                            <?php if(!$isAfterCheckout): ?>
                                <input type="datetime-local" name="requested_delivery_time" id="requested_delivery_time" 
                                       value="<?php echo e(old('requested_delivery_time')); ?>"
                                       min="<?php echo e($minTime->format('Y-m-d\TH:i')); ?>"
                                       max="<?php echo e($checkOutEnd->format('Y-m-d\TH:i')); ?>"
                                       class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <p class="text-sm text-gray-400 mt-2">
                                    Leave blank for ASAP delivery (estimated 30-45 minutes). 
                                    Delivery available during your booking period: 
                                    <span class="text-green-400"><?php echo e($currentBooking->check_in_date->format('M j')); ?> - <?php echo e($currentBooking->check_out_date->format('M j, Y')); ?></span>
                                </p>
                            <?php else: ?>
                                <div class="bg-yellow-900/30 border border-yellow-600/50 rounded-lg p-4">
                                    <p class="text-yellow-200 text-sm">
                                        Your booking has ended. Scheduled delivery is not available. Order will be delivered ASAP.
                                    </p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <input type="datetime-local" name="requested_delivery_time" id="requested_delivery_time" 
                                   value="<?php echo e(old('requested_delivery_time')); ?>"
                                   min="<?php echo e(now()->addMinutes(30)->format('Y-m-d\TH:i')); ?>"
                                   class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-sm text-gray-400 mt-2">
                                Leave blank for ASAP delivery (estimated 30-45 minutes)
                            </p>
                        <?php endif; ?>
                        <?php $__errorArgs = ['requested_delivery_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Special Instructions -->
                    <div class="mb-4">
                        <label for="special_instructions" class="block text-sm font-medium text-gray-300 mb-2">
                            Special Instructions (Optional)
                        </label>
                        <textarea name="special_instructions" id="special_instructions" rows="3"
                                  class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent placeholder-gray-400"
                                  placeholder="Any special requests or dietary considerations..."><?php echo e(old('special_instructions')); ?></textarea>
                        <?php $__errorArgs = ['special_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <?php if($currentBooking): ?>
                <!-- Booking Information -->
                <div class="bg-blue-900/30 border border-blue-600/50 rounded-lg p-5">
                    <h3 class="font-semibold text-blue-200 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Current Booking
                    </h3>
                    <p class="text-blue-100 text-sm space-y-1">
                        <span class="block"><strong>Booking:</strong> <?php echo e($currentBooking->booking_number); ?></span>
                        <?php if($currentBooking->room): ?>
                        <span class="block"><strong>Room:</strong> <?php echo e($currentBooking->room->room_number); ?> - <?php echo e($currentBooking->room->room_type); ?></span>
                        <?php endif; ?>
                        <span class="block"><strong>Dates:</strong> <?php echo e($currentBooking->check_in_date->format('M j')); ?> - <?php echo e($currentBooking->check_out_date->format('M j, Y')); ?></span>
                    </p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 sticky top-4 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Order Summary
                    </h2>
                    
                    <!-- Order Items -->
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between items-start p-3 bg-gray-700/50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-white"><?php echo e($item['menu_item']->name); ?></h4>
                                <p class="text-sm text-gray-400">Qty: <?php echo e($item['quantity']); ?></p>
                                <?php if($item['special_instructions']): ?>
                                <p class="text-xs text-gray-500 italic mt-1"><?php echo e($item['special_instructions']); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="font-semibold text-green-400 ml-2">
                                ₱<?php echo e(number_format($item['total'], 2)); ?>

                            </span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <!-- Pricing Breakdown -->
                    <div class="border-t border-gray-700 pt-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Subtotal</span>
                            <span class="font-semibold text-white">₱<?php echo e(number_format($subtotal, 2)); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Delivery Fee</span>
                            <span class="font-semibold text-white" id="delivery-fee">₱0.00</span>
                        </div>
                        <div class="border-t border-gray-700 pt-3">
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-900/50 to-green-800/50 rounded-lg">
                                <span class="text-lg font-bold text-white">Total</span>
                                <span class="text-2xl font-bold text-green-400" id="final-total">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="submit" 
                            class="w-full mt-6 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-4 px-4 rounded-lg font-bold transition-all duration-200 shadow-lg transform hover:scale-105 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Place Order
                    </button>
                    
                    <p class="text-xs text-gray-400 mt-4 text-center">
                        By placing this order, you agree to our terms and conditions. 
                        Payment will be processed upon delivery.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Checkout JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    toggleDeliveryLocation();
    
    // Update totals when delivery type changes
    document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            calculateTotal();
            toggleDeliveryLocation();
        });
    });
});

function toggleDeliveryLocation() {
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
    const locationSection = document.getElementById('delivery-location-section');
    const locationLabel = document.getElementById('location-label');
    const locationInput = document.getElementById('delivery_location');

    if (deliveryType === 'room_service') {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Room Number (Optional)';
        locationInput.placeholder = 'Enter room number';
        locationInput.required = false;
    } else if (deliveryType === 'pickup') {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Contact Number (Optional)';
        locationInput.placeholder = 'Enter phone number for pickup notification';
        locationInput.required = false;
    } else {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Table Preference (Optional)';
        locationInput.placeholder = 'Preferred seating area or table number';
        locationInput.required = false;
    }
}function calculateTotal() {
    const subtotal = <?php echo e($subtotal); ?>;
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;

    // Calculate delivery fee
    const deliveryFee = deliveryType === 'room_service' ? 5.00 : 0.00;

    // Calculate total (without tax)
    const total = subtotal + deliveryFee;

    // Update display
    document.getElementById('delivery-fee').textContent = '₱' + deliveryFee.toFixed(2);
    document.getElementById('final-total').textContent = '₱' + total.toFixed(2);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\food-orders\checkout.blade.php ENDPATH**/ ?>