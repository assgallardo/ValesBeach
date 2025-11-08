<?php $__env->startSection('title', 'Combined Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Invoice Container -->
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Invoice Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-8 text-white">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">INVOICE</h1>
                        <p class="text-green-100">Vales Beach Resort</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold"><?php echo e($invoice->invoice_number); ?></p>
                        <p class="text-green-100 text-sm"><?php echo e(\Carbon\Carbon::parse($invoice->created_at)->format('M d, Y')); ?></p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2">Bill To:</h3>
                        <p class="text-green-100"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-green-100 text-sm"><?php echo e(auth()->user()->email); ?></p>
                    </div>
                    <div class="text-left md:text-right">
                        <h3 class="font-semibold mb-2">From:</h3>
                        <p class="text-green-100">Vales Beach Resort</p>
                        <p class="text-green-100 text-sm">Hospitality and Leisure Services</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="p-8">
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
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
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
                                <span>Subtotal:</span>
                                <span class="font-medium">₱<?php echo e(number_format($invoice->subtotal, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-t border-gray-300 text-gray-900 font-bold text-lg">
                                <span>Total:</span>
                                <span>₱<?php echo e(number_format($invoice->total, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-t border-gray-200 text-green-600 font-semibold">
                                <span>Total Paid:</span>
                                <span>₱<?php echo e(number_format($invoice->total_paid, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-t border-gray-200 font-bold text-lg <?php echo e($invoice->total_balance > 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                <span>Balance Due:</span>
                                <span>₱<?php echo e(number_format($invoice->total_balance, 2)); ?></span>
                            </div>
                            <?php if(isset($invoice->general_payment_method) && $invoice->general_payment_method): ?>
                            <div class="flex justify-between py-2 border-t border-gray-200 pt-3 mt-2">
                                <span class="text-gray-600 font-medium">Payment Method:</span>
                                <span class="text-gray-900 font-semibold"><?php echo e(ucfirst(str_replace('_', ' ', $invoice->general_payment_method))); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Banner -->
                <?php if($invoice->total_balance <= 0): ?>
                <div class="mt-8 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-green-800">PAID IN FULL</p>
                            <p class="text-sm text-green-700">This invoice has been fully paid. Thank you!</p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-yellow-800">PAYMENT DUE</p>
                            <p class="text-sm text-yellow-700">Outstanding balance: ₱<?php echo e(number_format($invoice->total_balance, 2)); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>


                <!-- Notes -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-2">Notes:</h3>
                    <p class="text-sm text-gray-600">This is a combined invoice for multiple transactions. Please settle any outstanding balance at the front desk or through our payment system.</p>
                    <p class="text-sm text-gray-600 mt-2">Thank you for choosing Vales Beach Resort!</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-gray-100 p-6 flex flex-wrap gap-3">
                <button onclick="window.print()" 
                        class="flex-1 md:flex-none px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
                <a href="<?php echo e(route('payments.history')); ?>" 
                   class="flex-1 md:flex-none px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .bg-white, .bg-white * {
        visibility: visible;
    }
    .bg-white {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .bg-gray-100 {
        display: none !important;
    }
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.invoice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/invoices/combined.blade.php ENDPATH**/ ?>