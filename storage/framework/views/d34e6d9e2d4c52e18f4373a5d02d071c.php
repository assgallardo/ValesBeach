

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="<?php echo e(route($routePrefix . '.reports.index', request()->query())); ?>" class="text-teal-400 hover:text-teal-300 mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <h1 class="text-3xl font-bold text-white mt-2">
                <i class="fas fa-heart mr-3 text-teal-400"></i>Customer Preferences Report
            </h1>
            <p class="text-gray-400 mt-1">Analyze customer behavior and preferences</p>
        </div>

        <!-- Date Range Display -->
        <div class="bg-teal-900/30 border border-teal-600/30 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="fas fa-calendar text-teal-400 mr-3"></i>
                <span class="text-teal-100">Data from <strong><?php echo e($startDate->format('M d, Y')); ?></strong> to <strong><?php echo e($endDate->format('M d, Y')); ?></strong></span>
            </div>
        </div>

        <!-- Room Preferences -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-door-open text-blue-400 mr-3"></i>Room Type Preferences
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php $__empty_0 = true; $__currentLoopData = $roomPreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-white font-semibold mb-2"><?php echo e($pref->category); ?></h4>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-400 text-sm">Bookings:</span>
                            <span class="text-blue-400 font-bold"><?php echo e($pref->booking_count); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-sm">Unique Customers:</span>
                            <span class="text-green-400 font-bold"><?php echo e($pref->unique_customers); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="col-span-3 text-center text-gray-400 py-8">No data available</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Service Preferences -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-900/30 to-purple-800/20 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-concierge-bell text-purple-400 mr-3"></i>Service Preferences
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Requests</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Unique Customers</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $servicePreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-white"><?php echo e($pref->category); ?></td>
                            <td class="px-6 py-4 text-gray-300"><?php echo e($pref->name); ?></td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-purple-900/30 text-purple-400 rounded-full text-sm font-semibold"><?php echo e($pref->request_count); ?></span></td>
                            <td class="px-6 py-4 text-gray-400"><?php echo e($pref->unique_customers); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No service data available</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Food Preferences -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-utensils text-green-400 mr-3"></i>Top Food & Beverage Items
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Total Orders</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Unique Customers</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $foodPreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-white"><?php echo e($pref->category); ?></td>
                            <td class="px-6 py-4 text-gray-300"><?php echo e($pref->item_name); ?></td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-green-900/30 text-green-400 rounded-full text-sm font-semibold"><?php echo e($pref->total_orders); ?></span></td>
                            <td class="px-6 py-4 text-gray-400"><?php echo e($pref->unique_customers); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No food data available</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Peak Booking Times -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-900/30 to-yellow-800/20 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-clock text-yellow-400 mr-3"></i>Peak Booking Days
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    <?php $__empty_0 = true; $__currentLoopData = $bookingTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <div class="bg-gray-900 rounded-lg p-4 text-center border border-gray-700">
                        <div class="text-gray-400 text-sm mb-2"><?php echo e($time->day_name); ?></div>
                        <div class="text-yellow-400 text-2xl font-bold"><?php echo e($time->booking_count); ?></div>
                        <div class="text-gray-500 text-xs mt-1">bookings</div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <div class="col-span-7 text-center text-gray-400 py-8">No booking time data</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\reports\customer-preferences.blade.php ENDPATH**/ ?>