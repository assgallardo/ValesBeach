<?php $__env->startSection('title', 'Payment History'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-full mb-4">
                <i class="fas fa-history text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">Payment History</h1>
            <p class="text-gray-400">View all your payment transactions grouped by booking</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="<?php echo e(route('guest.bookings')); ?>"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-calendar mr-2"></i>My Bookings
            </a>
            <a href="<?php echo e(route('invoices.index')); ?>"
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                <i class="fas fa-file-invoice mr-2"></i>My Invoices
            </a>
            <a href="<?php echo e(route('guest.dashboard')); ?>"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Dashboard
            </a>
        </div>

        <?php if($bookings->isEmpty() && $servicePayments->isEmpty()): ?>
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Payment History</h3>
                <p class="text-gray-400 mb-6">You haven't made any payments yet. Make a booking to get started!</p>
                <a href="<?php echo e(route('guest.rooms.browse')); ?>"
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-bed mr-2"></i>Browse Rooms
                </a>
            </div>
        <?php else: ?>
            <!-- Booking Payments (Grouped) -->
            <div class="space-y-6">
                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-800 rounded-lg overflow-hidden">
                    <!-- Booking Header -->
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 border-b border-gray-700">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-bed text-green-400 text-xl"></i>
                                    <h2 class="text-2xl font-bold text-green-50"><?php echo e($booking->room->name); ?></h2>
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag text-gray-400 mr-2"></i>
                                        <span><?php echo e($booking->booking_reference); ?></span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                        <span><?php echo e($booking->check_in->format('M d')); ?> - <?php echo e($booking->check_out->format('M d, Y')); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Status Badge -->
                            <div class="flex flex-col items-end gap-2">
                                <?php if($booking->remaining_balance <= 0 || $booking->payment_status === 'paid'): ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-600 text-white">
                                        <i class="fas fa-check-circle mr-2"></i>FULLY PAID
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-yellow-500 text-black">
                                        <i class="fas fa-exclamation-circle mr-2"></i>PARTIAL PAYMENT
                                    </span>
                                <?php endif; ?>
                                
                                <?php
                                    $statusConfig = [
                                        'completed' => ['color' => 'bg-green-600 text-white', 'label' => 'Completed'],
                                        'confirmed' => ['color' => 'bg-blue-600 text-white', 'label' => 'Confirmed'],
                                        'pending' => ['color' => 'bg-gray-500 text-white', 'label' => 'Pending'],
                                        'cancelled' => ['color' => 'bg-red-600 text-white', 'label' => 'Cancelled']
                                    ];
                                    $config = $statusConfig[$booking->status] ?? ['color' => 'bg-gray-500 text-white', 'label' => ucfirst($booking->status)];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded text-xs font-medium <?php echo e($config['color']); ?>">
                                    <?php echo e($config['label']); ?>

                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-900/50 p-4 border-b border-gray-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Total Booking</p>
                                <p class="text-green-50 font-bold text-lg">₱<?php echo e(number_format($booking->total_price, 2)); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Total Paid</p>
                                <p class="text-green-400 font-bold text-lg">₱<?php echo e(number_format($booking->amount_paid, 2)); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Remaining Balance</p>
                                <p class="font-bold text-lg <?php echo e($booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400'); ?>">
                                    ₱<?php echo e(number_format($booking->remaining_balance, 2)); ?>

                                </p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Number of Payments</p>
                                <p class="text-blue-400 font-bold text-lg"><?php echo e($booking->payments->count()); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Payments List -->
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wide">
                            <i class="fas fa-list mr-2"></i>Payment Transactions (<?php echo e($booking->payments->count()); ?>)
                        </h3>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $booking->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <!-- Payment Amount -->
                                            <span class="text-green-400 font-bold text-xl">₱<?php echo e(number_format($payment->amount, 2)); ?></span>
                                            
                                            <!-- Payment Status -->
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                <?php echo e($payment->status === 'completed' ? 'bg-green-500 text-white' : 
                                                   ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white')); ?>">
                                                <?php echo e(ucfirst($payment->status)); ?>

                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                            <div>
                                                <span class="text-gray-400">Reference:</span>
                                                <span class="text-green-50 ml-1 font-medium"><?php echo e($payment->payment_reference); ?></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Method:</span>
                                                <span class="text-green-50 ml-1">
                                                    <?php
                                                        $methodIcons = [
                                                            'cash' => 'money-bill-wave',
                                                            'card' => 'credit-card',
                                                            'gcash' => 'mobile-alt',
                                                            'bank_transfer' => 'university',
                                                            'paymaya' => 'mobile-alt',
                                                            'online' => 'globe'
                                                        ];
                                                        $icon = $methodIcons[$payment->payment_method] ?? 'money-bill';
                                                    ?>
                                                    <i class="fas fa-<?php echo e($icon); ?> mr-1"></i>
                                                    <?php echo e($payment->payment_method_display); ?>

                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Date:</span>
                                                <span class="text-green-50 ml-1"><?php echo e($payment->created_at->format('M d, Y g:i A')); ?></span>
                                            </div>
                                        </div>

                                        <?php if($payment->notes): ?>
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-400">Notes:</span>
                                            <span class="text-gray-300 ml-1"><?php echo e($payment->notes); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Payment Actions -->
                                    <div class="flex gap-2">
                                        <a href="<?php echo e(route('payments.show', $payment)); ?>" 
                                           class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                                           title="View Payment Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Booking Action -->
                    <div class="bg-gray-700/30 p-4 border-t border-gray-700">
                        <div class="flex flex-wrap gap-3">
                            <a href="<?php echo e(route('guest.bookings.show', $booking)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View Booking Details
                            </a>
                            
                            <?php if($booking->remaining_balance > 0 && $booking->status !== 'cancelled'): ?>
                            <a href="<?php echo e(route('payments.create', $booking)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>Pay Remaining Balance
                            </a>
                            <?php endif; ?>
                            
                            <?php if($booking->invoice): ?>
                            <!-- Invoice exists (ID: <?php echo e($booking->invoice->id); ?>) -->
                            <a href="<?php echo e(route('invoices.show', $booking->invoice)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-file-invoice mr-2"></i>View Invoice
                            </a>
                            <a href="<?php echo e(route('invoices.download', $booking->invoice)); ?>" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download Invoice
                            </a>
                            <?php else: ?>
                            <!-- No invoice yet - showing generate button -->
                            <form action="<?php echo e(route('invoices.generate', $booking)); ?>" method="POST" class="inline" 
                                  onsubmit="return confirm('Generate invoice for booking <?php echo e($booking->booking_reference); ?>?\n\nBooking ID: <?php echo e($booking->id); ?>\nTotal Paid: ₱<?php echo e(number_format($booking->amount_paid, 2)); ?>');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors cursor-pointer"
                                        title="Generate invoice for this booking">
                                    <i class="fas fa-file-invoice-dollar mr-2"></i>Generate Invoice
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Service Payments (if any) -->
                <?php if($servicePayments->isNotEmpty()): ?>
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-green-50 mb-4 flex items-center">
                        <i class="fas fa-concierge-bell text-blue-400 mr-3"></i>
                        Service Payments
                    </h2>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $servicePayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-700/50 rounded-lg p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-blue-400 font-bold text-xl">₱<?php echo e(number_format($payment->amount, 2)); ?></span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            <?php echo e($payment->status === 'completed' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white'); ?>">
                                            <?php echo e(ucfirst($payment->status)); ?>

                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-400">Reference:</span>
                                            <span class="text-green-50 ml-1"><?php echo e($payment->payment_reference); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Method:</span>
                                            <span class="text-green-50 ml-1"><?php echo e($payment->payment_method_display); ?></span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Date:</span>
                                            <span class="text-green-50 ml-1"><?php echo e($payment->created_at->format('M d, Y')); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <a href="<?php echo e(route('payments.show', $payment)); ?>" 
                                   class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View
                                </a>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Summary Stats -->
            <?php
                $totalPaid = $bookings->sum('amount_paid') + $servicePayments->where('status', 'completed')->sum('amount');
                $totalBookings = $bookings->count();
                $fullyPaidBookings = $bookings->where('remaining_balance', '<=', 0)->count();
            ?>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-green-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Total Paid</p>
                            <p class="text-xl font-bold text-green-50">₱<?php echo e(number_format($totalPaid, 2)); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-bed text-blue-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Total Bookings</p>
                            <p class="text-xl font-bold text-green-50"><?php echo e($totalBookings); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Fully Paid</p>
                            <p class="text-xl font-bold text-green-50"><?php echo e($fullyPaidBookings); ?> / <?php echo e($totalBookings); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/payments/history.blade.php ENDPATH**/ ?>