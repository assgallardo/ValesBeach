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
                Service Request Details
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Request #<?php echo e($serviceRequest->id); ?>

            </p>
            <div class="mt-6">
                <a href="<?php echo e(route('manager.service-requests.index')); ?>" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to All Requests
                </a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
        <div class="bg-green-600 text-white px-6 py-4 rounded-lg mb-6 shadow-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Card -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-green-50">Request Status</h3>
                        <?php
                            $statusColors = [
                                'pending' => 'bg-blue-600 text-blue-100',
                                'assigned' => 'bg-purple-600 text-purple-100',
                                'in_progress' => 'bg-yellow-600 text-yellow-100',
                                'completed' => 'bg-green-600 text-green-100',
                                'cancelled' => 'bg-red-600 text-red-100'
                            ];
                        ?>
                        <span class="px-4 py-2 rounded-full text-sm font-medium <?php echo e($statusColors[$serviceRequest->status] ?? 'bg-gray-600 text-gray-100'); ?>">
                            <?php echo e(ucfirst($serviceRequest->status)); ?>

                        </span>
                    </div>

                    <!-- Timeline -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-green-50 font-medium">Request Created</p>
                                <p class="text-green-200 text-sm"><?php echo e($serviceRequest->requested_at ? $serviceRequest->requested_at->format('M d, Y H:i') : 'N/A'); ?></p>
                            </div>
                        </div>

                        <?php if($serviceRequest->assigned_at): ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-green-50 font-medium">Assigned to Staff</p>
                                <p class="text-green-200 text-sm"><?php echo e($serviceRequest->assigned_at->format('M d, Y H:i')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($serviceRequest->scheduled_at): ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-green-50 font-medium">Scheduled</p>
                                <p class="text-green-200 text-sm"><?php echo e($serviceRequest->scheduled_at->format('M d, Y H:i')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($serviceRequest->completed_at): ?>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-green-50 font-medium">Completed</p>
                                <p class="text-green-200 text-sm"><?php echo e($serviceRequest->completed_at->format('M d, Y H:i')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Service Details -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Service Details</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            <div>
                                <p class="text-green-200 text-sm">Service Name</p>
                                <p class="text-green-50 font-medium text-lg"><?php echo e($serviceRequest->service ? $serviceRequest->service->name : 'N/A'); ?></p>
                            </div>
                        </div>

                        <?php if($serviceRequest->service_type): ?>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <div>
                                <p class="text-green-200 text-sm">Service Type</p>
                                <p class="text-green-50 font-medium"><?php echo e(ucfirst($serviceRequest->service_type)); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($serviceRequest->description): ?>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div>
                                <p class="text-green-200 text-sm">Description</p>
                                <p class="text-green-50"><?php echo e($serviceRequest->description); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($serviceRequest->guests_count): ?>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <div>
                                <p class="text-green-200 text-sm">Number of Guests</p>
                                <p class="text-green-50 font-medium"><?php echo e($serviceRequest->guests_count); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if($serviceRequest->scheduled_date): ?>
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-green-200 text-sm">Scheduled Date</p>
                                <p class="text-green-50 font-medium"><?php echo e(\Carbon\Carbon::parse($serviceRequest->scheduled_date)->format('M d, Y')); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notes -->
                <?php if($serviceRequest->notes || $serviceRequest->manager_notes): ?>
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Notes</h3>
                    
                    <?php if($serviceRequest->notes): ?>
                    <div class="mb-4 bg-green-800/30 rounded-lg p-4">
                        <p class="text-green-200 text-sm mb-2">Staff Notes</p>
                        <p class="text-green-50"><?php echo e($serviceRequest->notes); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if($serviceRequest->manager_notes): ?>
                    <div class="bg-blue-900/30 rounded-lg p-4">
                        <p class="text-blue-200 text-sm mb-2">Manager Notes</p>
                        <p class="text-blue-50"><?php echo e($serviceRequest->manager_notes); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Guest Information -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-xl font-bold text-green-50 mb-4">Guest Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-green-200 text-sm">Name</p>
                            <p class="text-green-50 font-medium"><?php echo e($serviceRequest->guest_name); ?></p>
                        </div>
                        
                        <div>
                            <p class="text-green-200 text-sm">Email</p>
                            <p class="text-green-50"><?php echo e($serviceRequest->guest_email); ?></p>
                        </div>
                        
                        <div>
                            <p class="text-green-200 text-sm">Room Number</p>
                            <p class="text-green-50 font-medium"><?php echo e($serviceRequest->room_number); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Priority -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-xl font-bold text-green-50 mb-4">Priority Level</h3>
                    
                    <?php
                        $priorityColors = [
                            'low' => 'bg-gray-600 text-gray-100',
                            'normal' => 'bg-blue-600 text-blue-100',
                            'high' => 'bg-orange-600 text-orange-100',
                            'urgent' => 'bg-red-600 text-red-100'
                        ];
                    ?>
                    <div class="flex items-center justify-center">
                        <span class="px-4 py-2 rounded-full text-lg font-medium <?php echo e($priorityColors[$serviceRequest->priority] ?? 'bg-gray-600 text-gray-100'); ?>">
                            <?php echo e(ucfirst($serviceRequest->priority)); ?>

                        </span>
                    </div>
                </div>

                <!-- Assigned Staff -->
                <?php if($serviceRequest->assignedStaff): ?>
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-xl font-bold text-green-50 mb-4">Assigned Staff</h3>
                    
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3">
                            <?php echo e(substr($serviceRequest->assignedStaff->name, 0, 1)); ?>

                        </div>
                        <div>
                            <p class="text-green-50 font-medium"><?php echo e($serviceRequest->assignedStaff->name); ?></p>
                            <p class="text-green-200 text-sm"><?php echo e($serviceRequest->assignedStaff->email); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quick Actions -->
                <?php if($serviceRequest->status !== 'completed' && $serviceRequest->status !== 'cancelled'): ?>
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 border border-green-700/30">
                    <h3 class="text-xl font-bold text-green-50 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <button onclick="openQuickUpdateModal('in_progress')"
                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-3 rounded-lg transition-colors duration-200 font-medium">
                            Mark as In Progress
                        </button>
                        
                        <button onclick="openQuickUpdateModal('completed')"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg transition-colors duration-200 font-medium">
                            Mark as Completed
                        </button>
                        
                        <button onclick="openQuickUpdateModal('cancelled')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg transition-colors duration-200 font-medium">
                            Cancel Request
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Quick Update Modal -->
<div id="quickUpdateModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-green-900 rounded-lg p-8 max-w-md w-full mx-4 border border-green-700">
        <h3 class="text-2xl font-bold text-green-50 mb-6">Update Request Status</h3>
        
        <form id="quickUpdateForm" method="POST" action="<?php echo e(route('manager.service-requests.updateStatus', $serviceRequest)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            
            <input type="hidden" name="status" id="quickStatusInput">
            
            <p class="text-green-100 mb-6">Are you sure you want to update the status of this request?</p>
            
            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-green-50 text-sm font-medium mb-2">Add Notes (Optional)</label>
                <textarea name="notes" rows="3" 
                          class="w-full bg-green-800/50 text-green-50 border border-green-700 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-4">
                <button type="button" onclick="closeQuickUpdateModal()"
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function openQuickUpdateModal(status) {
    const modal = document.getElementById('quickUpdateModal');
    const input = document.getElementById('quickStatusInput');
    
    input.value = status;
    modal.style.display = 'flex';
}

function closeQuickUpdateModal() {
    const modal = document.getElementById('quickUpdateModal');
    modal.style.display = 'none';
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeQuickUpdateModal();
    }
});

// Close modal on outside click
document.getElementById('quickUpdateModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeQuickUpdateModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/service-requests/show.blade.php ENDPATH**/ ?>