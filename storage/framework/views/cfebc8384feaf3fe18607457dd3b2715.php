<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-gray-800 rounded-lg shadow-xl p-8">
            <h2 class="text-2xl font-bold text-white mb-6">
                <?php echo e(isset($room) ? 'Edit Room' : 'Add New Room'); ?>

            </h2>
            
            <form action="<?php echo e(isset($room) ? route('manager.rooms.update', $room) : route('manager.rooms.store')); ?>" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-6">
                <?php echo csrf_field(); ?>
                <?php if(isset($room)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>

                <!-- Room Number -->
                <div>
                    <label for="number" class="block text-sm font-medium text-white mb-2">Room Number</label>
                    <input type="text" 
                           id="number" 
                           name="number" 
                           value="<?php echo e(old('number', $room->number ?? '')); ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                           required>
                    <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Room Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">Room Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?php echo e(old('name', $room->name ?? '')); ?>"
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                           required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Room Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-white mb-2">Room Type</label>
                    <select id="type" 
                            name="type" 
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="standard" <?php echo e(old('type', $room->type ?? '') === 'standard' ? 'selected' : ''); ?>>Standard</option>
                        <option value="deluxe" <?php echo e(old('type', $room->type ?? '') === 'deluxe' ? 'selected' : ''); ?>>Deluxe</option>
                        <option value="suite" <?php echo e(old('type', $room->type ?? '') === 'suite' ? 'selected' : ''); ?>>Suite</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-white mb-2">Description</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                              required><?php echo e(old('description', $room->description ?? '')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Price and Capacity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-white mb-2">Price per Night (₱)</label>
                        <input type="number" 
                               id="price" 
                               name="price" 
                               value="<?php echo e(old('price', $room->price ?? '')); ?>"
                               step="0.01"
                               min="0"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               required>
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="capacity" class="block text-sm font-medium text-white mb-2">Capacity (Persons)</label>
                        <input type="number" 
                               id="capacity" 
                               name="capacity" 
                               value="<?php echo e(old('capacity', $room->capacity ?? '')); ?>"
                               min="1"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               required>
                        <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Amenities -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                            $amenities = [
                                'wifi' => 'Wi-Fi',
                                'tv' => 'TV',
                                'air_conditioning' => 'Air Conditioning',
                                'refrigerator' => 'Refrigerator',
                                'minibar' => 'Minibar',
                                'safe' => 'Safe',
                                'balcony' => 'Balcony',
                                'ocean_view' => 'Ocean View',
                                'bathtub' => 'Bathtub'
                            ];
                            $currentAmenities = old('amenities', $room->amenities ?? []);
                        ?>

                        <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" 
                                   name="amenities[]" 
                                   value="<?php echo e($value); ?>"
                                   <?php echo e(in_array($value, $currentAmenities) ? 'checked' : ''); ?>

                                   class="rounded border-gray-600 text-green-600 focus:ring-green-500 bg-gray-700">
                            <span class="ml-2 text-white"><?php echo e($label); ?></span>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php $__errorArgs = ['amenities'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Room Images -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Room Images</label>
                    <input type="file" 
                           name="images[]" 
                           accept="image/*" 
                           multiple
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                    <p class="mt-1 text-sm text-green-300">You can select multiple images. Supported formats: JPEG, PNG, GIF</p>
                    <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Availability -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_available" 
                           name="is_available" 
                           value="1"
                           <?php echo e(old('is_available', $room->is_available ?? true) ? 'checked' : ''); ?>

                           class="rounded border-gray-600 text-green-600 focus:ring-green-500 bg-gray-700">
                    <label for="is_available" class="ml-2 text-white">Make this room available for booking</label>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="<?php echo e(route('manager.rooms.index')); ?>" 
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300">
                        <?php echo e(isset($room) ? 'Update Room' : 'Create Room'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\rooms\form.blade.php ENDPATH**/ ?>