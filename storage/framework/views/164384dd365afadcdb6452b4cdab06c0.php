<?php $__env->startSection('title', 'Payment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-green-50">Payment Management</h1>
                <p class="text-gray-400 mt-2">Manage all guest payments for bookings and services</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button type="button" 
                        onclick="openGenerateInvoiceModal()" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Generate Invoice
                </button>
                <button type="button" 
                        onclick="toggleFilterPanel()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filters
                </button>
                <a href="<?php echo e(route('admin.payments.export', request()->query())); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </a>
            </div>
        </div>

        <!-- Statistics Cards - Single Line Compact View -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-9 gap-3 mb-6">
            <!-- Total Revenue -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-green-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-dollar-sign text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Total Revenue</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['total_payments'], 2)); ?></p>
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-yellow-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-clock text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Pending</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['pending_payments'], 2)); ?></p>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-teal-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-teal-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-receipt text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Transactions</p>
                    <p class="text-sm font-bold text-green-50"><?php echo e(number_format($stats['total_count'])); ?></p>
                </div>
            </div>

            <!-- Room Bookings -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-blue-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-bed text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Room Bookings</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['booking_payments'], 2)); ?></p>
                </div>
            </div>

            <!-- Services -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-purple-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-concierge-bell text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Services</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['service_payments'], 2)); ?></p>
                </div>
            </div>

            <!-- Food & Beverage -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-orange-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-utensils text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Food & Beverage</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['food_order_payments'] ?? 0, 2)); ?></p>
                </div>
            </div>

            <!-- Total Refunds -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-red-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-undo text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Refunds</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['total_refunds'], 2)); ?></p>
                </div>
            </div>

            <!-- Refundable -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-indigo-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-coins text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Refundable</p>
                    <p class="text-sm font-bold text-green-50"><?php echo e($stats['refundable_payments']); ?></p>
                </div>
            </div>

            <!-- Failed -->
            <div class="bg-gray-800 rounded-lg p-3 border border-gray-700 hover:border-gray-500 transition-colors">
                <div class="flex flex-col items-center text-center">
                    <div class="w-8 h-8 bg-gray-600 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-times-circle text-white text-sm"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mb-1">Failed</p>
                    <p class="text-sm font-bold text-green-50">₱<?php echo e(number_format($stats['failed_payments'], 2)); ?></p>
                </div>
            </div>
        </div>

        <!-- Filter Panel -->
        <div id="filterPanel" class="bg-gray-800 rounded-lg p-6 mb-8 border border-gray-700 hidden">
            <form method="GET" action="<?php echo e(route('admin.payments.index')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="processing" <?php echo e(request('status') === 'processing' ? 'selected' : ''); ?>>Processing</option>
                        <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                        <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                        <option value="refunded" <?php echo e(request('status') === 'refunded' ? 'selected' : ''); ?>>Refunded</option>
                        <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Methods</option>
                        <option value="cash" <?php echo e(request('payment_method') === 'cash' ? 'selected' : ''); ?>>Cash</option>
                        <option value="card" <?php echo e(request('payment_method') === 'card' ? 'selected' : ''); ?>>Credit/Debit Card</option>
                        <option value="bank_transfer" <?php echo e(request('payment_method') === 'bank_transfer' ? 'selected' : ''); ?>>Bank Transfer</option>
                        <option value="gcash" <?php echo e(request('payment_method') === 'gcash' ? 'selected' : ''); ?>>GCash</option>
                        <option value="paymaya" <?php echo e(request('payment_method') === 'paymaya' ? 'selected' : ''); ?>>PayMaya</option>
                        <option value="online" <?php echo e(request('payment_method') === 'online' ? 'selected' : ''); ?>>Online Payment</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Payment Type</label>
                    <select name="payment_type" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">All Types</option>
                        <option value="booking" <?php echo e(request('payment_type') === 'booking' ? 'selected' : ''); ?>>Room Bookings</option>
                        <option value="service" <?php echo e(request('payment_type') === 'service' ? 'selected' : ''); ?>>Services</option>
                        <option value="food_order" <?php echo e(request('payment_type') === 'food_order' ? 'selected' : ''); ?>>Food & Beverage</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date From</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Date To</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="flex flex-col justify-end">
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Apply
                        </button>
                        <a href="<?php echo e(route('admin.payments.index')); ?>" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center">
                            Clear
                        </a>
                    </div>
                </div>
            </form>

            <!-- Search Bar -->
            <div class="mt-4">
                <form method="GET" action="<?php echo e(route('admin.payments.index')); ?>">
                    <?php $__currentLoopData = request()->except('search'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search by payment reference, guest name, or email..." 
                               value="<?php echo e(request('search')); ?>"
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-semibold text-green-50">Payment Transactions</h3>
                <?php if($payments->count() > 0): ?>
                    <p class="text-sm text-gray-400 mt-2 sm:mt-0">
                        Showing <?php echo e($payments->firstItem()); ?> to <?php echo e($payments->lastItem()); ?> of <?php echo e($payments->total()); ?> results
                    </p>
                <?php endif; ?>
            </div>

            <?php if($payments->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Payment Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Type & Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Guest Info -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-green-50"><?php echo e($payment->user->name ?? 'N/A'); ?></div>
                                            <div class="text-sm text-gray-400"><?php echo e($payment->user->email ?? 'N/A'); ?></div>
                                            <?php if($payment->user->phone): ?>
                                                <div class="text-xs text-gray-500"><?php echo e($payment->user->phone); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Payment Details -->
                                <td class="px-6 py-4">
                                    <div class="font-medium text-blue-400"><?php echo e($payment->payment_reference); ?></div>
                                    <?php if($payment->transaction_id): ?>
                                        <div class="text-sm text-gray-400">TXN: <?php echo e($payment->transaction_id); ?></div>
                                    <?php endif; ?>
                                    <?php if($payment->notes): ?>
                                        <div class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-sticky-note mr-1"></i><?php echo e(Str::limit($payment->notes, 30)); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Type & Service -->
                                <td class="px-6 py-4">
                                    <?php if($payment->booking): ?>
                                        <div class="inline-flex items-start bg-blue-600 text-white px-3 py-2 rounded-lg">
                                            <div class="flex-shrink-0 mr-2">
                                                <i class="fas fa-bed text-white"></i>
                                            </div>
                                            <div class="text-xs">
                                                <div class="font-bold mb-1">Room Booking</div>
                                                <div class="opacity-90"><?php echo e($payment->booking->room->name ?? 'N/A'); ?></div>
                                                <div class="opacity-75 mt-0.5"><?php echo e($payment->booking->booking_reference); ?></div>
                                                <div class="opacity-75 text-[10px] mt-0.5">
                                                    <?php echo e($payment->booking->check_in ? $payment->booking->check_in->format('M d') : 'N/A'); ?> - 
                                                    <?php echo e($payment->booking->check_out ? $payment->booking->check_out->format('M d, Y') : 'N/A'); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif($payment->serviceRequest): ?>
                                        <div class="inline-flex items-start bg-purple-600 text-white px-3 py-2 rounded-lg">
                                            <div class="flex-shrink-0 mr-2">
                                                <i class="fas fa-concierge-bell text-white"></i>
                                            </div>
                                            <div class="text-xs">
                                                <div class="font-bold mb-1">Service</div>
                                                <div class="opacity-90"><?php echo e($payment->serviceRequest->service->name ?? 'Service Request'); ?></div>
                                                <div class="opacity-75 mt-0.5"><?php echo e($payment->serviceRequest->service->category ?? 'N/A'); ?></div>
                                            </div>
                                        </div>
                                    <?php elseif($payment->foodOrder): ?>
                                        <div class="inline-flex items-start bg-orange-600 text-white px-3 py-2 rounded-lg">
                                            <div class="flex-shrink-0 mr-2">
                                                <i class="fas fa-utensils text-white"></i>
                                            </div>
                                            <div class="text-xs">
                                                <div class="font-bold mb-1">Food & Beverage</div>
                                                <div class="opacity-90">Food Order #<?php echo e($payment->foodOrder->order_number); ?></div>
                                                <div class="opacity-75 mt-0.5"><?php echo e($payment->foodOrder->orderItems->count()); ?> item(s)</div>
                                                <div class="opacity-75 text-[10px] mt-0.5">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $payment->foodOrder->delivery_type))); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="inline-flex items-center bg-gray-600 text-white px-3 py-2 rounded-lg">
                                            <i class="fas fa-question-circle text-white mr-2"></i>
                                            <span class="text-xs font-bold">Other</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Amount -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-green-400">
                                        ₱<?php echo e(number_format($payment->calculated_amount, 2)); ?>

                                    </div>
                                    
                                    <!-- Show breakdown for bookings -->
                                    <?php if($payment->booking): ?>
                                        <div class="text-xs text-gray-400 mt-1">
                                            <?php if($payment->booking->room): ?>
                                                <?php
                                                    $checkIn = \Carbon\Carbon::parse($payment->booking->check_in_date);
                                                    $checkOut = \Carbon\Carbon::parse($payment->booking->check_out_date);
                                                    $nights = $checkIn->diffInDays($checkOut);
                                                    $roomCost = $payment->booking->room->price * $nights;
                                                ?>
                                                Room: ₱<?php echo e(number_format($payment->booking->room->price, 2)); ?> × <?php echo e($nights); ?> nights
                                                = ₱<?php echo e(number_format($roomCost, 2)); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if($payment->booking->additional_fees > 0): ?>
                                            <div class="text-xs text-blue-400">
                                                + ₱<?php echo e(number_format($payment->booking->additional_fees, 2)); ?> fees
                                            </div>
                                        <?php endif; ?>
                                        <?php if($payment->booking->discount_amount > 0): ?>
                                            <div class="text-xs text-yellow-400">
                                                - ₱<?php echo e(number_format($payment->booking->discount_amount, 2)); ?> discount
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <!-- Show breakdown for services -->
                                    <?php if($payment->serviceRequest && $payment->serviceRequest->service): ?>
                                        <?php
                                            $service = $payment->serviceRequest->service;
                                            $quantity = $payment->serviceRequest->quantity ?? 1;
                                            $serviceTotal = $service->price * $quantity;
                                        ?>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Service: <?php echo e($service->name); ?>

                                        </div>
                                        <div class="text-xs text-blue-400">
                                            ₱<?php echo e(number_format($service->price, 2)); ?>

                                            <?php if($quantity > 1): ?>
                                                × <?php echo e($quantity); ?> = ₱<?php echo e(number_format($serviceTotal, 2)); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if($payment->serviceRequest->service->duration): ?>
                                            <div class="text-xs text-gray-500">
                                                Duration: <?php echo e($payment->serviceRequest->service->duration); ?> min
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <!-- Show breakdown for food orders -->
                                    <?php if($payment->foodOrder): ?>
                                        <div class="text-xs text-gray-400 mt-1">
                                            <?php echo e($payment->foodOrder->orderItems->count()); ?> item(s)
                                        </div>
                                        <?php if($payment->foodOrder->subtotal): ?>
                                            <div class="text-xs text-orange-400">
                                                Subtotal: ₱<?php echo e(number_format($payment->foodOrder->subtotal, 2)); ?>

                                            </div>
                                        <?php endif; ?>
                                        <?php if($payment->foodOrder->delivery_fee > 0): ?>
                                            <div class="text-xs text-blue-400">
                                                + ₱<?php echo e(number_format($payment->foodOrder->delivery_fee, 2)); ?> delivery
                                            </div>
                                        <?php endif; ?>
                                        <?php if($payment->foodOrder->tax_amount > 0): ?>
                                            <div class="text-xs text-gray-500">
                                                + ₱<?php echo e(number_format($payment->foodOrder->tax_amount, 2)); ?> tax
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <!-- Show refund information -->
                                    <?php if($payment->refund_amount > 0): ?>
                                        <div class="text-sm text-red-400 mt-1">
                                            <i class="fas fa-minus-circle mr-1"></i>Refunded: ₱<?php echo e(number_format($payment->refund_amount, 2)); ?>

                                        </div>
                                        <div class="text-sm font-medium text-green-400">
                                            Net: ₱<?php echo e(number_format($payment->calculated_amount - ($payment->refund_amount ?? 0), 2)); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Method -->
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-700 text-gray-300 rounded border border-gray-600">
                                        <?php echo e($payment->payment_method_display); ?>

                                    </span>
                                    <?php if($payment->payment_date): ?>
                                        <div class="text-sm text-gray-400 mt-1">
                                            <?php echo e($payment->payment_date->format('M d, H:i')); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <?php
                                        $statusConfig = match($payment->status) {
                                            'completed' => ['bg' => 'bg-green-600', 'text' => 'text-white'],
                                            'pending' => ['bg' => 'bg-yellow-600', 'text' => 'text-white'],
                                            'processing' => ['bg' => 'bg-blue-600', 'text' => 'text-white'],
                                            'failed' => ['bg' => 'bg-red-600', 'text' => 'text-white'],
                                            'refunded' => ['bg' => 'bg-gray-600', 'text' => 'text-white'],
                                            'cancelled' => ['bg' => 'bg-gray-700', 'text' => 'text-gray-300'],
                                            default => ['bg' => 'bg-gray-700', 'text' => 'text-gray-300']
                                        };
                                    ?>
                                    <span class="inline-block px-2 py-1 text-xs <?php echo e($statusConfig['bg']); ?> <?php echo e($statusConfig['text']); ?> rounded">
                                        <?php echo e(ucfirst($payment->status)); ?>

                                    </span>
                                    <?php if($payment->isPartiallyRefunded()): ?>
                                        <div class="mt-1">
                                            <span class="inline-block px-2 py-1 text-xs bg-yellow-600 text-white rounded">Partial Refund</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Date -->
                                <td class="px-6 py-4">
                                    <div class="text-sm text-green-50"><?php echo e($payment->created_at->format('M d, Y')); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo e($payment->created_at->format('H:i A')); ?></div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <!-- View Details - Works for both bookings and services -->
                                        <a href="<?php echo e(route('admin.payments.show', $payment)); ?>" 
                                           class="inline-flex items-center px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" 
                                           title="View Payment Details">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        
                                        <!-- Refund Action - Works for both bookings and services -->
                                        <?php if($payment->canBeRefunded()): ?>
                                            <button onclick="showRefundModal(<?php echo e($payment->id); ?>, <?php echo e($payment->getRemainingRefundableAmount()); ?>)"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors" 
                                                    title="Process Refund">
                                                <i class="fas fa-undo mr-1"></i>Refund
                                            </button>
                                        <?php endif; ?>

                                        <!-- Mark as Complete - Works for both bookings and services -->
                                        <?php if($payment->status === 'pending'): ?>
                                            <button onclick="updatePaymentStatus(<?php echo e($payment->id); ?>, 'completed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors" 
                                                    title="Mark as Completed">
                                                <i class="fas fa-check mr-1"></i>Complete
                                            </button>
                                        <?php endif; ?>

                                        <!-- View Related Record - Booking or Service -->
                                        <?php if($payment->booking): ?>
                                            <a href="<?php echo e(route('admin.bookings.show', $payment->booking)); ?>" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors" 
                                               title="View Booking Details">
                                                <i class="fas fa-bed mr-1"></i>Booking
                                            </a>
                                            
                                            <!-- Invoice Actions for Booking Payments -->
                                            <?php if($payment->booking->invoice): ?>
                                                <a href="<?php echo e(route('invoices.show', $payment->booking->invoice)); ?>" 
                                                   class="inline-flex items-center px-2 py-1 text-xs bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors" 
                                                   title="View Invoice">
                                                    <i class="fas fa-file-invoice mr-1"></i>Invoice
                                                </a>
                                            <?php elseif($payment->booking->amount_paid > 0): ?>
                                                <form action="<?php echo e(route('invoices.generate', $payment->booking)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors w-full" 
                                                            title="Generate Invoice"
                                                            onclick="return confirm('Generate invoice for <?php echo e($payment->booking->booking_reference); ?>?');">
                                                        <i class="fas fa-file-invoice-dollar mr-1"></i>Generate Invoice
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php elseif($payment->serviceRequest): ?>
                                            <a href="<?php echo e(route('manager.service-requests.show', $payment->serviceRequest)); ?>" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors" 
                                               title="View Service Request Details">
                                                <i class="fas fa-concierge-bell mr-1"></i>Service
                                            </a>
                                        <?php elseif($payment->foodOrder): ?>
                                            <a href="<?php echo e(route('staff.orders.show', $payment->foodOrder)); ?>" 
                                               class="inline-flex items-center px-2 py-1 text-xs bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors" 
                                               title="View Food Order Details">
                                                <i class="fas fa-utensils mr-1"></i>Food Order
                                            </a>
                                        <?php endif; ?>

                                        <!-- Additional Actions for Processing Status -->
                                        <?php if($payment->status === 'processing'): ?>
                                            <button onclick="updatePaymentStatus(<?php echo e($payment->id); ?>, 'completed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors" 
                                                    title="Complete Payment">
                                                <i class="fas fa-check-circle mr-1"></i>Complete
                                            </button>
                                            <button onclick="updatePaymentStatus(<?php echo e($payment->id); ?>, 'failed')"
                                                    class="inline-flex items-center px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 transition-colors" 
                                                    title="Mark as Failed">
                                                <i class="fas fa-times-circle mr-1"></i>Failed
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-700 flex flex-col sm:flex-row items-center justify-between">
                    <div class="text-sm text-gray-400 mb-4 sm:mb-0">
                        Showing <?php echo e($payments->firstItem()); ?> to <?php echo e($payments->lastItem()); ?> of <?php echo e($payments->total()); ?> payments
                    </div>
                    <div class="flex-1 flex justify-end">
                        <?php echo e($payments->appends(request()->query())->links()); ?>

                    </div>
                </div>
            <?php else: ?>
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-50 mb-2">No payments found</h3>
                    <p class="text-gray-400">Try adjusting your search filters or check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-green-50">Process Refund</h3>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="refundForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Refund Amount</label>
                        <input type="number" name="refund_amount" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500" 
                               step="0.01" required>
                        <p class="text-xs text-gray-400 mt-1">Maximum: <span id="maxRefund"></span></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Refund Reason</label>
                        <textarea name="refund_reason" rows="3" 
                                  class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500" 
                                  placeholder="Please provide a reason for the refund..." required></textarea>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeRefundModal()" 
                            class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleFilterPanel() {
    const panel = document.getElementById('filterPanel');
    panel.classList.toggle('hidden');
}

