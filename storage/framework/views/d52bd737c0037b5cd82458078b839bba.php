<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>
        <?php if(auth()->user()->role === 'admin'): ?>
            Admin Dashboard - Vales Beach Resort
        <?php elseif(auth()->user()->role === 'manager'): ?>
            Manager Dashboard - Vales Beach Resort
        <?php else: ?>
            Staff Dashboard - Vales Beach Resort
        <?php endif; ?>
    </title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900" style="font-family: 'Poppins', sans-serif;">
    <!-- Top Navigation Bar -->
    <nav class="bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex">
                    <a href="<?php echo e(auth()->user()->role === 'admin' ? route('admin.dashboard') : 
                        (auth()->user()->role === 'manager' ? route('manager.dashboard') : route('staff.dashboard'))); ?>" class="text-2xl font-bold text-white hover:text-gray-200 transition-colors">
                        Vales Beach Resort
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                <span><?php echo e(Auth::user()->name); ?></span>
                                <!-- Role Indicator Badge -->
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                    <?php if(auth()->user()->role === 'admin'): ?>
                                        bg-green-600 text-white
                                    <?php elseif(auth()->user()->role === 'manager'): ?>
                                        bg-blue-600 text-white
                                    <?php elseif(auth()->user()->role === 'staff'): ?>
                                        bg-yellow-600 text-white
                                    <?php else: ?>
                                        bg-gray-600 text-white
                                    <?php endif; ?>">
                                    <?php echo e(strtoupper(auth()->user()->role ?? 'STAFF')); ?>

                                </span>
                                <svg class="w-5 h-5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 style="display: none;"
                                 class="absolute right-0 mt-3 w-40 rounded-lg shadow-2xl z-50 overflow-hidden">
                                
                                <!-- Logout Button -->
                                <form method="POST" action="<?php echo e(route('logout')); ?>" class="block">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit"
                                            class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-700 transition-colors duration-200 cursor-pointer">
                                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 min-h-screen">
            <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-700">
                <a href="<?php echo e(auth()->user()->role === 'admin' ? route('admin.dashboard') : 
                    (auth()->user()->role === 'manager' ? route('manager.dashboard') : route('staff.dashboard'))); ?>" class="text-xl font-bold text-white hover:text-gray-200 transition-colors">
                    Vales Beach Resort
                </a>
            </div>
            
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="<?php echo e(route('staff.dashboard')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('staff.dashboard') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="<?php echo e(route('admin.rooms.index')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('admin.rooms.*') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Rooms & Facilities
                    </a>

                    <a href="<?php echo e(route('admin.reservations')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('admin.reservations') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Bookings
                    </a>

                    <a href="<?php echo e(route('admin.calendar')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('admin.calendar') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Calendar
                    </a>

                    <!-- Food Management Section -->
                    <div class="pt-4 pb-2">
                        <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Food Management</span>
                    </div>

                    <a href="<?php echo e(route('staff.menu.index')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('staff.menu.*') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Menu Management
                    </a>

                    <a href="<?php echo e(route('staff.orders.index')); ?>"
                       class="flex items-center px-4 py-2 rounded-lg <?php echo e(request()->routeIs('staff.orders.*') ? 'bg-green-600' : 'hover:bg-gray-700'); ?> text-white transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Food Orders
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <main>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\sethy\ValesBeach\resources\views\layouts\staff.blade.php ENDPATH**/ ?>