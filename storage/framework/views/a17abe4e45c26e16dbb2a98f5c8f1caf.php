<?php $__env->startSection('title', 'Order #' . $foodOrder->order_number . ' - ValesBeach Resort'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
        <!-- Success Message -->
        <?php if(session('success')): ?>
        <div class="bg-green-900/50 border border-green-600 text-green-200 px-6 py-4 rounded-lg mb-6 flex items-start">
            <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <div>
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline ml-2"><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2">Order #<?php echo e($foodOrder->order_number); ?></h1>
                <p class="text-gray-400">Placed on <?php echo e($foodOrder->created_at->format('M j, Y \a\t g:i A')); ?></p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <a href="<?php echo e(route('guest.food-orders.orders')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>All Orders</span>
                </a>
                
                <?php if($foodOrder->status === 'pending'): ?>
                <form action="<?php echo e(route('guest.food-orders.cancel', $foodOrder)); ?>" method="POST" 
                      onsubmit="return confirm('Are you sure you want to cancel this order?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Order
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Status -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Order Status
                    </h2>
                    
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            <?php
                            $statusConfig = [
                                'pending' => ['color' => 'yellow', 'icon' => 'clock'],
                                'confirmed' => ['color' => 'blue', 'icon' => 'check-circle'],
                                'preparing' => ['color' => 'blue', 'icon' => 'fire'],
                                'ready' => ['color' => 'purple', 'icon' => 'bell'],
                                'delivered' => ['color' => 'green', 'icon' => 'check-circle'],
                                'completed' => ['color' => 'green', 'icon' => 'check-circle'],
                                'cancelled' => ['color' => 'red', 'icon' => 'x-circle']
                            ];
                            $config = $statusConfig[$foodOrder->status] ?? $statusConfig['pending'];
                            ?>
                            
                            <div class="w-16 h-16 bg-<?php echo e($config['color']); ?>-900 rounded-full flex items-center justify-center">
                                <?php if($config['icon'] === 'clock'): ?>
                                <svg class="w-8 h-8 text-<?php echo e($config['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                                <?php elseif($config['icon'] === 'check-circle'): ?>
                                <svg class="w-8 h-8 text-<?php echo e($config['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?php elseif($config['icon'] === 'fire'): ?>
                                <svg class="w-8 h-8 text-<?php echo e($config['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                </svg>
                                <?php elseif($config['icon'] === 'bell'): ?>
                                <svg class="w-8 h-8 text-<?php echo e($config['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <?php else: ?>
                                <svg class="w-8 h-8 text-<?php echo e($config['color']); ?>-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-bold text-white capitalize mb-2"><?php echo e(str_replace('_', ' ', $foodOrder->status)); ?></h3>
                            <p class="text-gray-300 text-sm">
                                <?php switch($foodOrder->status):
                                    case ('pending'): ?>
                                        We've received your order and it's being processed
                                        <?php break; ?>
                                    <?php case ('confirmed'): ?>
                                        Your order has been confirmed and will start preparation soon
                                        <?php break; ?>
                                    <?php case ('preparing'): ?>
                                        Our chefs are preparing your delicious meal
                                        <?php break; ?>
                                    <?php case ('ready'): ?>
                                        Your order is ready for <?php echo e($foodOrder->delivery_type === 'room_service' ? 'delivery' : 'pickup'); ?>

                                        <?php break; ?>
                                    <?php case ('delivered'): ?>
                                        Your order has been delivered. Enjoy your meal!
                                        <?php break; ?>
                                    <?php case ('completed'): ?>
                                        Your order has been completed. Enjoy your meal!
                                        <?php break; ?>
                                    <?php case ('cancelled'): ?>
                                        This order has been cancelled
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="mt-8 p-4 bg-gray-700/30 rounded-lg">
                        <h3 class="text-white font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Order Timeline
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-white font-medium flex-1">Order Placed</span>
                                <span class="text-gray-400"><?php echo e($foodOrder->created_at->format('g:i A')); ?></span>
                            </div>
                            
                            <?php if($foodOrder->confirmed_at): ?>
                            <div class="flex items-center text-sm">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-white font-medium flex-1">Order Confirmed</span>
                                <span class="text-gray-400"><?php echo e($foodOrder->confirmed_at->format('g:i A')); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($foodOrder->prepared_at): ?>
                            <div class="flex items-center text-sm">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-white font-medium flex-1">Preparation Started</span>
                                <span class="text-gray-400"><?php echo e($foodOrder->prepared_at->format('g:i A')); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($foodOrder->delivered_at): ?>
                            <div class="flex items-center text-sm">
                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                <span class="text-white font-medium flex-1">
                                    <?php echo e($foodOrder->delivery_type === 'room_service' ? 'Delivered' : 'Ready for Pickup'); ?>

                                </span>
                                <span class="text-gray-400"><?php echo e($foodOrder->delivered_at->format('g:i A')); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Order Items
                    </h2>
                    
                    <div class="space-y-4">
                        <?php $__currentLoopData = $foodOrder->orderItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start space-x-4 p-4 bg-gray-700/30 rounded-lg hover:bg-gray-700/50 transition-colors">
                            <!-- Item Image -->
                            <div class="flex-shrink-0">
                                <?php if($item->menuItem->image): ?>
                                <img src="<?php echo e(asset('storage/' . $item->menuItem->image)); ?>" 
                                     alt="<?php echo e($item->menuItem->name); ?>" 
                                     class="w-20 h-20 object-cover rounded-lg shadow-lg">
                                <?php else: ?>
                                <div class="w-20 h-20 bg-gray-700 rounded-lg flex items-center justify-center shadow-lg">
                                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-lg text-white mb-1"><?php echo e($item->menuItem->name); ?></h3>
                                <?php if($item->menuItem->description): ?>
                                <p class="text-gray-300 text-sm mb-2"><?php echo e($item->menuItem->description); ?></p>
                                <?php endif; ?>
                                
                                <!-- Dietary Badges -->
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <?php $__currentLoopData = $item->menuItem->dietary_badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo e($badge['class']); ?>">
                                        <?php echo e($badge['label']); ?>

                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                
                                <?php if($item->special_instructions): ?>
                                <div class="mt-2 p-3 bg-yellow-900/30 border border-yellow-600/50 rounded-lg">
                                    <p class="text-sm text-yellow-200">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <strong>Special Instructions:</strong> <?php echo e($item->special_instructions); ?>

                                    </p>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Quantity and Price -->
                            <div class="text-right flex-shrink-0">
                                <div class="text-sm text-gray-400 mb-1">
                                    Qty: <?php echo e($item->quantity); ?>

                                </div>
                                <div class="text-sm text-gray-400 mb-2">
                                    <?php echo e($item->formatted_price); ?> each
                                </div>
                                <div class="text-xl font-bold text-green-400">
                                    <?php echo e($item->formatted_total); ?>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-xl shadow-2xl p-6 sticky top-4 border border-gray-700">
                    <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Order Summary
                    </h2>
                    
                    <!-- Delivery Information -->
                    <div class="mb-6 p-4 bg-gray-700/30 rounded-lg">
                        <h3 class="font-bold text-white mb-3 flex items-center">
                            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Delivery Details
                        </h3>
                        <div class="text-sm text-gray-300 space-y-2">
                            <p><strong class="text-white">Type:</strong> <?php echo e(str_replace('_', ' ', ucfirst($foodOrder->delivery_type))); ?></p>
                            <?php if($foodOrder->delivery_location): ?>
                            <p><strong class="text-white">Location:</strong> <?php echo e($foodOrder->delivery_location); ?></p>
                            <?php endif; ?>
                            <?php if($foodOrder->requested_delivery_time): ?>
                            <p><strong class="text-white">Requested Time:</strong> <?php echo e($foodOrder->requested_delivery_time->format('M j, g:i A')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($foodOrder->special_instructions): ?>
                    <div class="mb-6 p-4 bg-yellow-900/30 border border-yellow-600/50 rounded-lg">
                        <h3 class="font-bold text-yellow-200 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Special Instructions
                        </h3>
                        <p class="text-sm text-yellow-100"><?php echo e($foodOrder->special_instructions); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Pricing -->
                    <div class="border-t border-gray-700 pt-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Subtotal</span>
                            <span class="font-semibold text-white"><?php echo e($foodOrder->formatted_subtotal); ?></span>
                        </div>
                        <?php if($foodOrder->delivery_fee > 0): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Delivery Fee</span>
                            <span class="font-semibold text-white"><?php echo e($foodOrder->formatted_delivery_fee); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Tax</span>
                            <span class="font-semibold text-white"><?php echo e($foodOrder->formatted_tax_amount); ?></span>
                        </div>
                        <div class="border-t border-gray-700 pt-3">
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-900/50 to-green-800/50 rounded-lg">
                                <span class="text-lg font-bold text-white">Total</span>
                                <span class="text-2xl font-bold text-green-400"><?php echo e($foodOrder->formatted_total_amount); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Status -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between p-3 bg-gray-700/30 rounded-lg">
                            <span class="text-gray-300 font-medium">Payment Status</span>
                            <span class="px-3 py-1.5 text-sm rounded-full font-bold
                                <?php switch($foodOrder->payment_status):
                                    case ('pending'): ?> bg-yellow-600 text-yellow-100 <?php break; ?>
                                    <?php case ('paid'): ?> bg-green-600 text-green-100 <?php break; ?>
                                    <?php case ('failed'): ?> bg-red-600 text-red-100 <?php break; ?>
                                    <?php case ('refunded'): ?> bg-gray-600 text-gray-100 <?php break; ?>
                                <?php endswitch; ?>">
                                <?php echo e(ucfirst($foodOrder->payment_status)); ?>

                            </span>
                        </div>
                    </div>
                    
                    <?php if($foodOrder->booking): ?>
                    <div class="mt-6 p-4 bg-blue-900/30 border border-blue-600/50 rounded-lg">
                        <h3 class="font-bold text-blue-200 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Associated Booking
                        </h3>
                        <p class="text-blue-100 text-sm space-y-1">
                            <span class="block"><strong>Booking:</strong> <?php echo e($foodOrder->booking->booking_number); ?></span>
                            <?php if($foodOrder->booking->room): ?>
                            <span class="block"><strong>Room:</strong> <?php echo e($foodOrder->booking->room->room_number); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/food-orders/show.blade.php ENDPATH**/ ?>