<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Invoice'); ?> - Vales Beach Resort</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900" style="font-family: 'Poppins', sans-serif;">
    <!-- Minimal Header Navigation -->
    <nav class="bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex">
                    <a href="<?php echo e(auth()->user()->role === 'admin' ? route('admin.dashboard') : 
                        (auth()->user()->role === 'manager' ? route('manager.dashboard') : 
                        (auth()->user()->role === 'staff' ? route('staff.dashboard') : 
                        route('guest.dashboard')))); ?>" class="text-2xl font-bold text-white">
                        Vales Beach Resort
                    </a>
                </div>

                <!-- User Menu -->
                <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center space-x-6">
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
                                <?php echo e(strtoupper(auth()->user()->role)); ?>

                            </span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-700"
                             style="display: none;">
                            <a href="<?php echo e(auth()->user()->role === 'admin' ? route('admin.dashboard') : 
                                (auth()->user()->role === 'manager' ? route('manager.dashboard') : 
                                (auth()->user()->role === 'staff' ? route('staff.dashboard') : 
                                route('guest.dashboard')))); ?>" class="block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">
                                <i class="fas fa-home mr-2"></i>Dashboard
                            </a>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</body>
</html>

<?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/layouts/invoice.blade.php ENDPATH**/ ?>