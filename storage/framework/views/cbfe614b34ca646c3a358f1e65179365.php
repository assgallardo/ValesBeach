<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8 flex items-center">
        <a href="<?php echo e(route('admin.reservations')); ?>" 
           class="inline-flex items-center text-gray-400 hover:text-white mr-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reservations
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Reserve Room: <?php echo e($room->name); ?></h1>
            <p class="text-gray-400 mt-2">Create a reservation for this specific room</p>
        </div>
    </div>

    <!-- Room Information Card -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Room Details</h3>
                <p class="text-gray-300"><strong>Name:</strong> <?php echo e($room->name); ?></p>
                <p class="text-gray-300"><strong>Type:</strong> <?php echo e($room->type ?? 'Standard'); ?></p>
                <p class="text-gray-300"><strong>Capacity:</strong> <?php echo e($room->capacity); ?> guests</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Pricing</h3>
                <p class="text-green-400 text-2xl font-bold">₱<?php echo e(number_format($room->price, 2)); ?></p>
                <p class="text-gray-400">per night</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Amenities</h3>
                <p class="text-gray-300"><?php echo e($room->description ?? 'Standard amenities included'); ?></p>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-gray-800 rounded-lg p-8">
        <h2 class="text-xl font-semibold text-white mb-6">Guest & Booking Details</h2>
        
        <form action="<?php echo e(route('admin.reservations.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="room_id" value="<?php echo e($room->id); ?>">
            
            <!-- Guest Selection or Creation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Guest Information *</label>
                        
                        <!-- Guest Type Selection -->
                        <div class="flex space-x-4 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="guest_type" value="existing" checked onchange="toggleAdminGuestFields('existing')" class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-300">Select Existing Guest</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="guest_type" value="new" onchange="toggleAdminGuestFields('new')" class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-300">Create New Guest</span>
                            </label>
                        </div>

                        <!-- Existing Guest Selection -->
                        <div id="admin_existing_guest_section" style="display: block;" class="space-y-4">
                            <select name="user_id" id="admin_user_id"
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Choose a guest...</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(old('user_id') == $user->id ? 'selected' : ''); ?>>
                                        <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- New Guest Creation -->
                        <div id="admin_new_guest_section" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Guest Name *</label>
                                <input type="text" name="guest_name" id="admin_guest_name"
                                       value="<?php echo e(old('guest_name')); ?>"
                                       placeholder="Enter guest full name"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Guest Email *</label>
                                <input type="email" name="guest_email" id="admin_guest_email"
                                       value="<?php echo e(old('guest_email')); ?>"
                                       placeholder="Enter guest email address"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['guest_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['guest_email'];
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

            <!-- Booking Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Check-in Date *</label>
                    <input type="date" name="check_in" required id="check_in"
                           value="<?php echo e(old('check_in', date('Y-m-d'))); ?>"
                           min="<?php echo e(date('Y-m-d')); ?>"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <?php $__errorArgs = ['check_in'];
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
                    <label class="block text-sm font-medium text-gray-300 mb-2">Check-out Date *</label>
                    <input type="date" name="check_out" required id="check_out"
                           value="<?php echo e(old('check_out', date('Y-m-d'))); ?>"
                           min="<?php echo e(date('Y-m-d')); ?>"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <?php $__errorArgs = ['check_out'];
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
                    <label class="block text-sm font-medium text-gray-300 mb-2">Number of Guests *</label>
                    <input type="number" name="guests" required min="1" max="<?php echo e($room->capacity); ?>"
                           value="<?php echo e(old('guests', 1)); ?>"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-400 mt-1">Maximum: <?php echo e($room->capacity); ?> guests</p>
                    <?php $__errorArgs = ['guests'];
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

            <!-- Early Check-in and Late Checkout Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-600 rounded-lg p-4 bg-gray-700 bg-opacity-50">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="early_checkin" value="1" <?php echo e(old('early_checkin') ? 'checked' : ''); ?>

                               class="w-5 h-5 text-green-600 bg-gray-600 border-gray-500 rounded focus:ring-green-500">
                        <span class="ml-3 text-white font-medium">Early Check-in</span>
                    </label>
                    <p class="text-gray-400 text-sm mt-2 ml-8">
                        Check-in before standard time (Fee: ₱500)
                    </p>
                    <div class="ml-8 mt-2">
                        <input type="time" name="early_checkin_time" <?php echo e(old('early_checkin_time')); ?>

                               class="px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-sm">
                    </div>
                </div>

                <div class="border border-gray-600 rounded-lg p-4 bg-gray-700 bg-opacity-50">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="late_checkout" value="1" <?php echo e(old('late_checkout') ? 'checked' : ''); ?>

                               class="w-5 h-5 text-green-600 bg-gray-600 border-gray-500 rounded focus:ring-green-500">
                        <span class="ml-3 text-white font-medium">Late Check-out</span>
                    </label>
                    <p class="text-gray-400 text-sm mt-2 ml-8">
                        Check-out after standard time (Fee: ₱500)
                    </p>
                    <div class="ml-8 mt-2">
                        <input type="time" name="late_checkout_time" <?php echo e(old('late_checkout_time')); ?>

                               class="px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-sm">
                    </div>
                </div>
            </div>

            <!-- Status and Price Preview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Booking Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="confirmed" <?php echo e(old('status', 'confirmed') == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                        <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="checked_in" <?php echo e(old('status') == 'checked_in' ? 'selected' : ''); ?>>Checked In</option>
                        <option value="checked_out" <?php echo e(old('status') == 'checked_out' ? 'selected' : ''); ?>>Checked Out</option>
                        <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                    <?php $__errorArgs = ['status'];
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

                <!-- Price Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Total Amount</label>
                    <div class="w-full px-4 py-3 bg-gray-600 border border-gray-600 rounded-lg">
                        <div class="text-green-400 text-xl font-bold" id="total_preview">
                            Select dates to calculate
                        </div>
                        <div class="text-gray-400 text-sm" id="breakdown_preview">
                            ₱<?php echo e(number_format($room->price, 2)); ?> per night
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="<?php echo e(route('admin.bookings')); ?>" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                    Create Booking for <?php echo e($room->name); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const totalPreview = document.getElementById('total_preview');
    const breakdownPreview = document.getElementById('breakdown_preview');
    const pricePerNight = <?php echo e($room->price); ?>;

    // Guest type toggle function
    function toggleAdminGuestFields(type) {
        const existingSection = document.getElementById('admin_existing_guest_section');
        const newSection = document.getElementById('admin_new_guest_section');
        const userIdField = document.getElementById('admin_user_id');
        const guestNameField = document.getElementById('admin_guest_name');
        const guestEmailField = document.getElementById('admin_guest_email');

        if (type === 'existing') {
            existingSection.style.display = 'block';
            newSection.style.display = 'none';
            // Clear new guest fields
            if (guestNameField) guestNameField.value = '';
            if (guestEmailField) guestEmailField.value = '';
        } else {
            existingSection.style.display = 'none';
            newSection.style.display = 'grid';
            // Clear existing guest field
            if (userIdField) userIdField.value = '';
        }
    }

    // Make function globally accessible
    window.toggleAdminGuestFields = toggleAdminGuestFields;

    function updateTotalPreview() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (checkInInput.value && checkOutInput.value && checkOutDate >= checkInDate) {
            let nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            
            // Same-day booking counts as 1 night
            if (nights === 0) {
                nights = 1;
            }
            
            const total = pricePerNight * nights;
            
            totalPreview.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            breakdownPreview.textContent = `₱${pricePerNight.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} × ${nights} night${nights > 1 ? 's' : ''}`;
        } else {
            totalPreview.textContent = 'Select dates to calculate';
            breakdownPreview.textContent = `₱${pricePerNight.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per night`;
        }
    }

    // Update check-out minimum date when check-in changes
    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        // Allow same-day booking - min checkout is same as check-in
        checkOutInput.min = this.value;
        
        // If checkout is before check-in, set it to check-in date (same-day booking)
        if (checkOutInput.value && new Date(checkOutInput.value) < checkInDate) {
            checkOutInput.value = this.value;
        }
        
        updateTotalPreview();
    });

    checkOutInput.addEventListener('change', updateTotalPreview);

    // Initial calculation
    updateTotalPreview();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\admin\bookings\create-from-room.blade.php ENDPATH**/ ?>