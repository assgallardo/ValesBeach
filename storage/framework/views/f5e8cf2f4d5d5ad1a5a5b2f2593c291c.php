<?php $__env->startSection('title', 'Service Request Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Service Request Details</h1>
                    <p class="text-gray-400">Request #<?php echo e($serviceRequest->id); ?></p>
                </div>
                <a href="<?php echo e(route('guest.services.history')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Service Request Card -->
        <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden border border-gray-700">
            <!-- Status Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-1"><?php echo e($serviceRequest->service->name ?? 'Service Request'); ?></h2>
                        <p class="text-green-100 text-sm"><?php echo e($serviceRequest->service->category ?? 'Service'); ?></p>
                    </div>
                    <div>
                        <?php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-yellow-500', 'icon' => 'clock', 'text' => 'Pending'],
                                'confirmed' => ['bg' => 'bg-blue-500', 'icon' => 'check', 'text' => 'Confirmed'],
                                'in_progress' => ['bg' => 'bg-purple-500', 'icon' => 'spinner', 'text' => 'In Progress'],
                                'completed' => ['bg' => 'bg-green-500', 'icon' => 'check-circle', 'text' => 'Completed'],
                                'cancelled' => ['bg' => 'bg-red-500', 'icon' => 'times-circle', 'text' => 'Cancelled'],
                            ];
                            $config = $statusConfig[$serviceRequest->status] ?? ['bg' => 'bg-gray-500', 'icon' => 'question', 'text' => ucfirst($serviceRequest->status)];
                        ?>
                        <span class="inline-flex items-center px-4 py-2 <?php echo e($config['bg']); ?> text-white rounded-lg font-medium text-sm">
                            <i class="fas fa-<?php echo e($config['icon']); ?> mr-2"></i>
                            <?php echo e($config['text']); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <!-- Service Information -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-concierge-bell text-green-400 mr-2"></i>
                            Service Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-400">Service Name</p>
                                <p class="text-white font-medium"><?php echo e($serviceRequest->service->name ?? 'N/A'); ?></p>
                            </div>
                            <?php if($serviceRequest->scheduled_date): ?>
                            <div>
                                <p class="text-sm text-gray-400">Scheduled Date & Time</p>
                                <p class="text-white font-medium">
                                    <i class="fas fa-calendar-alt text-green-400 mr-2"></i>
                                    <?php echo e(\Carbon\Carbon::parse($serviceRequest->scheduled_date)->format('M d, Y g:i A')); ?>

                                </p>
                            </div>
                            <?php endif; ?>
                            <?php if($serviceRequest->guests_count): ?>
                            <div>
                                <p class="text-sm text-gray-400">Number of Guests</p>
                                <p class="text-white font-medium">
                                    <i class="fas fa-users text-green-400 mr-2"></i>
                                    <?php echo e($serviceRequest->guests_count); ?> guest(s)
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Guest Information -->
                    <div class="bg-gray-700/50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-user text-blue-400 mr-2"></i>
                            Guest Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-400">Name</p>
                                <p class="text-white font-medium"><?php echo e($serviceRequest->guest_name ?? auth()->user()->name); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Email</p>
                                <p class="text-white font-medium"><?php echo e($serviceRequest->guest_email ?? auth()->user()->email); ?></p>
                            </div>
                            <?php if($serviceRequest->room_number): ?>
                            <div>
                                <p class="text-sm text-gray-400">Room Number</p>
                                <p class="text-white font-medium"><?php echo e($serviceRequest->room_number); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Special Requests / Notes -->
                <?php if($serviceRequest->special_requests || $serviceRequest->manager_notes || $serviceRequest->description): ?>
                <div class="bg-gray-700/50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-400 mr-2"></i>
                        Additional Information
                    </h3>
                    <?php if($serviceRequest->description): ?>
                    <div class="mb-3">
                        <p class="text-sm text-gray-400 mb-1">Description</p>
                        <p class="text-white"><?php echo e($serviceRequest->description); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($serviceRequest->special_requests): ?>
                    <div class="mb-3">
                        <p class="text-sm text-gray-400 mb-1">Special Requests</p>
                        <p class="text-white"><?php echo e($serviceRequest->special_requests); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($serviceRequest->manager_notes): ?>
                    <div>
                        <p class="text-sm text-gray-400 mb-1">Manager Notes</p>
                        <p class="text-white"><?php echo e($serviceRequest->manager_notes); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Payment Information -->
                <?php if($serviceRequest->payment): ?>
                <div class="bg-gray-700/50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fas fa-money-bill-wave text-green-400 mr-2"></i>
                        Payment Information
                    </h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-400">Amount</p>
                            <p class="text-2xl font-bold text-green-400">₱<?php echo e(number_format($serviceRequest->payment->amount, 2)); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Payment Method</p>
                            <p class="text-white font-medium"><?php echo e(ucfirst(str_replace('_', ' ', $serviceRequest->payment->payment_method))); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Payment Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                <?php if($serviceRequest->payment->status === 'completed'): ?> bg-green-500 text-white
                                <?php elseif($serviceRequest->payment->status === 'confirmed'): ?> bg-blue-500 text-white
                                <?php elseif($serviceRequest->payment->status === 'pending'): ?> bg-yellow-500 text-gray-900
                                <?php elseif($serviceRequest->payment->status === 'overdue'): ?> bg-orange-500 text-white
                                <?php elseif($serviceRequest->payment->status === 'processing'): ?> bg-indigo-500 text-white
                                <?php elseif($serviceRequest->payment->status === 'failed'): ?> bg-red-600 text-white
                                <?php elseif($serviceRequest->payment->status === 'refunded'): ?> bg-red-500 text-white
                                <?php elseif($serviceRequest->payment->status === 'cancelled'): ?> bg-gray-600 text-white
                                <?php else: ?> bg-gray-500 text-white
                                <?php endif; ?>">
                                <?php echo e(ucfirst($serviceRequest->payment->status)); ?>

                            </span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Timestamps -->
                <div class="mt-6 pt-6 border-t border-gray-700">
                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400">Requested on</p>
                            <p class="text-white"><?php echo e($serviceRequest->created_at->format('M d, Y g:i A')); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-400">Last updated</p>
                            <p class="text-white"><?php echo e($serviceRequest->updated_at->format('M d, Y g:i A')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\guest\services\show-request.blade.php ENDPATH**/ ?>