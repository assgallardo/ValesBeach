

<?php $__env->startSection('title', 'Completed Transactions'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">✅ Completed Transactions</h1>
                    <p class="text-gray-400">View all your completed payment transactions</p>
                </div>
                <a href="<?php echo e(route('payments.history')); ?>"
                   class="inline-flex items-center px-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-1.5 text-xs"></i>Back
                </a>
            </div>
        </div>

        <?php if($paymentTransactions->isEmpty()): ?>
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-xl p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-600/20 rounded-full mb-4">
                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Completed Transactions</h3>
                <p class="text-gray-400 mb-6">Your completed payment transactions will appear here.</p>
                <a href="<?php echo e(route('payments.history')); ?>"
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payment History
                </a>
            </div>
        <?php else: ?>
            <!-- Stats Summary -->
            <div class="mb-6">
                <p class="text-gray-400 text-right">Showing 1 to <?php echo e($paymentTransactions->count()); ?> of <?php echo e($paymentTransactions->count()); ?> payment transaction<?php echo e($paymentTransactions->count() > 1 ? 's' : ''); ?></p>
            </div>

            <!-- Payment Transaction Cards -->
            <?php $__currentLoopData = $paymentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 shadow-xl mb-4">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="w-[20%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Guest</th>
                            <th class="w-[15%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Types</th>
                            <th class="w-[15%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Amount</th>
                            <th class="w-[15%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Payments</th>
                            <th class="w-[15%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                            <th class="w-[10%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Latest Date</th>
                            <th class="w-[10%] px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <!-- Guest Info -->
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-white"><?php echo e(substr($user->name, 0, 1)); ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-white truncate"><?php echo e($user->name); ?></div>
                                        <div class="text-xs text-gray-400 truncate"><?php echo e($user->email); ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Payment Types -->
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <?php if($transaction->bookings_count > 0): ?>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-bed text-blue-400 text-xs"></i>
                                            <span class="text-xs text-gray-300"><?php echo e($transaction->bookings_count); ?> Booking<?php echo e($transaction->bookings_count > 1 ? 's' : ''); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($transaction->services_count > 0): ?>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-concierge-bell text-purple-400 text-xs"></i>
                                            <span class="text-xs text-gray-300"><?php echo e($transaction->services_count); ?> Service<?php echo e($transaction->services_count > 1 ? 's' : ''); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($transaction->food_orders_count > 0): ?>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-utensils text-orange-400 text-xs"></i>
                                            <span class="text-xs text-gray-300"><?php echo e($transaction->food_orders_count); ?> Food Order<?php echo e($transaction->food_orders_count > 1 ? 's' : ''); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($transaction->extra_charges_count > 0): ?>
                                        <div class="flex items-center gap-1.5">
                                            <i class="fas fa-receipt text-yellow-400 text-xs"></i>
                                            <span class="text-xs text-gray-300"><?php echo e($transaction->extra_charges_count); ?> Extra Charge<?php echo e($transaction->extra_charges_count > 1 ? 's' : ''); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Total Amount -->
                            <td class="px-4 py-4">
                                <div class="text-lg font-bold text-green-400">₱<?php echo e(number_format($transaction->totalAmount, 2)); ?></div>
                            </td>

                            <!-- Payments Count -->
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-300"><?php echo e($transaction->totalTransactions); ?> payment<?php echo e($transaction->totalTransactions > 1 ? 's' : ''); ?></div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-600/20 text-white">
                                    <?php echo e($transaction->totalTransactions); ?> Completed
                                </span>
                            </td>

                            <!-- Latest Date -->
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-300">
                                    <?php echo e(\Carbon\Carbon::parse($transaction->latestDate)->format('M d, Y')); ?>

                                </div>
                                <div class="text-xs text-gray-400">
                                    <?php echo e(\Carbon\Carbon::parse($transaction->latestDate)->format('h:i A')); ?>

                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4">
                                <a href="<?php echo e(route('payments.completed.details', ['transaction_id' => $transaction->payment_transaction_id])); ?>"
                                   class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                    <i class="fas fa-eye mr-1.5 text-xs"></i>View Details
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/payments/completed.blade.php ENDPATH**/ ?>