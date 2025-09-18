<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-white">Add New Room</h2>
            <a href="<?php echo e(route('admin.rooms.index')); ?>" 
               class="text-gray-300 hover:text-white">
                Back to Rooms
            </a>
        </div>

        <!-- Form -->
        <form action="<?php echo e(route('admin.rooms.store')); ?>" method="POST" enctype="multipart/form-data" class="bg-gray-800 rounded-lg shadow-xl p-6">
            <?php echo csrf_field(); ?>
            
            <!-- Display any validation errors -->
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
                <label for="name" class="block text-gray-300 mb-2">Room Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="<?php echo e(old('name')); ?>"
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                       required>
            </div>

            <!-- Type -->
            <div class="mb-6">
                <label for="type" class="block text-gray-300 mb-2">Room Type</label>
                <select name="type" 
                        id="type" 
                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                        required>
                    <option value="">Select Type</option>
                    <option value="Standard">Standard</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Suite">Suite</option>
                    <option value="Family">Family</option>
                </select>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-gray-300 mb-2">Description</label>
                <textarea name="description" 
                          id="description" 
                          rows="4"
                          class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                          required><?php echo e(old('description')); ?></textarea>
            </div>

            <!-- Price -->
            <div class="mb-6">
                <label for="price" class="block text-gray-300 mb-2">Price per Night (₱)</label>
                <input type="number" 
                       name="price" 
                       id="price" 
                       value="<?php echo e(old('price')); ?>"
                       min="0"
                       step="0.01"
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                       required>
            </div>

            <!-- Capacity & Beds -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="capacity" class="block text-gray-300 mb-2">Capacity (Guests)</label>
                    <input type="number" 
                           name="capacity" 
                           id="capacity" 
                           value="<?php echo e(old('capacity', 2)); ?>"
                           min="1"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           required>
                </div>
                <div>
                    <label for="beds" class="block text-gray-300 mb-2">Number of Beds</label>
                    <input type="number" 
                           name="beds" 
                           id="beds" 
                           value="<?php echo e(old('beds', 1)); ?>"
                           min="1"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           required>
                </div>
            </div>

            <!-- Amenities -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Amenities</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <?php $__currentLoopData = ['WiFi', 'TV', 'Air Conditioning', 'Mini Bar', 'Refrigerator', 'Safe', 'Balcony', 'Ocean View', 'Room Service']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="<?php echo e($amenity); ?>"
                                       class="form-checkbox text-green-500 rounded"
                                       <?php echo e(in_array($amenity, old('amenities', [])) ? 'checked' : ''); ?>>
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
                           <?php echo e(old('is_available', true) ? 'checked' : ''); ?>

                           class="form-checkbox text-green-500 rounded">
                    <span class="ml-2">Room is Available</span>
                </label>
            </div>

            <!-- Room Images -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Room Images (Max 10)</label>
                <div class="mt-2" id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                <input type="file" 
                       name="room_images[]" 
                       id="roomImages"
                       accept="image/*" 
                       multiple
                       class="mt-1 block w-full text-gray-300
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-green-600 file:text-white
                              hover:file:bg-green-700"
                       onchange="previewImages(this)">
                <p class="mt-1 text-sm text-gray-400">
                    You can select up to 10 images. First image will be set as featured.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Add Room
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files.length > 10) {
        alert('You can only upload up to 10 images');
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                ${index === 0 ? '<span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-sm">Featured</span>' : ''}
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\valesbeach\resources\views/admin/rooms/create.blade.php ENDPATH**/ ?>