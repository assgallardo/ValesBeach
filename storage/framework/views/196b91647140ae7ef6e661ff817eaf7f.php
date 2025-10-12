<?php $__env->startSection('title', 'Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-green-50">Invoices</h1>
            <p class="text-gray-400 mt-2">View and manage your invoices</p>
        </div>

        <?php if($invoices->isEmpty()): ?>
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-file-invoice text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Invoices Yet</h3>
                <p class="text-gray-400 mb-6">You don't have any invoices yet. Invoices are automatically generated for your bookings.</p>
                <a 
                    href="<?php echo e(route('guest.rooms.browse')); ?>" 
                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-search mr-2"></i>
                    Browse Rooms
                </a>
            </div>
        <?php else: ?>
            <!-- Invoices List -->
            <div class="space-y-6">
                <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Invoice Info -->
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <h3 class="text-xl font-semibold text-green-50 mr-4">
                                    <?php echo e($invoice->invoice_number); ?>

                                </h3>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo e($invoice->status_badge_class); ?>">
                                    <?php echo e(ucfirst($invoice->status)); ?>

                                </span>
                                <?php if($invoice->isOverdue()): ?>
                                    <span class="ml-2 inline-block px-2 py-1 rounded text-xs bg-red-600 text-white">
                                        Overdue
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm mb-4">
                                <div>
                                    <span class="text-gray-400 block">Amount</span>
                                    <span class="text-green-400 font-bold text-lg"><?php echo e($invoice->formatted_total_amount); ?></span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Issue Date</span>
                                    <span class="text-green-50"><?php echo e($invoice->issue_date->format('M d, Y')); ?></span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Due Date</span>
                                    <span class="text-green-50 <?php echo e($invoice->isOverdue() ? 'text-red-400' : ''); ?>">
                                        <?php echo e($invoice->due_date->format('M d, Y')); ?>

                                    </span>
                                </div>
                                
                                <?php if($invoice->paid_date): ?>
                                <div>
                                    <span class="text-gray-400 block">Paid Date</span>
                                    <span class="text-green-400"><?php echo e($invoice->paid_date->format('M d, Y')); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Booking Info -->
                            <div class="pt-3 border-t border-gray-600">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-bed text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Room:</span>
                                    <span class="text-green-50 font-medium mr-4"><?php echo e($invoice->booking->room->name); ?></span>
                                    
                                    <i class="fas fa-calendar text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Stay:</span>
                                    <span class="text-green-50">
                                        <?php echo e($invoice->booking->check_in->format('M d')); ?> - 
                                        <?php echo e($invoice->booking->check_out->format('M d, Y')); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3 mt-4 lg:mt-0">
                            <a 
                                href="<?php echo e(route('invoices.show', $invoice)); ?>" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                View Invoice
                            </a>
                            
                            <a 
                                href="<?php echo e(route('invoices.download', $invoice)); ?>" 
                                target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </a>
                            
                            <?php if(!$invoice->isPaid() && $invoice->booking->remaining_balance > 0): ?>
                            <a 
                                href="<?php echo e(route('payments.create', $invoice->booking)); ?>" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors"
                            >
                                <i class="fas fa-credit-card mr-2"></i>
                                Pay Now
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <?php if($invoices->hasPages()): ?>
            <div class="mt-8">
                <?php echo e($invoices->links()); ?>

            </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Invoice Summary Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice text-green-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Invoiced</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱<?php echo e(number_format($invoices->sum('total_amount'), 2)); ?>

                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Paid</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱<?php echo e(number_format($invoices->where('status', 'paid')->sum('total_amount'), 2)); ?>

                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Pending</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱<?php echo e(number_format($invoices->whereIn('status', ['draft', 'sent'])->sum('total_amount'), 2)); ?>

                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Overdue</p>
                        <p class="text-lg font-semibold text-green-50">
                            <?php echo e($invoices->filter(function($invoice) { return $invoice->isOverdue(); })->count()); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-8 text-center">
            <a 
                href="<?php echo e(route('guest.dashboard')); ?>" 
                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/invoices/index.blade.php ENDPATH**/ ?>