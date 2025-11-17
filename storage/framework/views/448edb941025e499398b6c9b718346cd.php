<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Back Button -->
    <a href="<?php echo e(route('guest.rooms.browse')); ?>" 
       class="inline-flex items-center text-gray-300 hover:text-white mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Facilities
    </a>

    <!-- Facility Details Card -->
    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden">
        <!-- Image Gallery -->
        <div class="relative h-[500px]" x-data="{ activeSlide: 0 }">
            <?php if($room->images->isNotEmpty()): ?>
                <!-- Main Image -->
                <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>"
                         class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                         x-show="activeSlide === <?php echo e($index); ?>"
                         alt="<?php echo e($room->name); ?>">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Image Navigation -->
                <?php if($room->images->count() > 1): ?>
                    <!-- Previous/Next Buttons -->
                    <div class="absolute inset-0 flex items-center justify-between p-4">
                        <button @click="activeSlide = activeSlide === 0 ? <?php echo e($room->images->count() - 1); ?> : activeSlide - 1"
                                class="bg-black/50 text-white p-2 rounded-full hover:bg-black/75">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button @click="activeSlide = activeSlide === <?php echo e($room->images->count() - 1); ?> ? 0 : activeSlide + 1"
                                class="bg-black/50 text-white p-2 rounded-full hover:bg-black/75">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Image Indicators -->
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                        <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button @click="activeSlide = <?php echo e($index); ?>"
                                    class="w-3 h-3 rounded-full transition-colors duration-200"
                                    :class="activeSlide === <?php echo e($index); ?> ? 'bg-white' : 'bg-white/50'">
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Room Information -->
        <div class="p-8">
            <div class="flex flex-wrap gap-8">
                <!-- Left Column - Details -->
                <div class="flex-1 min-w-[320px]">
                    <h1 class="text-3xl font-bold text-white mb-4"><?php echo e($room->name); ?></h1>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-green-800/50 p-4 rounded-lg">
                            <span class="block text-gray-300 text-sm">Category</span>
                            <span class="text-white font-semibold"><?php echo e($room->category ?? 'Rooms'); ?></span>
                        </div>
                        <div class="bg-green-800/50 p-4 rounded-lg">
                            <span class="block text-gray-300 text-sm">Type</span>
                            <span class="text-white font-semibold"><?php echo e($room->type); ?></span>
                        </div>
                        <div class="bg-green-800/50 p-4 rounded-lg">
                            <span class="block text-gray-300 text-sm">Capacity</span>
                            <span class="text-white font-semibold"><?php echo e($room->capacity); ?> persons</span>
                        </div>
                        <div class="bg-green-800/50 p-4 rounded-lg">
                            <span class="block text-gray-300 text-sm">Beds</span>
                            <span class="text-white font-semibold"><?php echo e($room->beds); ?></span>
                        </div>
                        <?php if(!empty($room->check_in_time) || !empty($room->check_out_time)): ?>
                        <div class="bg-green-800/50 p-4 rounded-lg col-span-2 md:col-span-1">
                            <span class="block text-gray-300 text-sm">Check-in / Check-out</span>
                            <span class="text-white font-semibold">
                                <?php if(!empty($room->check_in_time)): ?>
                                    Check-in: <?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $room->check_in_time)->format('g:i A')); ?><br>
                                <?php endif; ?>
                                <?php if(!empty($room->check_out_time)): ?>
                                    Check-out: <?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $room->check_out_time)->format('g:i A')); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-white mb-2">Description</h2>
                        <p class="text-gray-300"><?php echo e($room->description); ?></p>
                    </div>

                    <!-- Amenities -->
                    <?php if($room->amenities): ?>
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-white mb-2">Amenities</h2>
                            <div class="grid grid-cols-2 gap-2">
                                <?php $__currentLoopData = is_array($room->amenities) ? $room->amenities : json_decode($room->amenities, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center text-gray-300">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <?php echo e($amenity); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Booking Info -->
                <div class="w-full md:w-96">
                    <div class="bg-green-800/50 rounded-lg p-6 sticky top-4" 
                         x-data="{ 
                            checkIn: '',
                            checkOut: '',
                            nights: 0,
                            basePrice: <?php echo e($room->price); ?>,
                            get totalPrice() {
                                return this.nights * this.basePrice;
                            },
                            calculateNights() {
                                if (this.checkIn && this.checkOut) {
                                    const start = new Date(this.checkIn);
                                    const end = new Date(this.checkOut);
                                    const msPerDay = 1000 * 60 * 60 * 24;
                                    // Calculate difference in days
                                    const diff = (end - start) / msPerDay;
                                    this.nights = diff >= 0 ? (diff === 0 ? 1 : diff) : 0;
                                } else {
                                    this.nights = 0;
                                }
                            }
                         }">
                        <!-- Base Price Display -->
                        <div class="text-2xl font-bold text-white mb-4">
                            ₱<?php echo e(number_format($room->price, 2)); ?>

                            <span class="text-sm font-normal text-gray-300">per night</span>
                        </div>

                        <!-- Availability Status -->
                        <div class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm 
                                      <?php echo e($isAvailable ? 'bg-green-600/50 text-green-100' : 'bg-red-600/50 text-red-100'); ?>">
                                <?php echo e($isAvailable ? 'Available' : 'Not Available'); ?>

                            </span>
                        </div>

                        <?php if($isAvailable): ?>
                            <!-- Display any errors -->
                            <?php if($errors->any()): ?>
                                <div class="bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-4">
                                    <ul class="list-disc list-inside">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <!-- Booking Form -->
                            <form action="<?php echo e(route('guest.rooms.book.store', $room)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="space-y-4">
                                    <!-- Check-in Date -->
                                    <div>
                                        <label class="block text-gray-300 mb-1">Check-in Date</label>
                                        <input type="date" 
                                               name="check_in"
                                               x-model="checkIn"
                                               :min="new Date().toISOString().split('T')[0]"
                                               @change="calculateNights"
                                               class="w-full bg-green-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                                               required>
                                    </div>

                                    <!-- Check-out Date -->
                                    <div>
                                        <label class="block text-gray-300 mb-1">Check-out Date</label>
                                        <input type="date" 
                                               name="check_out"
                                               x-model="checkOut"
                                               :min="checkIn || new Date().toISOString().split('T')[0]"
                                               @change="calculateNights"
                                               class="w-full bg-green-700 text-white rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"
                                               required>
                                    </div>

                                    <!-- Early Check-in Option -->
                                    <div class="border border-green-700 rounded-lg p-4 bg-green-800 bg-opacity-30">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="early_checkin" 
                                                   value="1"
                                                   class="w-5 h-5 text-green-600 bg-green-700 border-green-600 rounded focus:ring-green-500">
                                            <span class="ml-3 text-white font-medium">Request Early Check-in</span>
                                        </label>
                                        <p class="text-gray-400 text-sm mt-2 ml-8">
                                            Disclaimer: early check-in depends on room availability upon arrival
                                        </p>
                                    </div>

                                    <!-- Late Check-out Option -->
                                    <div class="border border-green-700 rounded-lg p-4 bg-green-800 bg-opacity-30">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="late_checkout" 
                                                   value="1"
                                                   class="w-5 h-5 text-green-600 bg-green-700 border-green-600 rounded focus:ring-green-500">
                                            <span class="ml-3 text-white font-medium">Request Late Check-out</span>
                                        </label>
                                        <p class="text-gray-400 text-sm mt-2 ml-8">
                                            request late check out
                                        </p>
                                    </div>

                                    <!-- Price Calculation -->
                                    <div x-show="nights > 0" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         class="border-t border-green-700 pt-4 space-y-2">
                                        <div class="flex justify-between text-gray-300">
                                            <span>Base Price per Night:</span>
                                            <span>₱<?php echo e(number_format($room->price, 2)); ?></span>
                                        </div>
                                        <div class="flex justify-between text-gray-300">
                                            <span>Number of Nights:</span>
                                            <span x-text="nights"></span>
                                        </div>
                                        <div class="flex justify-between text-white font-bold text-lg pt-2 border-t border-green-700">
                                            <span>Total Price:</span>
                                            <span x-text="`₱${totalPrice.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`"></span>
                                        </div>
                                    </div>

                                    <!-- Book Now Button -->
                                    <button type="submit"
                                            class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors"
                                            :disabled="nights <= 0"
                                            :class="{ 'opacity-50 cursor-not-allowed': nights <= 0 }">
                                        Book Now
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/rooms/show.blade.php ENDPATH**/ ?>