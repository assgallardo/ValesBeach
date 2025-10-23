<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Order Statistics</h1>
            <p class="text-gray-400 mt-1">View order analytics and performance metrics</p>
        </div>
        <a href="<?php echo e(route('staff.orders.index')); ?>" 
           class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Orders
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Today's Orders -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-blue-100 mb-2">Today's Orders</h3>
            <p class="text-3xl font-bold mb-3"><?php echo e($stats['today']['orders']); ?></p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-blue-100">Revenue</span>
                <span class="font-semibold">₱<?php echo e(number_format($stats['today']['revenue'], 2)); ?></span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="text-blue-100">Completed</span>
                <span class="font-semibold"><?php echo e($stats['today']['completed']); ?></span>
            </div>
        </div>

        <!-- This Week -->
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-lg shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-purple-100 mb-2">This Week</h3>
            <p class="text-3xl font-bold mb-3"><?php echo e($stats['week']['orders']); ?></p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-purple-100">Revenue</span>
                <span class="font-semibold">₱<?php echo e(number_format($stats['week']['revenue'], 2)); ?></span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="text-purple-100">Completed</span>
                <span class="font-semibold"><?php echo e($stats['week']['completed']); ?></span>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-gradient-to-br from-orange-600 to-orange-800 rounded-lg shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-orange-100 mb-2">This Month</h3>
            <p class="text-3xl font-bold mb-3"><?php echo e($stats['month']['orders']); ?></p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-orange-100">Revenue</span>
                <span class="font-semibold">₱<?php echo e(number_format($stats['month']['revenue'], 2)); ?></span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="text-orange-100">Completed</span>
                <span class="font-semibold"><?php echo e($stats['month']['completed']); ?></span>
            </div>
        </div>

        <!-- All Time -->
        <div class="bg-gradient-to-br from-green-600 to-green-800 rounded-lg shadow-xl p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white/20 rounded-lg p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-sm font-medium text-green-100 mb-2">All Time</h3>
            <p class="text-3xl font-bold mb-3"><?php echo e($stats['all_time']['orders']); ?></p>
            <div class="flex items-center justify-between text-sm">
                <span class="text-green-100">Revenue</span>
                <span class="font-semibold">₱<?php echo e(number_format($stats['all_time']['revenue'], 2)); ?></span>
            </div>
            <div class="flex items-center justify-between text-sm mt-1">
                <span class="text-green-100">Completed</span>
                <span class="font-semibold"><?php echo e($stats['all_time']['completed']); ?></span>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Items -->
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gray-700 px-6 py-4 border-b border-gray-600">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-white">Top 10 Popular Items</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-300 uppercase tracking-wider">Item Name</th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-300 uppercase tracking-wider">Total Orders</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $popularItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-700 transition-colors duration-150">
                                    <td class="py-3 px-4 text-white"><?php echo e($item->name); ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-900 text-blue-200">
                                            <?php echo e($item->total_quantity); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="2" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-gray-400">No data available</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <div class="bg-gray-700 px-6 py-4 border-b border-gray-600">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-white">Recent Orders</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Order #</th>
                                <th class="text-left py-3 px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="text-center py-3 px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Items</th>
                                <th class="text-center py-3 px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="text-right py-3 px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-700 transition-colors duration-150">
                                    <td class="py-3 px-2">
                                        <a href="<?php echo e(route('staff.orders.show', $order)); ?>" class="text-blue-400 hover:text-blue-300 font-medium">
                                            <?php echo e($order->order_number); ?>

                                        </a>
                                    </td>
                                    <td class="py-3 px-2 text-gray-300"><?php echo e($order->customer_name); ?></td>
                                    <td class="py-3 px-2 text-center text-gray-300"><?php echo e($order->orderItems->count()); ?></td>
                                    <td class="py-3 px-2 text-center">
                                        <?php
                                            $statusConfig = [
                                                'pending' => ['bg' => 'bg-yellow-900', 'text' => 'text-yellow-200', 'label' => 'Pending'],
                                                'preparing' => ['bg' => 'bg-blue-900', 'text' => 'text-blue-200', 'label' => 'Preparing'],
                                                'ready' => ['bg' => 'bg-purple-900', 'text' => 'text-purple-200', 'label' => 'Ready'],
                                                'completed' => ['bg' => 'bg-green-900', 'text' => 'text-green-200', 'label' => 'Completed'],
                                                'cancelled' => ['bg' => 'bg-red-900', 'text' => 'text-red-200', 'label' => 'Cancelled']
                                            ];
                                            $config = $statusConfig[$order->status] ?? ['bg' => 'bg-gray-700', 'text' => 'text-gray-300', 'label' => ucfirst($order->status)];
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($config['bg']); ?> <?php echo e($config['text']); ?>">
                                            <?php echo e($config['label']); ?>

                                        </span>
                                    </td>
                                    <td class="py-3 px-2 text-right text-white font-semibold">₱<?php echo e(number_format($order->total_amount, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-gray-400">No orders yet</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.staff', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/staff/orders/statistics.blade.php ENDPATH**/ ?>