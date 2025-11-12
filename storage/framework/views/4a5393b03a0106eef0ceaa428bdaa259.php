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
            <h1 class="text-3xl font-bold text-white">Create Manual Reservation</h1>
            <p class="text-gray-400 mt-2">Add a new reservation for a guest</p>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-gray-800 rounded-lg p-8">
        <form action="<?php echo e(route('admin.reservations.store')); ?>" method="POST" class="space-y-6">
            <?php echo csrf_field(); ?>
            
            <!-- Guest Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select Guest *</label>
                    <select name="user_id" required 
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Choose a guest...</option>
                        <?php $__currentLoopData = $users->where('role', 'guest'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('user_id') == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
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
                </div>

                <div x-data="{
                    open: false,
                    searchQuery: '',
                    selectedRoom: <?php echo e(old('room_id') ? old('room_id') : 'null'); ?>,
                    selectedRoomName: '<?php echo e(old('room_id') ? ($rooms->where('id', old('room_id'))->first()->name ?? 'Choose a room...') : 'Choose a room...'); ?>',
                    selectRoom(id, name, price, capacity) {
                        this.selectedRoom = id;
                        this.selectedRoomName = name;
                        this.open = false;
                        this.searchQuery = '';
                        // Update hidden input
                        document.getElementById('room_id_input').value = id;
                        // Update hidden select and its selected option's data attributes
                        const roomSelect = document.getElementById('room_select');
                        if (roomSelect) {
                            roomSelect.value = id;
                            // Update the selected option's data attributes
                            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                            if (selectedOption) {
                                selectedOption.setAttribute('data-price', price);
                                selectedOption.setAttribute('data-capacity', capacity);
                            }
                            // Trigger change event to update price calculation
                            roomSelect.dispatchEvent(new Event('change'));
                        }
                    },
                    clearSearch() {
                        this.searchQuery = '';
                        this.$refs.searchInput.focus();
                    }
                }" x-init="$nextTick(() => { 
                    if (selectedRoom) {
                        const roomSelect = document.getElementById('room_select');
                        if (roomSelect) {
                            roomSelect.value = selectedRoom;
                            roomSelect.dispatchEvent(new Event('change'));
                        }
                    }
                })">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select Room *</label>
                    
                    <!-- Hidden input for form submission -->
                    <input type="hidden" name="room_id" id="room_id_input" :value="selectedRoom" required>
                    
                    <!-- Hidden select for compatibility with existing JS -->
                    <select id="room_select" class="hidden">
                        <option value="">Choose a room...</option>
                        <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($room->id); ?>" 
                                    data-price="<?php echo e($room->price); ?>"
                                    data-capacity="<?php echo e($room->capacity); ?>">
                                <?php echo e($room->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                    <!-- Custom Dropdown Button -->
                    <div class="relative">
                        <button type="button"
                                @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()); }"
                                class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-left text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent flex items-center justify-between"
                                :class="selectedRoom ? 'border-gray-600' : 'border-red-500'">
                            <span x-text="selectedRoomName" class="truncate"></span>
                            <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             @click.away="open = false"
                             class="absolute z-50 w-full mt-2 bg-gray-800 rounded-lg shadow-xl border border-gray-700"
                             style="display: none;">
                            
                            <!-- Search Input -->
                            <div class="p-3 border-b border-gray-700">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input x-ref="searchInput"
                                           x-model="searchQuery"
                                           type="text"
                                           placeholder="Search facilities..."
                                           class="w-full pl-10 pr-10 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <button type="button"
                                            x-show="searchQuery.length > 0"
                                            @click="clearSearch()"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Room List -->
                            <div class="max-h-64 overflow-y-auto">
                                <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <button type="button"
                                            @click="selectRoom(<?php echo e($room->id); ?>, '<?php echo e($room->name); ?>', <?php echo e($room->price); ?>, <?php echo e($room->capacity); ?>)"
                                            x-show="searchQuery === '' || '<?php echo e(strtolower($room->name)); ?> <?php echo e(strtolower($room->category ?? '')); ?> <?php echo e(strtolower($room->type ?? '')); ?>'.includes(searchQuery.toLowerCase())"
                                            class="w-full text-left px-4 py-3 text-sm text-gray-300 hover:bg-gray-700 hover:text-white border-b border-gray-700 last:border-b-0 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="font-medium text-white"><?php echo e($room->name); ?></div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        ₱<?php echo e(number_format((float)$room->price, 2)); ?>/night
                                                    </span>
                                                    <span class="mx-2">•</span>
                                                    <span class="inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        <?php echo e($room->capacity); ?> guests max
                                                    </span>
                                                </div>
                                                <?php if($room->category): ?>
                                                <div class="mt-1">
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                                        <?php echo e($room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                                        <?php echo e($room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                                        <?php echo e($room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : ''); ?>">
                                                        <?php echo e($room->category); ?>

                                                    </span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                    </button>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php $__errorArgs = ['room_id'];
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

            <!-- Dates and Guests -->
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
                    <input type="number" name="guests" required min="1" max="10"
                           value="<?php echo e(old('guests', 1)); ?>"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
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

            <!-- Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Booking Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="pending" <?php echo e(old('status', 'confirmed') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="confirmed" <?php echo e(old('status', 'confirmed') == 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                        <option value="checked_in" <?php echo e(old('status', 'confirmed') == 'checked_in' ? 'selected' : ''); ?>>Checked In</option>
                        <option value="checked_out" <?php echo e(old('status', 'confirmed') == 'checked_out' ? 'selected' : ''); ?>>Checked Out</option>
                        <option value="cancelled" <?php echo e(old('status', 'confirmed') == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
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
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Estimated Total
                        </span>
                    </label>
                    <div class="w-full px-4 py-3 bg-gradient-to-r from-green-900 to-green-800 border-2 border-green-600 rounded-lg">
                        <span id="total_preview" class="text-xl font-bold text-white">Select room and dates</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Automatically calculated based on room and dates</p>
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
                    Create Booking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_select');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const guestsInput = document.querySelector('input[name="guests"]');
    const totalPreview = document.getElementById('total_preview');

    function updateTotalPreview() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (selectedRoom.value && checkInInput.value && checkOutInput.value && checkOutDate >= checkInDate) {
            const pricePerNight = parseFloat(selectedRoom.dataset.price);
            let nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            
            // Same-day booking counts as 1 night
            if (nights === 0) {
                nights = 1;
            }
            
            const total = pricePerNight * nights;
            
            totalPreview.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${nights} night${nights > 1 ? 's' : ''})`;
        } else {
            totalPreview.textContent = 'Select room and dates';
        }
    }

    function validateGuests() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        if (selectedRoom.value && guestsInput.value) {
            const capacity = parseInt(selectedRoom.dataset.capacity);
            const guests = parseInt(guestsInput.value);
            
            if (guests > capacity) {
                guestsInput.setCustomValidity(`This room can accommodate maximum ${capacity} guests`);
                guestsInput.style.borderColor = '#ef4444';
            } else {
                guestsInput.setCustomValidity('');
                guestsInput.style.borderColor = '#4b5563';
            }
        }
    }

    // Update room capacity limit when room changes
    roomSelect.addEventListener('change', function() {
        const selectedRoom = this.options[this.selectedIndex];
        if (selectedRoom.value) {
            const capacity = parseInt(selectedRoom.dataset.capacity);
            guestsInput.max = capacity;
            validateGuests();
        }
        updateTotalPreview();
    });

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
    guestsInput.addEventListener('input', validateGuests);

    // Initial calculation
    updateTotalPreview();
    validateGuests();
});

// Pre-select room if passed from quick book
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($selectedRoom)): ?>
        // Auto-select the room
        const roomSelect = document.getElementById('selected_room_id');
        if (roomSelect) {
            roomSelect.value = '<?php echo e($selectedRoom->id); ?>';
        }
        
        // Auto-populate room selection
        selectedRoomData = {
            id: <?php echo e($selectedRoom->id); ?>,
            name: '<?php echo e($selectedRoom->name); ?>',
            price: <?php echo e($selectedRoom->price); ?>,
            capacity: <?php echo e($selectedRoom->capacity); ?>

        };
        
        // Show selected room info
        document.getElementById('selected_room_name').textContent = '<?php echo e($selectedRoom->name); ?>';
        document.getElementById('selected_room_details').textContent = 'Capacity: <?php echo e($selectedRoom->capacity); ?> guests • ₱<?php echo e(number_format($selectedRoom->price)); ?>/night';
        document.getElementById('selectedRoomSection').classList.remove('hidden');
        
        // Calculate initial total if dates are set
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        if (checkIn && checkOut) {
            selectRoom(<?php echo e($selectedRoom->id); ?>, '<?php echo e($selectedRoom->name); ?>', <?php echo e($selectedRoom->price); ?>, <?php echo e($selectedRoom->capacity); ?>);
        }
        
        showNotification('Room <?php echo e($selectedRoom->name); ?> pre-selected', 'success');
    <?php endif; ?>
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/bookings/create.blade.php ENDPATH**/ ?>