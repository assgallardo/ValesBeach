<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-green-50 mb-2">Edit Service Request</h1>
                <p class="text-green-200">Update service request details and assignment.</p>
            </div>
            <a href="<?php echo e(route('manager.staff-assignment.index')); ?>" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-green-50">Service Request Details</h2>
        </div>

        <form action="<?php echo e(route('manager.staff-assignment.update', $serviceRequest)); ?>" method="POST" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Service Type Dropdown -->
                    <div>
                        <label for="service_type" class="block text-gray-300 mb-2">Service Type</label>
                        <select name="service_type" id="service_type" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">Select Service Type</option>
                            <?php $__currentLoopData = $availableServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($service->name); ?>" 
                                    <?php echo e(old('service_type', $serviceRequest->service_type) === $service->name ? 'selected' : ''); ?>>
                                <?php echo e($service->name); ?> - $<?php echo e($service->price); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['service_type'];
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
                    <div>
                        <label for="description" class="block text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full bg-gray-700 text-green-100 rounded-lg p-3"><?php echo e(old('description', $serviceRequest->description)); ?></textarea>
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

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-gray-300 mb-2">Priority</label>
                        <select name="priority" id="priority" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="low" <?php echo e(old('priority', $serviceRequest->priority) === 'low' ? 'selected' : ''); ?>>Low</option>
                            <option value="medium" <?php echo e(old('priority', $serviceRequest->priority) === 'medium' ? 'selected' : ''); ?>>Medium</option>
                            <option value="high" <?php echo e(old('priority', $serviceRequest->priority) === 'high' ? 'selected' : ''); ?>>High</option>
                            <option value="urgent" <?php echo e(old('priority', $serviceRequest->priority) === 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                        </select>
                        <?php $__errorArgs = ['priority'];
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

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-gray-300 mb-2">Status</label>
                        <select name="status" id="status" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="pending" <?php echo e(old('status', $serviceRequest->status) === 'pending' ? 'selected' : ''); ?>>Pending</option>
                            <option value="confirmed" <?php echo e(old('status', $serviceRequest->status) === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                            <option value="assigned" <?php echo e(old('status', $serviceRequest->status) === 'assigned' ? 'selected' : ''); ?>>Assigned</option>
                            <option value="in_progress" <?php echo e(old('status', $serviceRequest->status) === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                            <option value="completed" <?php echo e(old('status', $serviceRequest->status) === 'completed' ? 'selected' : ''); ?>>Completed</option>
                            <option value="cancelled" <?php echo e(old('status', $serviceRequest->status) === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
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
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Assigned To -->
                    <div>
                        <label for="assigned_to" class="block text-gray-300 mb-2">Assigned To</label>
                        <select name="assigned_to" id="assigned_to" class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">Unassigned</option>
                            <?php $__currentLoopData = $availableStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($staff->id); ?>" 
                                    <?php echo e(old('assigned_to', $serviceRequest->assigned_to) == $staff->id ? 'selected' : ''); ?>>
                                <?php echo e($staff->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['assigned_to'];
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

                    <!-- Guests Count -->
                    <div>
                        <label for="guests_count" class="block text-gray-300 mb-2">Number of Guests</label>
                        <input type="number" name="guests_count" id="guests_count" min="1"
                               value="<?php echo e(old('guests_count', $serviceRequest->guests_count ?? $serviceRequest->guests ?? 1)); ?>"
                               class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
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

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-gray-300 mb-2">Deadline</label>
                        <input type="datetime-local" name="deadline" id="deadline"
                               value="<?php echo e(old('deadline', $serviceRequest->deadline ? $serviceRequest->deadline->format('Y-m-d\TH:i') : '')); ?>"
                               class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                        <?php $__errorArgs = ['deadline'];
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

                    <!-- Estimated Duration -->
                    <div>
                        <label for="estimated_duration" class="block text-gray-300 mb-2">Estimated Duration</label>
                        <select name="estimated_duration" id="estimated_duration" class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">No estimate</option>
                            <option value="15" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 15 ? 'selected' : ''); ?>>15 minutes</option>
                            <option value="30" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 30 ? 'selected' : ''); ?>>30 minutes</option>
                            <option value="60" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 60 ? 'selected' : ''); ?>>1 hour</option>
                            <option value="90" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 90 ? 'selected' : ''); ?>>1.5 hours</option>
                            <option value="120" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 120 ? 'selected' : ''); ?>>2 hours</option>
                            <option value="240" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 240 ? 'selected' : ''); ?>>4 hours</option>
                            <option value="480" <?php echo e(old('estimated_duration', $serviceRequest->estimated_duration) == 480 ? 'selected' : ''); ?>>8 hours</option>
                        </select>
                        <?php $__errorArgs = ['estimated_duration'];
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

            <!-- Full Width Fields -->
            <div class="mt-6">
                <!-- Manager Notes -->
                <div>
                    <label for="manager_notes" class="block text-gray-300 mb-2">Manager Notes</label>
                    <textarea name="manager_notes" id="manager_notes" rows="4"
                              placeholder="Add notes for staff members..."
                              class="w-full bg-gray-700 text-green-100 rounded-lg p-3"><?php echo e(old('manager_notes', $serviceRequest->manager_notes)); ?></textarea>
                    <?php $__errorArgs = ['manager_notes'];
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

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="<?php echo e(route('manager.staff-assignment.index')); ?>" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Service Request
                </button>
            </div>
        </form>
    </div>

    <!-- Request Information -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Guest Information -->
        <div class="bg-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-green-50 mb-4">Guest Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Guest:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->guest->name ?? $serviceRequest->guest_name ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Email:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->guest->email ?? $serviceRequest->guest_email ?? 'N/A'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->room->name ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <!-- Request Timeline -->
        <div class="bg-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-green-50 mb-4">Timeline</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Requested:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->created_at->format('M d, Y H:i')); ?></span>
                </div>
                <?php if($serviceRequest->assigned_at): ?>
                <div class="flex justify-between">
                    <span class="text-gray-400">Assigned:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->assigned_at->format('M d, Y H:i')); ?></span>
                </div>
                <?php endif; ?>
                <?php if($serviceRequest->completed_at): ?>
                <div class="flex justify-between">
                    <span class="text-gray-400">Completed:</span>
                    <span class="text-green-100"><?php echo e($serviceRequest->completed_at->format('M d, Y H:i')); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\staff-assignment\edit.blade.php ENDPATH**/ ?>