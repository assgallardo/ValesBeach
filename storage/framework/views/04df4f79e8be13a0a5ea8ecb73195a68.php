<?php $__env->startSection('content'); ?>
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Reports & Analytics
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    View detailed reports and analytics
                </p>
                <div class="mt-6">
                    <a href="<?php echo e(route('manager.dashboard')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Reports Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Monthly Bookings -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Monthly Bookings</h3>
                    <?php if($monthlyBookings->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $monthlyBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                            <div>
                                <h4 class="text-green-200 font-medium">
                                    <?php echo e(\Carbon\Carbon::create($month->year, $month->month)->format('F Y')); ?>

                                </h4>
                                <p class="text-green-300 text-sm"><?php echo e($month->total_bookings); ?> bookings</p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-50 font-bold">₱<?php echo e(number_format($month->total_revenue ?? 0, 2)); ?></p>
                                <p class="text-green-300 text-sm">Revenue</p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <p class="text-green-300">No booking data available.</p>
                    <?php endif; ?>
                </div>

                <!-- Popular Rooms -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Popular Rooms</h3>
                    <?php if($popularRooms->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $popularRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                            <div>
                                <h4 class="text-green-200 font-medium"><?php echo e($room->name); ?></h4>
                                <p class="text-green-300 text-sm"><?php echo e($room->type ?? 'Standard'); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-50 font-bold"><?php echo e($room->booking_count ?? 0); ?></p>
                                <p class="text-green-300 text-sm">Bookings</p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php else: ?>
                    <p class="text-green-300">No room data available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Service Statistics -->
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 mt-8">
                <h3 class="text-2xl font-bold text-green-50 mb-6">Service Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                        <span class="text-green-300">Available Services</span>
                        <span class="text-green-50 font-bold"><?php echo e($availableServices ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                        <span class="text-green-300">Total Services</span>
                        <span class="text-green-50 font-bold"><?php echo e($totalServices ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/reports.blade.php ENDPATH**/ ?>