<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo e(route($routePrefix . '.reports.index', request()->query())); ?>" class="text-emerald-400 hover:text-emerald-300 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <h1 class="text-3xl font-bold text-white mt-2">
                <i class="fas fa-credit-card mr-3 text-emerald-400"></i>Payment Methods Report
            </h1>
            <p class="text-gray-400 mt-1">Payment transaction analysis and trends</p>
        </div>

        <!-- Date Range Display -->
        <div class="bg-emerald-900/30 border border-emerald-600/30 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="fas fa-calendar text-emerald-400 mr-3"></i>
                <span class="text-emerald-100">Data from <strong><?php echo e($startDate->format('M d, Y')); ?></strong> to <strong><?php echo e($endDate->format('M d, Y')); ?></strong></span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-receipt text-blue-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1"><?php echo e(number_format($stats['total_transactions'])); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Total Transactions</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-peso-sign text-green-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">₱<?php echo e(number_format($stats['total_revenue'], 2)); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Total Revenue</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">₱<?php echo e(number_format($stats['avg_transaction'], 2)); ?></h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Avg Transaction</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-900/40 to-emerald-800/30 rounded-lg border-2 border-emerald-500/50 p-5">
                <div class="w-12 h-12 bg-emerald-600/30 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-star text-emerald-400 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-emerald-400 mb-1"><?php echo e(ucwords(str_replace('_', ' ', $stats['most_popular_method']))); ?></h2>
                <p class="text-emerald-200 text-xs uppercase tracking-wider font-semibold">Most Popular</p>
            </div>
        </div>

        <!-- Payment Method Statistics -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-emerald-900/30 to-emerald-800/20 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-chart-pie text-emerald-400 mr-3"></i>Payment Method Breakdown
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Payment Method</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Transactions</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Total Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Avg Transaction</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">% of Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php
                            $totalAmount = $paymentMethodStats->sum('total_amount');
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $paymentMethodStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-emerald-600/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-<?php echo e($method->payment_method === 'cash' ? 'money-bill' : ($method->payment_method === 'card' ? 'credit-card' : 'mobile')); ?> text-emerald-400"></i>
                                    </div>
                                    <span class="text-white font-medium"><?php echo e(ucwords(str_replace('_', ' ', $method->payment_method))); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-900/30 text-blue-400 text-sm font-semibold rounded-full border border-blue-600/30">
                                    <?php echo e(number_format($method->transaction_count)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-semibold">₱<?php echo e(number_format($method->total_amount, 2)); ?></div>
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                ₱<?php echo e(number_format($method->avg_transaction, 2)); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $percentage = $totalAmount > 0 ? ($method->total_amount / $totalAmount) * 100 : 0;
                                ?>
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-700 rounded-full h-2 mr-3">
                                        <div class="bg-emerald-500 h-2 rounded-full" style="width: <?php echo e($percentage); ?>%"></div>
                                    </div>
                                    <span class="text-emerald-400 font-semibold text-sm"><?php echo e(number_format($percentage, 1)); ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-5xl mb-4 block"></i>
                                <p class="text-lg">No payment data available</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Methods by Source -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Booking Payments -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 px-6 py-4 border-b border-gray-700">
                    <h4 class="font-semibold text-white flex items-center">
                        <i class="fas fa-door-open text-blue-400 mr-2"></i>Booking Payments
                    </h4>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $paymentsBySource['bookings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300 text-sm"><?php echo e(ucwords(str_replace('_', ' ', $payment->payment_method))); ?></span>
                        <span class="text-blue-400 font-bold">₱<?php echo e(number_format($payment->total, 2)); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Food Order Payments -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 px-6 py-4 border-b border-gray-700">
                    <h4 class="font-semibold text-white flex items-center">
                        <i class="fas fa-utensils text-green-400 mr-2"></i>Food Order Payments
                    </h4>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $paymentsBySource['food_orders']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300 text-sm"><?php echo e(ucwords(str_replace('_', ' ', $payment->payment_method))); ?></span>
                        <span class="text-green-400 font-bold">₱<?php echo e(number_format($payment->total, 2)); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Service Payments -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-900/30 to-purple-800/20 px-6 py-4 border-b border-gray-700">
                    <h4 class="font-semibold text-white flex items-center">
                        <i class="fas fa-concierge-bell text-purple-400 mr-2"></i>Service Payments
                    </h4>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $paymentsBySource['services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300 text-sm"><?php echo e(ucwords(str_replace('_', ' ', $payment->payment_method))); ?></span>
                        <span class="text-purple-400 font-bold">₱<?php echo e(number_format($payment->total, 2)); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/reports/payment-methods.blade.php ENDPATH**/ ?>