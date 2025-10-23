<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-4xl">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Book Service</h1>
                    <p class="text-gray-400">Request booking for <?php echo e($service->name); ?></p>
                </div>
                <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Service
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
        <div class="bg-green-500/10 border border-green-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-green-400"><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-red-400"><?php echo e(session('error')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Error Messages from Validation -->
        <?php if($errors->any()): ?>
        <div class="bg-red-500/10 border border-red-500 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-red-400 font-semibold mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside text-red-400 space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Service Summary Card -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8 shadow-lg">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <?php if($service->category === 'spa'): ?>
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php elseif($service->category === 'dining'): ?>
                    <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <?php elseif($service->category === 'transportation'): ?>
                    <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    <?php elseif($service->category === 'activities'): ?>
                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <?php else: ?>
                    <svg class="w-10 h-10 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-white"><?php echo e($service->name); ?></h3>
                    <p class="text-gray-400"><?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?></p>
                    <p class="text-green-400 text-xl font-bold mt-1">₱<?php echo e(number_format($service->price, 2)); ?></p>
                </div>
                <?php if($service->duration): ?>
                <div class="text-right">
                    <p class="text-gray-400 text-sm">Duration</p>
                    <p class="text-white text-lg font-semibold">
                        <?php if($service->duration >= 60): ?>
                            <?php echo e(floor($service->duration / 60)); ?>h <?php echo e($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : ''); ?>

                        <?php else: ?>
                            <?php echo e($service->duration); ?>m
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-gray-800 rounded-lg p-8 shadow-lg">
            <h2 class="text-2xl font-bold text-white mb-6">Booking Details</h2>
            
            <form action="<?php echo e(route('guest.services.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>

                <!-- Hidden Fields -->
                <input type="hidden" name="service_id" value="<?php echo e($service->id); ?>">
                <input type="hidden" name="service_type" value="<?php echo e($service->name); ?>">
                <input type="hidden" name="description" id="description" value="Guest booking for <?php echo e($service->name); ?> - Service request">

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="requested_date" class="block text-gray-300 text-sm font-medium mb-2">
                            Preferred Date <span class="text-red-400">*</span>
                        </label>
                        <input type="date" 
                               id="requested_date" 
                               name="requested_date" 
                               value="<?php echo e(old('requested_date')); ?>"
                               min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                               required
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent <?php $__errorArgs = ['requested_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['requested_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="requested_time" class="block text-gray-300 text-sm font-medium mb-2">
                            Preferred Time <span class="text-red-400">*</span>
                        </label>
                        <select id="requested_time" 
                                name="requested_time" 
                                required
                                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent <?php $__errorArgs = ['requested_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Select Time</option>
                            <option value="08:00" <?php echo e(old('requested_time') === '08:00' ? 'selected' : ''); ?>>8:00 AM</option>
                            <option value="09:00" <?php echo e(old('requested_time') === '09:00' ? 'selected' : ''); ?>>9:00 AM</option>
                            <option value="10:00" <?php echo e(old('requested_time') === '10:00' ? 'selected' : ''); ?>>10:00 AM</option>
                            <option value="11:00" <?php echo e(old('requested_time') === '11:00' ? 'selected' : ''); ?>>11:00 AM</option>
                            <option value="12:00" <?php echo e(old('requested_time') === '12:00' ? 'selected' : ''); ?>>12:00 PM</option>
                            <option value="13:00" <?php echo e(old('requested_time') === '13:00' ? 'selected' : ''); ?>>1:00 PM</option>
                            <option value="14:00" <?php echo e(old('requested_time') === '14:00' ? 'selected' : ''); ?>>2:00 PM</option>
                            <option value="15:00" <?php echo e(old('requested_time') === '15:00' ? 'selected' : ''); ?>>3:00 PM</option>
                            <option value="16:00" <?php echo e(old('requested_time') === '16:00' ? 'selected' : ''); ?>>4:00 PM</option>
                            <option value="17:00" <?php echo e(old('requested_time') === '17:00' ? 'selected' : ''); ?>>5:00 PM</option>
                            <option value="18:00" <?php echo e(old('requested_time') === '18:00' ? 'selected' : ''); ?>>6:00 PM</option>
                            <option value="19:00" <?php echo e(old('requested_time') === '19:00' ? 'selected' : ''); ?>>7:00 PM</option>
                            <option value="20:00" <?php echo e(old('requested_time') === '20:00' ? 'selected' : ''); ?>>8:00 PM</option>
                        </select>
                        <?php $__errorArgs = ['requested_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Hidden scheduled_date field -->
                <input type="hidden" name="scheduled_date" id="scheduled_date">

                <!-- Number of Guests -->
                <div>
                    <label for="guests_count" class="block text-gray-300 text-sm font-medium mb-2">
                        Number of Guests <span class="text-red-400">*</span>
                        <?php if($service->capacity): ?>
                        <span class="text-gray-400 text-sm">(Maximum: <?php echo e($service->capacity); ?>)</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" 
                           id="guests_count" 
                           name="guests_count" 
                           value="<?php echo e(old('guests_count', 1)); ?>"
                           min="1"
                           <?php if($service->capacity): ?> max="<?php echo e($service->capacity); ?>" <?php endif; ?>
                           required
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent <?php $__errorArgs = ['guests_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['guests_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Special Requests -->
                <div>
                    <label for="special_requests" class="block text-gray-300 text-sm font-medium mb-2">
                        Special Requests or Notes
                    </label>
                    <textarea id="special_requests" 
                              name="special_requests" 
                              rows="4"
                              placeholder="Any special requirements, allergies, or preferences..."
                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent <?php $__errorArgs = ['special_requests'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('special_requests')); ?></textarea>
                    <?php $__errorArgs = ['special_requests'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Booking Information -->
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="text-blue-200 font-medium mb-2">Booking Information</h4>
                            <ul class="text-gray-300 text-sm space-y-1">
                                <li>• This is a booking request. Confirmation will be sent via email/SMS</li>
                                <li>• Our staff will contact you within 24 hours to confirm availability</li>
                                <li>• Payment can be made upon arrival or as directed by our staff</li>
                                <li>• Cancellations must be made at least 24 hours in advance</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                    <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                       class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Submit Booking Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Combine date and time into scheduled_date
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const dateInput = document.getElementById('requested_date');
    const timeInput = document.getElementById('requested_time');
    const scheduledDateInput = document.getElementById('scheduled_date');

    function updateScheduledDate() {
        const date = dateInput.value;
        const time = timeInput.value;
        
        if (date && time) {
            scheduledDateInput.value = `${date} ${time}:00`;
        }
    }

    dateInput.addEventListener('change', updateScheduledDate);
    timeInput.addEventListener('change', updateScheduledDate);

    form.addEventListener('submit', function(e) {
        updateScheduledDate();
        
        if (!scheduledDateInput.value) {
            e.preventDefault();
            alert('Please select both date and time for your service.');
            return false;
        }

        const scheduledDate = new Date(scheduledDateInput.value);
        const now = new Date();
        
        if (scheduledDate <= now) {
            e.preventDefault();
            alert('Please select a future date and time for your service.');
            return false;
        }
    });

    updateScheduledDate();
});

// Update description with guest count
document.getElementById('guests_count').addEventListener('change', function() {
    const guestCount = this.value;
    const serviceName = document.querySelector('[name="service_type"]').value;
    const descriptionField = document.querySelector('[name="description"]');
    
    if (guestCount && serviceName) {
        descriptionField.value = `Guest booking for ${serviceName} - ${guestCount} guest${guestCount > 1 ? 's' : ''}`;
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/guest/services/request.blade.php ENDPATH**/ ?>