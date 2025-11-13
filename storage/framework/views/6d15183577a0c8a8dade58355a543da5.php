<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Edit Booking
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Reference: <?php echo e($booking->booking_reference); ?>

                </p>
                <div class="mt-6">
                    <a href="<?php echo e(route('manager.bookings.show', $booking)); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Booking
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <form action="<?php echo e(route('manager.bookings.update', $booking)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guest Selection -->
                            <div class="md:col-span-2">
                                <label for="user_id" class="block text-green-200 text-sm font-medium mb-2">Guest</label>
                                <select name="user_id" id="user_id" required 
                                        class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <?php $__currentLoopData = $guests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($guest->id); ?>" <?php echo e($booking->user_id == $guest->id ? 'selected' : ''); ?>>
                                        <?php echo e($guest->name); ?> (<?php echo e($guest->email); ?>)
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

                            <!-- Room Selection -->
                            <div class="md:col-span-2">
                                <label for="room_id" class="block text-green-200 text-sm font-medium mb-2">Room</label>
                                <select name="room_id" id="room_id" required 
                                        class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($room->id); ?>" <?php echo e($booking->room_id == $room->id ? 'selected' : ''); ?>>
                                        <?php echo e($room->name); ?> - <?php echo e($room->type); ?> (₱<?php echo e(number_format($room->price, 2)); ?>/night)
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
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

                            <!-- Check-in Date -->
                            <div>
                                <label for="check_in_date" class="block text-green-200 text-sm font-medium mb-2">Check-in Date</label>
                                <input type="date" name="check_in_date" id="check_in_date" required
                                       value="<?php echo e($booking->check_in_date); ?>"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <?php $__errorArgs = ['check_in_date'];
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

                            <!-- Check-out Date -->
                            <div>
                                <label for="check_out_date" class="block text-green-200 text-sm font-medium mb-2">Check-out Date</label>
                                <input type="date" name="check_out_date" id="check_out_date" required
                                       value="<?php echo e($booking->check_out_date); ?>"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <?php $__errorArgs = ['check_out_date'];
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

                            <!-- Number of Guests -->
                            <div>
                                <label for="guests" class="block text-green-200 text-sm font-medium mb-2">Number of Guests</label>
                                <input type="number" name="guests" id="guests" required min="1" max="10"
                                       value="<?php echo e($booking->guests); ?>"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
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

                            <!-- Total Price -->
                            <div>
                                <label for="total_price" class="block text-green-200 text-sm font-medium mb-2">Total Price (₱)</label>
                                <input type="number" name="total_price" id="total_price" required min="0" step="0.01"
                                       value="<?php echo e($booking->total_price); ?>"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <?php $__errorArgs = ['total_price'];
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
                            <div class="md:col-span-2">
                                <label for="special_requests" class="block text-green-200 text-sm font-medium mb-2">Special Requests</label>
                                <textarea name="special_requests" id="special_requests" rows="3"
                                          class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Any special requests or notes..."><?php echo e($booking->special_requests); ?></textarea>
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
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\bookings\edit.blade.php ENDPATH**/ ?>