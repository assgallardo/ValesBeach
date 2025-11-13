<?php $__env->startSection('title', 'Customer Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Action Buttons -->
        <div class="flex items-center justify-between mb-6">
            <a href="<?php echo e(route('admin.payments.customer', $customer->id)); ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Customer Details
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
        </div>

        <!-- Invoice Container -->
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden" id="invoice">
            <!-- Invoice Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-8 text-white">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">INVOICE</h1>
                        <p class="text-green-100">Vales Beach Resort</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold"><?php echo e($invoice->invoice_number); ?></p>
                        <p class="text-green-100 text-sm"><?php echo e($invoice->created_at->format('M d, Y')); ?></p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2">Bill To:</h3>
                        <p class="text-green-100"><?php echo e($customer->name); ?></p>
                        <p class="text-green-100 text-sm"><?php echo e($customer->email); ?></p>
                        <p class="text-green-100 text-sm">Member since <?php echo e($customer->created_at->format('M d, Y')); ?></p>
                    </div>
                    <div class="text-left md:text-right">
                        <h3 class="font-semibold mb-2">From:</h3>
                        <p class="text-green-100">Vales Beach Resort</p>
                        <p class="text-green-100 text-sm">Hospitality and Leisure Services</p>
                        <p class="text-green-100 text-sm">Beach Resort & Hotel</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="p-8">
                <?php if(count($invoice->items) > 0): ?>
                    <!-- Items Table -->
                    <div class="overflow-x-auto mb-8">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-300">
                                    <th class="text-left py-4 px-2 font-semibold text-gray-700">Type</th>
                                    <th class="text-left py-4 px-2 font-semibold text-gray-700">Description</th>
                                    <th class="text-left py-4 px-2 font-semibold text-gray-700">Details</th>
                                    <th class="text-right py-4 px-2 font-semibold text-gray-700">Amount</th>
                                    <th class="text-right py-4 px-2 font-semibold text-gray-700">Paid</th>
                                    <th class="text-right py-4 px-2 font-semibold text-gray-700">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-4 px-2">
                                        <?php if($item['type'] === 'booking'): ?>
                                            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                                <i class="fas fa-bed mr-1"></i>Booking
                                            </span>
                                        <?php elseif($item['type'] === 'service'): ?>
                                            <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">
                                                <i class="fas fa-concierge-bell mr-1"></i>Service
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs font-medium">
                                                <i class="fas fa-utensils mr-1"></i>Food
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-2">
                                        <div class="font-medium text-gray-900"><?php echo e($item['description']); ?></div>
                                        <div class="text-xs text-gray-500">Ref: <?php echo e($item['reference']); ?></div>
                                    </td>
                                    <td class="py-4 px-2 text-sm text-gray-600">
                                        <?php echo e($item['details']); ?>

                                    </td>
                                    <td class="py-4 px-2 text-right font-medium text-gray-900">
                                        ₱<?php echo e(number_format($item['amount'], 2)); ?>

                                    </td>
                                    <td class="py-4 px-2 text-right text-green-600 font-medium">
                                        ₱<?php echo e(number_format($item['paid'], 2)); ?>

                                    </td>
                                    <td class="py-4 px-2 text-right font-medium <?php echo e($item['balance'] > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        ₱<?php echo e(number_format($item['balance'], 2)); ?>

                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Section -->
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2">
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between py-2 text-gray-700">
                                    <span class="font-medium">Total Amount:</span>
                                    <span class="font-bold">₱<?php echo e(number_format($invoice->total_amount, 2)); ?></span>
                                </div>
                                <div class="flex justify-between py-2 text-green-700 border-t border-gray-300">
                                    <span class="font-medium">Total Paid:</span>
                                    <span class="font-bold">₱<?php echo e(number_format($invoice->total_paid, 2)); ?></span>
                                </div>
                                <div class="flex justify-between py-2 border-t-2 border-gray-400 text-gray-900 font-bold text-lg">
                                    <span>Balance Due:</span>
                                    <span class="<?php echo e($invoice->balance_due > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                        ₱<?php echo e(number_format($invoice->balance_due, 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="mt-8 p-4 rounded-lg <?php echo e($invoice->balance_due <= 0 ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'); ?> border-2">
                        <?php if($invoice->balance_due <= 0): ?>
                            <div class="flex items-center text-green-800">
                                <i class="fas fa-check-circle text-2xl mr-3"></i>
                                <div>
                                    <div class="font-bold text-lg">Fully Paid</div>
                                    <div class="text-sm">All dues have been settled. Thank you!</div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center text-yellow-800">
                                <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                                <div>
                                    <div class="font-bold text-lg">Payment Pending</div>
                                    <div class="text-sm">Outstanding balance: ₱<?php echo e(number_format($invoice->balance_due, 2)); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- No Items Message -->
                    <div class="text-center py-12">
                        <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Transactions Found</h3>
                        <p class="text-gray-500">This customer has no billable transactions at this time.</p>
                    </div>
                <?php endif; ?>

                <!-- Footer Notes -->
                <div class="mt-8 pt-8 border-t-2 border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">
                        <strong>Note:</strong> This invoice shows all transactions for <?php echo e($customer->name); ?>.
                    </p>
                    <p class="text-sm text-gray-600">
                        Generated on <?php echo e(now()->format('F d, Y \a\t h:i A')); ?> by <?php echo e(auth()->user()->name); ?>

                    </p>
                </div>

                <!-- Thank You Message -->
                <div class="mt-6 text-center">
                    <p class="text-gray-700 font-medium">Thank you for choosing Vales Beach Resort!</p>
                    <p class="text-sm text-gray-500 mt-1">We hope you enjoyed your stay with us.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice, #invoice * {
        visibility: visible;
    }
    #invoice {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\invoices\customer-combined.blade.php ENDPATH**/ ?>