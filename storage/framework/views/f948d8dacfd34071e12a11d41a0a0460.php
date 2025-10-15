<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Manager Dashboard - Vales Beach Resort</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <!-- Manager Navigation -->
    <nav class="relative z-10 bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex">
                    <a href="<?php echo e(route('manager.dashboard')); ?>" class="text-2xl font-bold text-white">
                        ValesBeach Manager
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-6">
                    <div class="hidden md:block">
                        <div class="flex items-center space-x-4">
                            <!-- Dashboard link -->
                            <a href="<?php echo e(route('manager.dashboard')); ?>" 
                               class="text-gray-200 hover:text-white transition-colors duration-200 font-medium <?php echo e(request()->routeIs('manager.dashboard') ? 'text-white' : ''); ?>">
                                Dashboard
                            </a>
                            
                            <!-- Service Requests link -->
                            <a href="<?php echo e(route('manager.service-requests.index')); ?>" 
                               class="text-gray-200 hover:text-white transition-colors duration-200 font-medium <?php echo e(request()->routeIs('manager.service-requests.*') ? 'text-white' : ''); ?>">
                                Service Requests
                            </a>
                            
                            <!-- Reports link -->
                            <a href="<?php echo e(route('manager.reports.index')); ?>" 
                               class="text-gray-200 hover:text-white transition-colors duration-200 font-medium <?php echo e(request()->routeIs('manager.reports.*') ? 'text-white' : ''); ?>">
                                Reports
                            </a>
                            
                            <!-- Calendar Function - FIXED -->
                            <a href="<?php echo e(route('manager.calendar')); ?>" 
                               class="inline-flex items-center px-6 py-3 bg-green-50 text-black font-medium rounded-lg hover:bg-white transition-colors duration-200 <?php echo e(request()->routeIs('manager.calendar') ? 'bg-white' : ''); ?>">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Calendar
                            </a>
                        </div>
                    </div>

                    <!-- User Menu - LOGOUT ONLY -->
                    <div class="flex items-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    @mouseenter="open = true"
                                    @mouseleave="setTimeout(() => { if (!$refs.dropdown.matches(':hover')) open = false }, 100)"
                                    class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                <span><?php echo e(Auth::user()->name); ?></span>
                                <!-- Role Indicator Badge -->
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-600 text-white">
                                    <?php echo e(strtoupper(auth()->user()->role)); ?>

                                </span>
                                <svg class="w-5 h-5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown - LOGOUT ONLY -->
                            <div x-show="open" 
                                 x-ref="dropdown"
                                 @mouseenter="open = true"
                                 @mouseleave="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-40 bg-green-800 rounded-lg shadow-xl border border-green-700 z-50">
                                
                                <!-- Logout link ONLY -->
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="block px-4 py-3 text-sm text-gray-200 hover:bg-green-700 hover:text-white transition-colors duration-100 font-medium rounded-lg">
                                    <svg class="w-4 h-4 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </a>
                                
                                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="hidden">
                                    <?php echo csrf_field(); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <?php echo $__env->yieldContent('content'); ?>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/layouts/manager.blade.php ENDPATH**/ ?>