function updatePaymentStatus(paymentId, status) {
    if (!status || !['pending', 'completed', 'failed', 'refunded', 'cancelled'].includes(status)) {
        console.error('Invalid payment status:', status);
        alert('Invalid payment status selected');
        return;
    }

    if (confirm('Are you sure you want to update this payment status to ' + status + '?')) {
        console.log('Updating payment status:', {
            paymentId: paymentId,
            status: status,
            action: `/admin/payments/${paymentId}/status`
        });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/payments/${paymentId}/status`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        const statusField = document.createElement('input');
        statusField.type = 'hidden';
        statusField.name = 'status';
        statusField.value = status;
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        form.appendChild(statusField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refundForm').action = `/admin/payments/${paymentId}/refund`;
    document.getElementById('maxRefund').textContent = `₱${maxAmount.toFixed(2)}`;
    document.querySelector('input[name="refund_amount"]').max = maxAmount;
    document.getElementById('refundModal').classList.remove('hidden');
}

function closeRefundModal() {
    document.getElementById('refundModal').classList.add('hidden');
    document.getElementById('refundForm').reset();
}

// Close modal when clicking outside
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});

// Generate Invoice Modal Functions
function openGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.remove('hidden');
}

function closeGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.add('hidden');
    document.getElementById('generateInvoiceForm').reset();
    document.getElementById('generateInvoiceForm').action = '';
    updateButtonState();
}

function updateFormAction() {
    const bookingSelect = document.getElementById('booking_id');
    const form = document.getElementById('generateInvoiceForm');
    
    if (bookingSelect.value) {
        form.action = `/bookings/${bookingSelect.value}/invoice/generate`;
    } else {
        form.action = '';
    }
    updateButtonState();
}

function updateButtonState() {
    const bookingSelect = document.getElementById('booking_id');
    const button = document.getElementById('generateButton');
    
    if (bookingSelect.value) {
        button.disabled = false;
        button.className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    } else {
        button.disabled = true;
        button.className = 'flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed';
    }
}

// Close modal when clicking outside
document.getElementById('generateInvoiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateInvoiceModal();
    }
});
</script>

<!-- Generate Invoice Modal -->
<div id="generateInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-green-50">Generate Invoice</h3>
                <button onclick="closeGenerateInvoiceModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="generateInvoiceForm" method="POST">
                <?php echo csrf_field(); ?>
                
                <!-- Booking Selection -->
                <div class="mb-4">
                    <label for="booking_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Select Booking
                    </label>
                    <select 
                        name="booking_id" 
                        id="booking_id" 
                        required 
                        onchange="updateFormAction()"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">Select a booking...</option>
                        <?php $__currentLoopData = \App\Models\Booking::with('room', 'user')->whereDoesntHave('invoice')->whereHas('payments')->orderBy('created_at', 'desc')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($booking->id); ?>">
                            <?php echo e($booking->booking_reference); ?> - <?php echo e($booking->room->name); ?> - <?php echo e($booking->user->name); ?>

                            (<?php echo e($booking->check_in->format('M d')); ?> - <?php echo e($booking->check_out->format('M d, Y')); ?>)
                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Only bookings with payments and without invoices are shown</p>
                </div>
                
                <!-- Due Date -->
                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-300 mb-2">
                        Due Date
                    </label>
                    <input 
                        type="date" 
                        name="due_date" 
                        id="due_date" 
                        value="<?php echo e(now()->addDays(7)->format('Y-m-d')); ?>"
                        min="<?php echo e(now()->format('Y-m-d')); ?>"
                        required 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                </div>
                
                <!-- Tax Rate -->
                <div class="mb-4">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-300 mb-2">
                        Tax Rate (%)
                    </label>
                    <input 
                        type="number" 
                        name="tax_rate" 
                        id="tax_rate" 
                        value="0" 
                        min="0" 
                        max="100" 
                        step="0.01"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                    <p class="text-xs text-gray-400 mt-1">Enter tax rate (e.g., 12 for 12% VAT)</p>
                </div>
                
                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Additional notes for this invoice..."
                    ></textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeGenerateInvoiceModal()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        id="generateButton"
                        disabled
                        class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed"
                    >
                        <i class="fas fa-file-invoice mr-2"></i>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/admin/payments/index.blade.php ENDPATH**/ ?>