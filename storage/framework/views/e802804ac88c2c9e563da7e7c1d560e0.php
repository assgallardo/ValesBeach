<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-white">Edit Facility</h2>
            <a href="<?php echo e(route('admin.rooms.index')); ?>" 
               class="text-gray-300 hover:text-white">
                Back to Facilities
            </a>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.rooms.update', $room)); ?>" method="POST" class="bg-gray-800 rounded-lg shadow-xl p-6" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <?php if($errors->any()): ?>
                <div class="bg-red-500/10 text-red-400 p-4 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-300 mb-2">Facility Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?php echo e(old('name', $room->name)); ?>"
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                       required>
            </div>

            <!-- Key Number -->
            <div class="mb-6">
                <label for="key_number" class="block text-gray-300 mb-2">Key Number (Optional)</label>
                <input type="text" 
                       name="key_number" 
                       id="key_number" 
                       value="<?php echo e(old('key_number', $room->key_number)); ?>"
                       placeholder="e.g., 001, 002, A-101"
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                       maxlength="20">
                <p class="text-gray-400 text-sm mt-1">Assign a key number for room identification (e.g., Key 001, Key 002)</p>
            </div>

            <div x-data="{ selectedCategory: '<?php echo e(old('category', $room->category ?? 'Rooms')); ?>' }">
                <!-- Category (Main Type) -->
                <div class="mb-6">
                    <label for="category" class="block text-gray-300 mb-2">Type</label>
                    <select name="category" 
                            id="category" 
                            x-model="selectedCategory"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="Rooms">Rooms</option>
                        <option value="Cottages">Cottages</option>
                        <option value="Event and Dining">Event and Dining</option>
                    </select>
                </div>

                <!-- Specific Type -->
                <div class="mb-6">
                    <label for="type" class="block text-gray-300 mb-2">Specific Type/Name</label>
                    <input type="text" 
                           name="type" 
                           id="type" 
                           value="<?php echo e(old('type', $room->type)); ?>"
                           placeholder="e.g., Umbrella Cottage, Executive Room, Function Hall"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           required>
                    <p class="text-gray-400 text-sm mt-1">Examples: Umbrella Cottage, Bahay Kubo, Standard Room, Beer Garden, Dining Hall</p>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-gray-300 mb-2">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                              required><?php echo e(old('description', $room->description)); ?></textarea>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    <label for="price" class="block text-gray-300 mb-2">Price per Night (₱)</label>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           value="<?php echo e(old('price', $room->price)); ?>"
                           min="0"
                           step="0.01"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                           required>
                </div>

                <!-- Capacity & Beds -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="capacity" class="block text-gray-300 mb-2">Capacity (Guests)</label>
                        <input type="number" 
                               name="capacity" 
                               id="capacity" 
                               value="<?php echo e(old('capacity', $room->capacity)); ?>"
                               min="1"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                               required>
                    </div>
                    <div>
                        <label for="beds" class="block text-gray-300 mb-2">Number of Beds</label>
                        <input type="number" 
                               name="beds" 
                               id="beds" 
                               value="<?php echo e(old('beds', $room->beds)); ?>"
                               min="0"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                               required>
                    </div>
                </div>

                <!-- Optional Check-in/Check-out Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="check_in_time" class="block text-gray-300 mb-2">Check-in Time (optional)</label>
                        <input type="time"
                               name="check_in_time"
                               id="check_in_time"
                               value="<?php echo e(old('check_in_time', $room->check_in_time ?? '')); ?>"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="check_out_time" class="block text-gray-300 mb-2">Check-out Time (optional)</label>
                        <input type="time"
                               name="check_out_time"
                               id="check_out_time"
                               value="<?php echo e(old('check_out_time', $room->check_out_time ?? '')); ?>"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <!-- Amenities (Dynamic based on Category) -->
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Amenities</label>
                    
                    <!-- Rooms Amenities -->
                    <div x-show="selectedCategory === 'Rooms'" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php $__currentLoopData = ['WiFi', 'TV', 'Air Conditioning', 'Mini Bar', 'Refrigerator', 'Safe', 'Balcony', 'Ocean View', 'Room Service']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="<?php echo e($amenity); ?>"
                                       class="form-checkbox text-green-500 rounded"
                                       <?php echo e(in_array($amenity, old('amenities', is_array($room->amenities) ? $room->amenities : json_decode($room->amenities, true) ?? [])) ? 'checked' : ''); ?>>
                                <span class="ml-2"><?php echo e($amenity); ?></span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                    <!-- Cottages Amenities -->
                    <div x-show="selectedCategory === 'Cottages'" class="grid grid-cols-2 md:grid-cols-3 gap-4" style="display: none;">
                    <?php $__currentLoopData = ['Traditional Filipino cottage', '20 pax capacity', 'Bamboo construction', 'Thatched roof', 'Natural ventilation', 'Tables and benches', 'Near beach access', 'Shaded area', 'Power outlet']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="<?php echo e($amenity); ?>"
                                       class="form-checkbox text-green-500 rounded"
                                       <?php echo e(in_array($amenity, old('amenities', is_array($room->amenities) ? $room->amenities : json_decode($room->amenities, true) ?? [])) ? 'checked' : ''); ?>>
                                <span class="ml-2"><?php echo e($amenity); ?></span>
                            </label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                    <!-- Event and Dining Amenities -->
                    <div x-show="selectedCategory === 'Event and Dining'" class="grid grid-cols-2 md:grid-cols-3 gap-4" style="display: none;">
                    <?php $__currentLoopData = ['Air Conditioning', 'Sound System', 'Stage/Platform', 'Dining Tables', 'Chairs', 'Kitchen Access', 'Parking Space', 'Restrooms', 'WiFi', 'LED Lighting', 'Audio Visual Equipment', 'Catering Service']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="<?php echo e($amenity); ?>"
                                       class="form-checkbox text-green-500 rounded"
                                       <?php echo e(in_array($amenity, old('amenities', is_array($room->amenities) ? $room->amenities : json_decode($room->amenities, true) ?? [])) ? 'checked' : ''); ?>>
                                <span class="ml-2"><?php echo e($amenity); ?></span>
                            </label>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Availability -->
                <div class="mb-6">
                    <label class="inline-flex items-center text-gray-300">
                        <input type="checkbox" 
                               name="is_available" 
                               value="1"
                               <?php echo e(old('is_available', $room->is_available) ? 'checked' : ''); ?>

                               class="form-checkbox text-green-500 rounded">
                        <span class="ml-2">Facility is Available</span>
                    </label>
                </div>
            </div>

            <!-- Images -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Current Facility Images</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <?php $__currentLoopData = $room->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative group">
                        <img src="<?php echo e(asset('storage/' . $image->image_path)); ?>" 
                             alt="Room image" 
                             class="w-full h-32 object-cover rounded-lg">
                        <?php if($image->is_featured): ?>
                            <span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-sm">
                                Featured
                            </span>
                        <?php endif; ?>
                        <button type="button"
                                onclick="deleteImage(<?php echo e($room->id); ?>, <?php echo e($image->id); ?>)"
                                class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <label class="block text-gray-300 mb-2">Add New Images (Max <?php echo e(10 - $room->images->count()); ?> remaining)</label>
                <div class="mt-2" id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                <input type="file" 
                       name="room_images[]" 
                       id="roomImages"
                       accept="image/*" 
                       multiple
                       <?php echo e($room->images->count() >= 10 ? 'disabled' : ''); ?>

                       class="mt-1 block w-full text-gray-300
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-green-600 file:text-white
                              hover:file:bg-green-700"
                       onchange="previewImages(this, <?php echo e(10 - $room->images->count()); ?>)">
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" 
                      class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                  Update Facility
              </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function deleteImage(roomId, imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/rooms/${roomId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function previewImages(input, maxRemaining) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files.length > maxRemaining) {
        alert(`You can only upload up to ${maxRemaining} more images`);
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\admin\rooms\edit.blade.php ENDPATH**/ ?>