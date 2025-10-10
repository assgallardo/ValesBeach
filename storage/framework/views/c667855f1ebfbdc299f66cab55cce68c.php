<?php $__env->startSection('content'); ?>
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16 max-w-4xl">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Book Service
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Request booking for <?php echo e($service->name); ?>

            </p>
            <div class="mt-6">
                <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Service Details
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
        <div class="bg-green-600/20 border border-green-500/50 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-3"></i>
                <span class="text-green-100"><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
        <div class="bg-red-600/20 border border-red-500/50 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                <span class="text-red-100"><?php echo e(session('error')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Service Summary Card -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6 mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-green-800/50 rounded-lg flex items-center justify-center">
                    <?php if($service->category === 'spa'): ?>
                    <i class="fas fa-spa text-2xl text-green-400"></i>
                    <?php elseif($service->category === 'dining'): ?>
                    <i class="fas fa-utensils text-2xl text-orange-400"></i>
                    <?php elseif($service->category === 'transportation'): ?>
                    <i class="fas fa-car text-2xl text-blue-400"></i>
                    <?php elseif($service->category === 'activities'): ?>
                    <i class="fas fa-swimmer text-2xl text-purple-400"></i>
                    <?php else: ?>
                    <i class="fas fa-concierge-bell text-2xl text-yellow-400"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-green-50"><?php echo e($service->name); ?></h3>
                    <p class="text-green-300"><?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?></p>
                    <p class="text-green-400 font-semibold">₱<?php echo e(number_format($service->price, 2)); ?></p>
                </div>
                <?php if($service->duration): ?>
                <div class="text-right">
                    <p class="text-green-200 text-sm">Duration</p>
                    <p class="text-green-50 font-semibold">
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
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8">
            <form action="<?php echo e(route('guest.services.store')); ?>" method="POST" class="space-y-6">
                <?php echo csrf_field(); ?>

                <!-- Hidden Fields for Controller -->
                <input type="hidden" name="service_id" value="<?php echo e($service->id); ?>">
                <input type="hidden" name="service_type" value="<?php echo e($service->name); ?>">
                <input type="hidden" name="description" value="Guest booking for <?php echo e($service->name); ?> - Service request">

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="requested_date" class="block text-green-200 text-sm font-medium mb-2">
                            Preferred Date <span class="text-red-400">*</span>
                        </label>
                        <input type="date" 
                               id="requested_date" 
                               name="requested_date" 
                               value="<?php echo e(old('requested_date')); ?>"
                               min="<?php echo e(date('Y-m-d', strtotime('+1 day'))); ?>"
                               required
                               class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                        <label for="requested_time" class="block text-green-200 text-sm font-medium mb-2">
                            Preferred Time <span class="text-red-400">*</span>
                        </label>
                        <select id="requested_time" 
                                name="requested_time" 
                                required
                                class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
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

                <!-- Hidden scheduled_date field (combination of date and time) -->
                <input type="hidden" name="scheduled_date" id="scheduled_date">

                <!-- Number of Guests -->
                <div>
                    <label for="guests_count" class="block text-green-200 text-sm font-medium mb-2">
                        Number of Guests <span class="text-red-400">*</span>
                        <?php if($service->capacity): ?>
                        <span class="text-green-400 text-sm">(Maximum: <?php echo e($service->capacity); ?>)</span>
                        <?php endif; ?>
                    </label>
                    <input type="number" 
                           id="guests_count" 
                           name="guests_count" 
                           value="<?php echo e(old('guests_count', 1)); ?>"
                           min="1"
                           <?php if($service->capacity): ?> max="<?php echo e($service->capacity); ?>" <?php endif; ?>
                           required
                           class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                    <label for="special_requests" class="block text-green-200 text-sm font-medium mb-2">
                        Special Requests or Notes
                    </label>
                    <textarea id="special_requests" 
                              name="special_requests" 
                              rows="4"
                              placeholder="Any special requirements, allergies, or preferences..."
                              class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent"><?php echo e(old('special_requests')); ?></textarea>
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
                <div class="bg-green-800/20 border border-green-600/30 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-400 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-green-200 font-medium mb-2">Booking Information</h4>
                            <ul class="text-green-300 text-sm space-y-1">
                                <li>• This is a booking request. Confirmation will be sent via email/SMS</li>
                                <li>• Our staff will contact you within 24 hours to confirm availability</li>
                                <li>• Payment can be made upon arrival or as directed by our staff</li>
                                <li>• Cancellations must be made at least 24 hours in advance</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="<?php echo e(route('guest.services.show', $service)); ?>" 
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Booking Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
// Combine date and time into scheduled_date when form is submitted
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const dateInput = document.getElementById('requested_date');
    const timeInput = document.getElementById('requested_time');
    const scheduledDateInput = document.getElementById('scheduled_date');

    // Function to update scheduled_date
    function updateScheduledDate() {
        const date = dateInput.value;
        const time = timeInput.value;
        
        if (date && time) {
            const scheduledDateTime = `${date} ${time}:00`;
            scheduledDateInput.value = scheduledDateTime;
            console.log('Updated scheduled_date:', scheduledDateTime);
        }
    }

    // Update scheduled_date when date or time changes
    dateInput.addEventListener('change', updateScheduledDate);
    timeInput.addEventListener('change', updateScheduledDate);

    // Update scheduled_date before form submission
    form.addEventListener('submit', function(e) {
        updateScheduledDate();
        
        // Validate that scheduled_date is set
        if (!scheduledDateInput.value) {
            e.preventDefault();
            alert('Please select both date and time for your service.');
            return false;
        }

        // Validate that the scheduled date is in the future
        const scheduledDate = new Date(scheduledDateInput.value);
        const now = new Date();
        
        if (scheduledDate <= now) {
            e.preventDefault();
            alert('Please select a future date and time for your service.');
            return false;
        }

        console.log('Form submission with data:', {
            service_id: document.querySelector('[name="service_id"]').value,
            service_type: document.querySelector('[name="service_type"]').value,
            description: document.querySelector('[name="description"]').value,
            scheduled_date: scheduledDateInput.value,
            guests_count: document.querySelector('[name="guests_count"]').value,
            special_requests: document.querySelector('[name="special_requests"]').value
        });
    });

    // Initialize scheduled_date if both date and time are already selected
    updateScheduledDate();
});

// Auto-update description to include guest count when it changes
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
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/services/request.blade.php ENDPATH**/ ?>