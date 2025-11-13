<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo e(route($routePrefix . '.reports.index', request()->query())); ?>" class="text-cyan-400 hover:text-cyan-300 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <h1 class="text-3xl font-bold text-white mt-2">
                <i class="fas fa-chart-line mr-3 text-cyan-400"></i>Customer Analytics Summary
            </h1>
            <p class="text-gray-400 mt-1">Comprehensive overview of customer behavior and preferences</p>
        </div>

        <!-- Date Range Display -->
        <div class="bg-cyan-900/30 border border-cyan-600/30 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="fas fa-calendar text-cyan-400 mr-3 text-lg"></i>
                <span class="text-cyan-100">
                    Showing data from <strong><?php echo e($startDate->format('M d, Y')); ?></strong> to <strong><?php echo e($endDate->format('M d, Y')); ?></strong>
                </span>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-400 text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2"><?php echo e(number_format($totalCustomers)); ?></h2>
                <p class="text-gray-400 text-sm">Total Customers with Bookings</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-400 text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2"><?php echo e(number_format($repeatCustomers)); ?></h2>
                <p class="text-gray-400 text-sm">Repeat Customers (2+ Bookings)</p>
            </div>

            <div class="bg-gradient-to-br from-cyan-900/40 to-cyan-800/30 rounded-lg border-2 border-cyan-500/50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-cyan-600/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-cyan-400 text-2xl"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-cyan-400 mb-2"><?php echo e($retentionRate); ?>%</h2>
                <p class="text-cyan-200 text-sm font-semibold">Customer Retention Rate</p>
            </div>
        </div>

        <!-- Top Repeat Customers -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-cyan-900/30 to-cyan-800/20 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-cyan-600/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-star text-cyan-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Top Repeat Customers</h3>
                            <p class="text-gray-400 text-xs">Your most loyal customers</p>
                        </div>
                    </div>
                    <a href="<?php echo e(route($routePrefix . '.reports.repeat-customers', request()->query())); ?>" 
                       class="text-cyan-400 hover:text-cyan-300 text-sm">
                        View Full Report <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Bookings</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Total Spent</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase">Payment Methods</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $topRepeatCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <?php
                            $totalSpent = $customer->bookings->sum('total_price');
                            $paymentMethods = $customer->bookings->flatMap->payments->pluck('payment_method')->unique()->filter();
                        ?>
                        <tr class="hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-cyan-600/20 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-check text-cyan-400"></i>
                                    </div>
                                    <div class="text-white font-medium"><?php echo e($customer->name); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-sm"><?php echo e($customer->email); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-cyan-900/30 text-cyan-400 text-sm font-semibold rounded-full border border-cyan-600/30">
                                    <?php echo e($customer->bookings_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-semibold">₱<?php echo e(number_format($totalSpent, 2)); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php $__empty_1 = true; $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <span class="px-2 py-1 bg-green-900/30 text-green-400 text-xs rounded border border-green-600/30">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $method))); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <span class="text-gray-500 text-xs">No payments</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No repeat customers found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Three Column Grid: Payment Methods, Room Preferences, Service Preferences -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Top Payment Methods -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-900/30 to-emerald-800/20 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-emerald-600/30 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-credit-card text-emerald-400 text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white text-sm">Payment Methods</h4>
                        </div>
                        <a href="<?php echo e(route($routePrefix . '.reports.payment-methods', request()->query())); ?>" 
                           class="text-emerald-400 hover:text-emerald-300 text-xs">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_0 = true; $__currentLoopData = $topPaymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="flex justify-between items-center p-3 bg-gray-900 rounded border border-gray-700">
                        <div>
                            <div class="text-white font-medium text-sm"><?php echo e(ucfirst(str_replace('_', ' ', $method->payment_method))); ?></div>
                            <div class="text-gray-400 text-xs"><?php echo e($method->count); ?> transactions</div>
                        </div>
                        <div class="text-emerald-400 font-bold">₱<?php echo e(number_format($method->total, 2)); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Room Preferences -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600/30 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-door-open text-blue-400 text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white text-sm">Room Preferences</h4>
                        </div>
                        <a href="<?php echo e(route($routePrefix . '.reports.customer-preferences', request()->query())); ?>" 
                           class="text-blue-400 hover:text-blue-300 text-xs">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_0 = true; $__currentLoopData = $topRoomPreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="flex justify-between items-center p-3 bg-gray-900 rounded border border-gray-700">
                        <div>
                            <div class="text-white font-medium text-sm"><?php echo e($pref->category); ?></div>
                            <div class="text-gray-400 text-xs"><?php echo e($pref->unique_customers); ?> customers</div>
                        </div>
                        <div class="text-blue-400 font-bold"><?php echo e($pref->booking_count); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Service Preferences -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-900/30 to-purple-800/20 px-6 py-4 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-600/30 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-concierge-bell text-purple-400 text-sm"></i>
                            </div>
                            <h4 class="font-semibold text-white text-sm">Service Preferences</h4>
                        </div>
                        <a href="<?php echo e(route($routePrefix . '.reports.customer-preferences', request()->query())); ?>" 
                           class="text-purple-400 hover:text-purple-300 text-xs">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__empty_0 = true; $__currentLoopData = $topServicePreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="flex justify-between items-center p-3 bg-gray-900 rounded border border-gray-700">
                        <div class="text-white font-medium text-sm"><?php echo e($pref->name); ?></div>
                        <div class="text-purple-400 font-bold"><?php echo e($pref->request_count); ?></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <p class="text-gray-500 text-sm text-center py-4">No data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Food Items -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-utensils text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Top Food & Beverage Items</h3>
                            <p class="text-gray-400 text-xs">Most ordered items</p>
                        </div>
                    </div>
                    <a href="<?php echo e(route($routePrefix . '.reports.customer-preferences', request()->query())); ?>" 
                       class="text-green-400 hover:text-green-300 text-sm">
                        View Full Report <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php $__empty_0 = true; $__currentLoopData = $topFoodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-white font-medium"><?php echo e($item->name); ?></h4>
                                <p class="text-gray-400 text-xs mt-1">Total orders</p>
                            </div>
                            <div class="text-2xl font-bold text-green-400"><?php echo e($item->total_orders); ?></div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="col-span-3 text-center text-gray-400 py-8">No food order data</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\reports\customer-analytics.blade.php ENDPATH**/ ?>