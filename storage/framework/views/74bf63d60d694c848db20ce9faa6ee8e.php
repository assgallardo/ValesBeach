

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h1 class="text-3xl font-bold text-white mb-2">Book <?php echo e($cottage->name); ?></h1>
            <p class="text-gray-300">Complete the form below to book your Bahay Kubo</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('guest.cottages.book.store', $cottage)); ?>" method="POST" id="bookingForm" class="space-y-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Booking Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Booking Type -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <label class="block text-white font-semibold mb-4">Booking Type *</label>
                        <div class="grid grid-cols-2 gap-4" x-data="{ bookingType: 'day_use' }">
                            <?php if($cottage->allow_day_use): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="day_use" 
                                       x-model="bookingType"
                                       class="sr-only peer" required>
                                <div class="bg-gray-700 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-400 rounded-lg p-4 text-center transition-all">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-white font-semibold">Day Use</p>
                                    <p class="text-gray-400 text-sm">6AM - 6PM</p>
                                    <p class="text-green-400 font-bold mt-2"><?php echo e($cottage->formatted_price_per_day); ?></p>
                                </div>
                            </label>
                            <?php endif; ?>

                            <?php if($cottage->allow_overnight): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="overnight" 
                                       x-model="bookingType"
                                       class="sr-only peer" <?php echo e(!$cottage->allow_day_use ? 'required' : ''); ?>>
                                <div class="bg-gray-700 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-400 rounded-lg p-4 text-center transition-all">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                    <p class="text-white font-semibold">Overnight</p>
                                    <p class="text-gray-400 text-sm">24 hours</p>
                                    <p class="text-purple-400 font-bold mt-2"><?php echo e($cottage->formatted_price_per_day); ?></p>
                                </div>
                            </label>
                            <?php endif; ?>

                            <?php if($cottage->price_per_hour): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="hourly" 
                                       x-model="bookingType"
                                       class="sr-only peer">
                                <div class="bg-gray-700 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-400 rounded-lg p-4 text-center transition-all">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-white font-semibold">Hourly</p>
                                    <p class="text-gray-400 text-sm"><?php echo e($cottage->min_hours); ?>-<?php echo e($cottage->max_hours); ?> hrs</p>
                                    <p class="text-blue-400 font-bold mt-2"><?php echo e($cottage->formatted_price_per_hour); ?>/hr</p>
                                </div>
                            </label>
                            <?php endif; ?>

                            <?php if($cottage->allow_events): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="event" 
                                       x-model="bookingType"
                                       class="sr-only peer">
                                <div class="bg-gray-700 peer-checked:bg-blue-600 peer-checked:ring-2 peer-checked:ring-blue-400 rounded-lg p-4 text-center transition-all">
                                    <svg class="w-8 h-8 mx-auto text-gray-400 peer-checked:text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/>
                                    </svg>
                                    <p class="text-white font-semibold">Event</p>
                                    <p class="text-gray-400 text-sm">Special rate</p>
                                    <p class="text-yellow-400 font-bold mt-2">Contact us</p>
                                </div>
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="bg-gray-800 rounded-lg p-6" x-data="{ bookingType: 'day_use' }">
                        <h3 class="text-white font-semibold mb-4">Date & Time</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 mb-2">Check-in Date *</label>
                                <input type="date" 
                                       name="check_in_date" 
                                       id="check_in_date"
                                       min="<?php echo e(date('Y-m-d')); ?>"
                                       value="<?php echo e(old('check_in_date')); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       required>
                            </div>

                            <div x-show="bookingType === 'overnight'">
                                <label class="block text-gray-300 mb-2">Check-out Date *</label>
                                <input type="date" 
                                       name="check_out_date" 
                                       id="check_out_date"
                                       min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                                       value="<?php echo e(old('check_out_date')); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div x-show="bookingType === 'hourly'">
                                <label class="block text-gray-300 mb-2">Number of Hours *</label>
                                <input type="number" 
                                       name="hours" 
                                       min="<?php echo e($cottage->min_hours ?? 1); ?>"
                                       max="<?php echo e($cottage->max_hours ?? 12); ?>"
                                       value="<?php echo e(old('hours', $cottage->min_hours ?? 4)); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-white font-semibold mb-4">Guest Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-300 mb-2">Number of Guests *</label>
                                <input type="number" 
                                       name="guests" 
                                       min="1" 
                                       max="<?php echo e($cottage->capacity); ?>"
                                       value="<?php echo e(old('guests', 1)); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       required>
                                <p class="text-gray-400 text-sm mt-1">Maximum: <?php echo e($cottage->capacity); ?> persons</p>
                            </div>

                            <div>
                                <label class="block text-gray-300 mb-2">Number of Children</label>
                                <input type="number" 
                                       name="children" 
                                       min="0"
                                       value="<?php echo e(old('children', 0)); ?>"
                                       class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <label class="block text-white font-semibold mb-2">Special Requests (Optional)</label>
                        <textarea name="special_requests" 
                                  rows="4"
                                  placeholder="Any special requests or requirements..."
                                  class="w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo e(old('special_requests')); ?></textarea>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-800 rounded-lg p-6 sticky top-4">
                        <h3 class="text-white font-semibold mb-4">Booking Summary</h3>
                        
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-gray-300">
                                <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="font-semibold text-white"><?php echo e($cottage->name); ?></span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span><?php echo e($cottage->location); ?></span>
                            </div>
                            <div class="flex items-center text-gray-300">
                                <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Up to <?php echo e($cottage->capacity); ?> guests</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-700 pt-4 mb-4">
                            <div class="flex justify-between text-gray-300 mb-2">
                                <span>Base Price:</span>
                                <span class="font-semibold text-white"><?php echo e($cottage->formatted_price_per_day); ?></span>
                            </div>
                            <?php if($cottage->security_deposit): ?>
                            <div class="flex justify-between text-gray-300">
                                <span>Security Deposit:</span>
                                <span class="text-yellow-400">₱<?php echo e(number_format($cottage->security_deposit, 2)); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="bg-blue-500/20 border border-blue-500 rounded-lg p-3 mb-4">
                            <p class="text-blue-300 text-sm">
                                <strong>Note:</strong> Final price will be calculated based on your selected dates and booking type.
                            </p>
                        </div>

                        <button type="submit" 
                                class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 font-semibold">
                            Confirm Booking
                        </button>

                        <a href="<?php echo e(route('guest.cottages.show', $cottage)); ?>" 
                           class="block w-full text-center bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors duration-200 mt-3">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const checkInInput = document.getElementById('check_in_date');
    const checkOutInput = document.getElementById('check_out_date');

    // Update check-out minimum date when check-in changes
    if (checkInInput && checkOutInput) {
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            checkInDate.setDate(checkInDate.getDate() + 1);
            checkOutInput.min = checkInDate.toISOString().split('T')[0];
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/guest/cottages/book.blade.php ENDPATH**/ ?>