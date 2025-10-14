<?php $__env->startSection('title', 'Payment Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="<?php echo e(route('admin.payments.index')); ?>" 
                                   class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-green-400">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Payment Management
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-600 mx-2"></i>
                                    <span class="text-sm font-medium text-gray-300"><?php echo e($payment->payment_reference); ?></span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-green-100">Payment Details</h1>
                    <p class="text-gray-400 mt-1">View and manage payment information</p>
                </div>
                
                <div class="flex space-x-3">
                    <?php if($payment->canBeRefunded()): ?>
                        <button onclick="showRefundModal(<?php echo e($payment->id); ?>, <?php echo e($payment->getRemainingRefundableAmount()); ?>)"
                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i>Process Refund
                        </button>
                    <?php endif; ?>
                    <a href="<?php echo e(route('admin.payments.index')); ?>" 
                       class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment Information Card -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-green-100">Payment Information</h3>
                            <?php
                                $statusColors = [
                                    'completed' => 'bg-green-600 text-green-100',
                                    'pending' => 'bg-yellow-600 text-yellow-100',
                                    'processing' => 'bg-blue-600 text-blue-100',
                                    'failed' => 'bg-red-600 text-red-100',
                                    'refunded' => 'bg-red-600 text-red-100',
                                    'partially_refunded' => 'bg-yellow-600 text-yellow-100'
                                ];
                            ?>
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusColors[$payment->status] ?? 'bg-gray-600 text-gray-100'); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $payment->status))); ?>

                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Reference</label>
                                <div class="bg-gray-900 px-3 py-2 rounded border border-gray-600">
                                    <code class="text-green-400 font-mono"><?php echo e($payment->payment_reference); ?></code>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Date</label>
                                <div class="text-gray-300">
                                    <?php echo e($payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not processed yet'); ?>

                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Amount</label>
                                <div class="text-3xl font-bold text-green-400">
                                    ₱<?php echo e(number_format($payment->calculated_amount ?? $payment->amount, 2)); ?>

                                </div>
                                
                                <!-- Amount breakdown -->
                                <?php if($payment->serviceRequest && $payment->serviceRequest->service): ?>
                                    <?php
                                        $service = $payment->serviceRequest->service;
                                        $quantity = $payment->serviceRequest->quantity ?? 1;
                                    ?>
                                    <div class="text-sm text-gray-400 mt-1">
                                        Service: <?php echo e($service->name); ?>

                                        <?php if($quantity > 1): ?>
                                            (₱<?php echo e(number_format($service->price, 2)); ?> × <?php echo e($quantity); ?>)
                                        <?php endif; ?>
                                    </div>
                                <?php elseif($payment->booking && $payment->booking->room): ?>
                                    <?php
                                        $checkIn = \Carbon\Carbon::parse($payment->booking->check_in_date);
                                        $checkOut = \Carbon\Carbon::parse($payment->booking->check_out_date);
                                        $nights = $checkIn->diffInDays($checkOut);
                                    ?>
                                    <div class="text-sm text-gray-400 mt-1">
                                        Room: <?php echo e($payment->booking->room->name); ?> (<?php echo e($nights); ?> nights)
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Payment Method</label>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-700 text-gray-300">
                                    <?php echo e($payment->payment_method_display ?? ucfirst(str_replace('_', ' ', $payment->payment_method))); ?>

                                </span>
                            </div>
                            
                            <?php if($payment->transaction_id): ?>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Transaction ID</label>
                                    <div class="bg-gray-900 px-3 py-2 rounded border border-gray-600">
                                        <code class="text-green-400 font-mono"><?php echo e($payment->transaction_id); ?></code>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Refund Information -->
                        <?php if($payment->refund_amount > 0): ?>
                            <div class="mt-6 p-4 bg-yellow-900/30 border border-yellow-600/30 rounded-lg">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                                    <h4 class="text-lg font-semibold text-yellow-100">Refund Information</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-400">Refund Amount:</span>
                                        <div class="text-lg font-semibold text-red-400">₱<?php echo e(number_format($payment->refund_amount, 2)); ?></div>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-400">Refunded Date:</span>
                                        <div class="text-gray-300"><?php echo e($payment->refunded_at ? $payment->refunded_at->format('M d, Y') : 'N/A'); ?></div>
                                    </div>
                                </div>
                                <?php if($payment->refundedBy): ?>
                                    <div class="mt-3">
                                        <span class="text-sm text-gray-400">Processed by:</span>
                                        <span class="text-gray-300"><?php echo e($payment->refundedBy->name); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if($payment->refund_reason): ?>
                                    <div class="mt-3">
                                        <span class="text-sm text-gray-400">Reason:</span>
                                        <div class="text-gray-300 mt-1"><?php echo e($payment->refund_reason); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($payment->notes): ?>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-400 mb-2">Notes</label>
                                <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                    <div class="text-gray-300"><?php echo nl2br(e($payment->notes)); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Booking Information -->
                <?php if($payment->booking): ?>
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Related Booking</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Booking Reference</label>
                                    <a href="<?php echo e(route('admin.bookings.show', $payment->booking)); ?>" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        <?php echo e($payment->booking->booking_reference); ?>

                                    </a>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Room</label>
                                    <div class="text-gray-300">
                                        <?php if($payment->booking->room): ?>
                                            <?php echo e($payment->booking->room->name); ?> (<?php echo e($payment->booking->room->room_number); ?>)
                                        <?php else: ?>
                                            Not assigned
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Check-in Date</label>
                                    <div class="text-gray-300"><?php echo e(\Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d, Y')); ?></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Check-out Date</label>
                                    <div class="text-gray-300"><?php echo e(\Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y')); ?></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Total Amount</label>
                                    <div class="text-xl font-semibold text-green-400">₱<?php echo e(number_format($payment->booking->total_amount, 2)); ?></div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Booking Status</label>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo e($payment->booking->status === 'confirmed' ? 'bg-green-600 text-green-100' : 'bg-yellow-600 text-yellow-100'); ?>">
                                        <?php echo e(ucfirst($payment->booking->status)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Related Service Request -->
                <?php if($payment->serviceRequest): ?>
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                            <h3 class="text-lg font-semibold text-green-100">Related Service Request</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Service Request ID</label>
                                    <a href="<?php echo e(route('admin.service-requests.show', $payment->serviceRequest)); ?>" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        #<?php echo e($payment->serviceRequest->id); ?>

                                    </a>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-blue-100">
                                        <?php echo e(ucfirst($payment->serviceRequest->status)); ?>

                                    </span>
                                </div>
                                
                                <?php if($payment->serviceRequest->service): ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Name</label>
                                        <div class="text-gray-300"><?php echo e($payment->serviceRequest->service->name); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Category</label>
                                        <div class="text-gray-300"><?php echo e($payment->serviceRequest->service->category ?? 'N/A'); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Service Price</label>
                                        <div class="text-xl font-semibold text-green-400">₱<?php echo e(number_format($payment->serviceRequest->service->price, 2)); ?></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Quantity</label>
                                        <div class="text-gray-300"><?php echo e($payment->serviceRequest->quantity ?? 1); ?></div>
                                    </div>
                                    
                                    <?php if($payment->serviceRequest->service->duration): ?>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Duration</label>
                                            <div class="text-gray-300"><?php echo e($payment->serviceRequest->service->duration); ?> minutes</div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($payment->serviceRequest->scheduled_date): ?>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Scheduled Date</label>
                                        <div class="text-gray-300"><?php echo e(\Carbon\Carbon::parse($payment->serviceRequest->scheduled_date)->format('M d, Y h:i A')); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($payment->serviceRequest->description): ?>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Description</label>
                                    <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                        <div class="text-gray-300"><?php echo e($payment->serviceRequest->description); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($payment->serviceRequest->special_requests): ?>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Special Requests</label>
                                    <div class="bg-gray-900 p-3 rounded border border-gray-600">
                                        <div class="text-gray-300"><?php echo e($payment->serviceRequest->special_requests); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Customer Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user text-2xl text-white"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-green-100"><?php echo e($payment->user->name); ?></h4>
                            <p class="text-gray-400"><?php echo e($payment->user->email); ?></p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">User Role</label>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-blue-100">
                                    <?php echo e(ucfirst($payment->user->role)); ?>

                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Member Since</label>
                                <div class="text-gray-300"><?php echo e($payment->user->created_at->format('M d, Y')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <?php if($payment->canBeRefunded()): ?>
                            <button onclick="showRefundModal(<?php echo e($payment->id); ?>, <?php echo e($payment->getRemainingRefundableAmount()); ?>)"
                                    class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                                <i class="fas fa-undo mr-2"></i>Process Refund
                            </button>
                        <?php endif; ?>

                        <?php if($payment->status === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('admin.payments.status', $payment)); ?>" class="w-full">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" 
                                        onclick="return confirm('Mark this payment as completed?')"
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <i class="fas fa-check mr-2"></i>Mark Completed
                                </button>
                            </form>

                            <form method="POST" action="<?php echo e(route('admin.payments.status', $payment)); ?>" class="w-full">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <input type="hidden" name="status" value="failed">
                                <button type="submit" 
                                        onclick="return confirm('Mark this payment as failed?')"
                                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>Mark Failed
                                </button>
                            </form>
                        <?php endif; ?>

                        <a href="<?php echo e(route('admin.payments.index')); ?>" 
                           class="w-full bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200 inline-flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                        </a>
                    </div>
                </div>

                <!-- Payment Timeline -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-green-100">Payment Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-plus text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-green-100">Payment Created</div>
                                    <div class="text-xs text-gray-400"><?php echo e($payment->created_at->format('M d, Y h:i A')); ?></div>
                                </div>
                            </div>

                            <?php if($payment->payment_date): ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-green-100">Payment Processed</div>
                                        <div class="text-xs text-gray-400"><?php echo e($payment->payment_date->format('M d, Y h:i A')); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if($payment->refunded_at): ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-undo text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-green-100">Refund Processed</div>
                                        <div class="text-xs text-gray-400"><?php echo e($payment->refunded_at->format('M d, Y h:i A')); ?></div>
                                        <?php if($payment->refundedBy): ?>
                                            <div class="text-xs text-gray-500">By: <?php echo e($payment->refundedBy->name); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg border border-gray-700 w-full max-w-md">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-green-100">Process Refund</h3>
                    <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form id="refundForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Refund Amount</label>
                        <input type="number" name="refund_amount" step="0.01" required
                               class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">
                            Maximum refundable: <span id="maxRefund"></span>
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Refund Reason</label>
                        <textarea name="refund_reason" rows="3" required
                                  placeholder="Please provide a reason for the refund..."
                                  class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500"></textarea>
                    </div>
                    
                    <div class="p-3 bg-yellow-900/30 border border-yellow-600/30 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <div class="text-sm text-yellow-100">
                                <strong>Warning:</strong> This action cannot be undone. Please ensure the refund amount and reason are correct.
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-750 px-6 py-4 border-t border-gray-700 flex justify-end space-x-3">
                    <button type="button" onclick="closeRefundModal()"
                            class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Show refund modal function
function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('maxRefund').textContent = `₱${maxAmount.toFixed(2)}`;
    document.querySelector('input[name="refund_amount"]').max = maxAmount;
    document.getElementById('refundModal').classList.remove('hidden');
}

// Close refund modal function
function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundForm').reset();
}

// Close modal when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/admin/payments/show.blade.php ENDPATH**/ ?>