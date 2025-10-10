

<?php $__env->startSection('content'); ?>
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Edit Service
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Update service information and settings
            </p>
            <div class="mt-6 space-x-4">
                <a href="<?php echo e(route('manager.services.show', $service)); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Service
                </a>
            </div>
        </div>

        <!-- Service Edit Form -->
        <div class="max-w-4xl mx-auto">
            <form action="<?php echo e(route('manager.services.update', $service)); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <!-- Basic Information Section -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6">
                    <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-green-400"></i>
                        Basic Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-green-200 text-sm font-medium mb-2">
                                Service Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo e(old('name', $service->name)); ?>"
                                   required
                                   class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['name'];
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

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-green-200 text-sm font-medium mb-2">
                                Category <span class="text-red-400">*</span>
                            </label>
                            <select id="category" 
                                    name="category" 
                                    required
                                    class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <option value="spa" <?php echo e(old('category', $service->category) === 'spa' ? 'selected' : ''); ?>>Spa & Wellness</option>
                                <option value="dining" <?php echo e(old('category', $service->category) === 'dining' ? 'selected' : ''); ?>>Dining</option>
                                <option value="transportation" <?php echo e(old('category', $service->category) === 'transportation' ? 'selected' : ''); ?>>Transportation</option>
                                <option value="activities" <?php echo e(old('category', $service->category) === 'activities' ? 'selected' : ''); ?>>Activities</option>
                                <option value="room_service" <?php echo e(old('category', $service->category) === 'room_service' ? 'selected' : ''); ?>>Room Service</option>
                            </select>
                            <?php $__errorArgs = ['category'];
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

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-green-200 text-sm font-medium mb-2">
                                Price (₱) <span class="text-red-400">*</span>
                            </label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="<?php echo e(old('price', $service->price)); ?>"
                                   step="0.01"
                                   min="0"
                                   required
                                   class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['price'];
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

                        <!-- Duration -->
                        <div>
                            <label for="duration" class="block text-green-200 text-sm font-medium mb-2">
                                Duration (minutes)
                            </label>
                            <input type="number" 
                                   id="duration" 
                                   name="duration" 
                                   value="<?php echo e(old('duration', $service->duration)); ?>"
                                   min="0"
                                   class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['duration'];
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

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-green-200 text-sm font-medium mb-2">
                                Maximum Capacity
                            </label>
                            <input type="number" 
                                   id="capacity" 
                                   name="capacity" 
                                   value="<?php echo e(old('capacity', $service->capacity)); ?>"
                                   min="1"
                                   class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <?php $__errorArgs = ['capacity'];
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

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-green-200 text-sm font-medium mb-2">
                                Description <span class="text-red-400">*</span>
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      required
                                      class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent"><?php echo e(old('description', $service->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
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
                </div>

                <!-- Service Image Section -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6">
                    <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                        <i class="fas fa-image mr-2 text-green-400"></i>
                        Service Image
                    </h3>

                    <!-- Current Image -->
                    <?php if($service->image): ?>
                    <div class="mb-6">
                        <label class="block text-green-200 text-sm font-medium mb-2">Current Image</label>
                        <div class="relative w-full h-48 bg-green-800/50 rounded-lg overflow-hidden">
                            <img src="<?php echo e(asset('storage/' . $service->image)); ?>" 
                                 alt="<?php echo e($service->name); ?>" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Image Upload -->
                    <div>
                        <label for="image" class="block text-green-200 text-sm font-medium mb-2">
                            <?php echo e($service->image ? 'Replace Image' : 'Upload Service Image'); ?>

                        </label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700">
                        <?php $__errorArgs = ['image'];
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

                <!-- Service Status Section -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6">
                    <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                        <i class="fas fa-cog mr-2 text-green-400"></i>
                        Service Settings
                    </h3>

                    <div class="flex items-center">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" 
                               id="is_available" 
                               name="is_available" 
                               value="1"
                               <?php echo e(old('is_available', $service->is_available) ? 'checked' : ''); ?>

                               class="w-4 h-4 text-green-600 bg-green-800/50 border-green-600/50 rounded focus:ring-green-500 focus:ring-2">
                        <label for="is_available" class="ml-3 text-green-200 text-sm font-medium">
                            Service is available for booking
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="<?php echo e(route('manager.services.show', $service)); ?>" 
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/services/edit.blade.php ENDPATH**/ ?>