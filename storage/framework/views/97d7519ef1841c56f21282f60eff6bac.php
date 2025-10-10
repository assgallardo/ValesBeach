<?php $__env->startSection('content'); ?>
<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-green-50">Create New Service</h2>
            <p class="text-green-200 mt-2">Add a new service to your resort offerings</p>
        </div>

        <div class="max-w-2xl mx-auto bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
            <form action="<?php echo e(route('manager.services.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-green-200 mb-2">Service Name</label>
                        <input type="text" id="name" name="name" value="<?php echo e(old('name')); ?>" required
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-green-200 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500"><?php echo e(old('description')); ?></textarea>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-green-200 mb-2">Category</label>
                        <select id="category" name="category" required
                                class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                            <option value="">Select Category</option>
                            <option value="spa" <?php echo e(old('category') === 'spa' ? 'selected' : ''); ?>>Spa</option>
                            <option value="dining" <?php echo e(old('category') === 'dining' ? 'selected' : ''); ?>>Dining</option>
                            <option value="activities" <?php echo e(old('category') === 'activities' ? 'selected' : ''); ?>>Activities</option>
                            <option value="transportation" <?php echo e(old('category') === 'transportation' ? 'selected' : ''); ?>>Transportation</option>
                            <option value="room_service" <?php echo e(old('category') === 'room_service' ? 'selected' : ''); ?>>Room Service</option>
                        </select>
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-green-200 mb-2">Price (₱)</label>
                        <input type="number" id="price" name="price" value="<?php echo e(old('price')); ?>" step="0.01" min="0" required
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-green-200 mb-2">Duration (minutes)</label>
                        <input type="number" id="duration" name="duration" value="<?php echo e(old('duration')); ?>" min="1"
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-green-200 mb-2">Capacity</label>
                        <input type="number" id="capacity" name="capacity" value="<?php echo e(old('capacity')); ?>" min="1"
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-green-200 mb-2">Service Image</label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- Availability -->
                    <div class="flex items-center">
                        <input type="checkbox" id="is_available" name="is_available" value="1" <?php echo e(old('is_available', true) ? 'checked' : ''); ?>

                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-green-300 rounded">
                        <label for="is_available" class="ml-2 block text-sm text-green-200">
                            Service is available for booking
                        </label>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <a href="<?php echo e(route('manager.services.index')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                        Create Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/services/create.blade.php ENDPATH**/ ?>