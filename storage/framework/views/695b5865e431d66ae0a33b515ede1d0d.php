<?php $__env->startSection('content'); ?>
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<!-- Hero Section -->
<div class="relative z-10 py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                Welcome Back, <?php echo e(auth()->user()->name); ?>

            </h1>
            <p class="text-xl text-gray-200 max-w-3xl mx-auto">
                Your perfect beachside getaway awaits. Browse our exclusive rooms and manage your bookings all in one place.
            </p>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-16">
            <!-- Browse Rooms Card -->
            <a href="<?php echo e(route('guest.rooms.browse')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Browse Rooms</h3>
                    <p class="text-green-100">Explore our luxurious accommodations</p>
                </div>
            </a>

            <!-- My Bookings Card -->
            <a href="<?php echo e(route('guest.bookings')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">My Bookings</h3>
                    <p class="text-green-100">View and manage your reservations</p>
                </div>
            </a>

            <!-- Booking History Card -->
            <a href="<?php echo e(route('guest.bookings.history')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Booking History</h3>
                    <p class="text-green-100">View your past reservations</p>
                </div>
            </a>

            <!-- Payment History Card -->
            <a href="<?php echo e(route('payments.history')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Payment History</h3>
                    <p class="text-green-100">View your payment transactions</p>
                </div>
            </a>

            <!-- Invoices Card -->
            <a href="<?php echo e(route('invoices.index')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Invoices</h3>
                    <p class="text-green-100">View and download invoices</p>
                </div>
            </a>

            <!-- Resort Services Card -->
                        <!-- Resort Services Card -->
            <a href="<?php echo e(route('guest.services.index')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Resort Services</h3>
                    <p class="text-green-100">Spa, dining, and activity services</p>
                </div>
            </a>

            <!-- Food Ordering Card -->
            <a href="<?php echo e(route('guest.food-orders.menu')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16l-1 10a2 2 0 01-2 2H7a2 2 0 01-2-2L4 6zM4 6l-1-2m5 5v6m4-6v6m4-6v6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Food Ordering</h3>
                    <p class="text-green-100">Delicious meals delivered to your room</p>
                </div>
            </a>

            <!-- Service Requests Card -->
            <a href="<?php echo e(route('guest.services.history')); ?>" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="text-green-100 text-3xl mb-4">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Service Requests</h3>
                    <p class="text-green-200 text-sm">View your service request history</p>
                </div>
            </a>
        </div>

        <!-- Latest Updates Section -->
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-green-50 mb-8 text-center">Latest Updates</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Promotion Card -->
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-8 text-center shadow-lg">
                    <div class="bg-green-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-white mb-4">Weekday Special</h3>
                    <p class="text-green-100 mb-6">Get 20% off on weekday bookings this month!</p>
                    <a href="#" class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-500 transition-colors duration-200">
                        Learn More
                    </a>
                </div>

                <!-- New Rooms Card -->
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-8 text-center shadow-lg">
                    <div class="bg-green-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-white mb-4">Coming Soon</h3>
                    <p class="text-green-100 mb-6">New beachfront villas opening next month!</p>
                    <a href="#" class="inline-block px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-500 transition-colors duration-200">
                        Get Notified
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/guest/dashboard.blade.php ENDPATH**/ ?>