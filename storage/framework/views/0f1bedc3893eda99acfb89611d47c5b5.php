

<?php $__env->startSection('title', 'Completed Transaction Details'); ?>

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
                    <h1 class="text-3xl font-bold text-white mb-2">Completed Transaction Details</h1>
                    <p class="text-gray-400">View all your completed payment transactions</p>
                </div>
                <a href="<?php echo e(route('payments.completed')); ?>"
                   class="inline-flex items-center px-3 py-1.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    <i class="fas fa-arrow-left mr-1.5 text-xs"></i>Back
                </a>
            </div>
        </div>

        <!-- Guest Info Card -->
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl overflow-hidden border border-gray-700 shadow-xl mb-6">
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-white"><?php echo e(substr($user->name, 0, 1)); ?></span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white"><?php echo e($user->name); ?></h2>
                            <p class="text-green-100 text-sm"><?php echo e($user->email); ?></p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                                <span class="text-xs text-green-100">
                                    Member since <?php echo e($user->created_at->format('M d, Y')); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-green-100 text-sm">Total Completed</p>
                        <p class="text-3xl font-bold text-white">₱<?php echo e(number_format($totalAmount, 2)); ?></p>
                        <p class="text-green-100 text-sm mt-1"><?php echo e($totalTransactions); ?> transaction<?php echo e($totalTransactions > 1 ? 's' : ''); ?></p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 bg-gray-800/50">
                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bed text-blue-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Bookings</p>
                            <p class="text-sm font-semibold text-white"><?php echo e($bookingPayments->count()); ?> payment<?php echo e($bookingPayments->count() > 1 ? 's' : ''); ?></p>
                        </div>
                    </div>
                    <p class="text-lg font-bold text-green-400">₱<?php echo e(number_format($bookingPayments->sum('amount'), 2)); ?></p>
                </div>

                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-concierge-bell text-purple-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Services</p>
                            <p class="text-sm font-semibold text-white"><?php echo e($servicePayments->count()); ?> payment<?php echo e($servicePayments->count() > 1 ? 's' : ''); ?></p>
                        </div>
                    </div>
                    <p class="text-lg font-bold text-green-400">₱<?php echo e(number_format($servicePayments->sum('amount'), 2)); ?></p>
                </div>

                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-orange-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-orange-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Food Orders</p>
                            <p class="text-sm font-semibold text-white"><?php echo e($foodOrderPayments->count()); ?> payment<?php echo e($foodOrderPayments->count() > 1 ? 's' : ''); ?></p>
                        </div>
                    </div>
                    <p class="text-lg font-bold text-green-400">₱<?php echo e(number_format($foodOrderPayments->sum('amount'), 2)); ?></p>
                </div>

                <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600/50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-yellow-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Extra Charges</p>
                            <p class="text-sm font-semibold text-white"><?php echo e($extraChargePayments->count()); ?> charge<?php echo e($extraChargePayments->count() > 1 ? 's' : ''); ?></p>
                        </div>
                    </div>
                    <p class="text-lg font-bold text-green-400">₱<?php echo e(number_format($extraChargePayments->sum('amount'), 2)); ?></p>
                </div>
            </div>
        </div>

        <!-- All Completed Transactions -->
        <div class="bg-gray-800 rounded-xl overflow-hidden border border-gray-700 shadow-xl">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Completed Payment Transactions</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-750">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Payment Ref</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Type & Details</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Method</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__currentLoopData = $completedPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-750 transition-colors">
                            <!-- Payment Reference -->
                            <td class="px-4 py-3">
                                <div class="text-sm text-blue-400 font-mono">
                                    <?php echo e($payment->payment_reference); ?>

                                </div>
                            </td>

                            <!-- Type & Details -->
                            <td class="px-4 py-3">
                                <?php if($payment->booking): ?>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-bed text-blue-400 mt-0.5"></i>
                                        <div>
                                            <div class="font-medium text-white text-sm">Booking</div>
                                            <div class="text-xs text-gray-400"><?php echo e($payment->booking->room->name ?? 'N/A'); ?></div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo e($payment->booking->check_in_date ? \Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d') : ''); ?> - 
                                                <?php echo e($payment->booking->check_out_date ? \Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y') : ''); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php elseif($payment->serviceRequest): ?>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-concierge-bell text-purple-400 mt-0.5"></i>
                                        <div>
                                            <div class="font-medium text-white text-sm">Service</div>
                                            <div class="text-xs text-gray-400"><?php echo e($payment->serviceRequest->service->name ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                <?php elseif($payment->foodOrder): ?>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-utensils text-orange-400 mt-0.5"></i>
                                        <div>
                                            <div class="font-medium text-white text-sm">Food Order</div>
                                            <div class="text-xs text-gray-400">Order #<?php echo e($payment->foodOrder->order_number); ?></div>
                                            <?php if($payment->foodOrder->orderItems->count() > 0): ?>
                                                <div class="text-xs text-gray-500">
                                                    <?php echo e($payment->foodOrder->orderItems->count()); ?> item<?php echo e($payment->foodOrder->orderItems->count() > 1 ? 's' : ''); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php
                                        $paymentDetails = $payment->payment_details ?? [];
                                        $description = $paymentDetails['description'] ?? 'Extra Charge';
                                        $reference = $paymentDetails['reference'] ?? '';
                                        $details = $paymentDetails['details'] ?? '';
                                    ?>
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-receipt text-yellow-400 mt-0.5"></i>
                                        <div>
                                            <div class="font-medium text-white text-sm">Extra Charge</div>
                                            <div class="text-xs text-gray-400"><?php echo e($description); ?></div>
                                            <?php if($reference): ?>
                                                <div class="text-xs text-gray-500">Ref: <?php echo e($reference); ?></div>
                                            <?php endif; ?>
                                            <?php if($details): ?>
                                                <div class="text-xs text-gray-500"><?php echo e($details); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Amount -->
                            <td class="px-4 py-3">
                                <div class="text-sm font-bold text-green-400">
                                    ₱<?php echo e(number_format($payment->amount, 2)); ?>

                                </div>
                                <?php if($payment->refund_amount > 0): ?>
                                    <div class="text-xs text-red-400">
                                        -₱<?php echo e(number_format($payment->refund_amount, 2)); ?>

                                    </div>
                                    <div class="text-xs font-medium text-gray-300">
                                        Net: ₱<?php echo e(number_format($payment->amount - $payment->refund_amount, 2)); ?>

                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Payment Method -->
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-300">
                                    <?php echo e(ucfirst($payment->payment_method ?? 'N/A')); ?>

                                </div>
                            </td>

                            <!-- Date -->
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-300">
                                    <?php echo e($payment->created_at->format('M d, Y')); ?>

                                </div>
                                <div class="text-xs text-gray-400">
                                    <?php echo e($payment->created_at->format('h:i A')); ?>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/payments/completed-details.blade.php ENDPATH**/ ?>