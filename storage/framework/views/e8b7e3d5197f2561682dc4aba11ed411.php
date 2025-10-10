<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Vales Beach Resort')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <!-- Add Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <nav class="relative z-10 bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex justify-between items-center h-16">
                <div class="flex">
                    <a href="/" class="text-2xl font-bold text-white">
                        Vales Beach
                    </a>
                </div>

                <div class="flex items-center space-x-6">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->role === 'guest'): ?>
                            <a href="<?php echo e(route('guest.dashboard')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">
                                Dashboard
                            </a>
                            <a href="<?php echo e(route('guest.rooms.browse')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">
                                Browse Rooms
                            </a>
                            <a href="<?php echo e(route('guest.services.index')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">
                                Services
                            </a>
                            <a href="<?php echo e(route('guest.food-orders.menu')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">
                                Food Menu
                            </a>
                            <a href="<?php echo e(route('guest.bookings')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">
                                My Bookings
                            </a>
                        <?php endif; ?>
                        
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors duration-200">
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
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 class="absolute right-0 mt-2 w-48 bg-green-800 rounded-lg shadow-xl py-1 border border-green-700">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-green-700 transition-colors duration-200">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-gray-600 hover:text-gray-900">Login</a>
                        <a href="<?php echo e(route('signup')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <p>Email: info@valesbeach.com</p>
                    <p>Phone: (123) 456-7890</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-gray-300">About Us</a></li>
                        <li><a href="#" class="hover:text-gray-300">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-gray-300">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-gray-300"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="hover:text-gray-300"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-gray-300"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center text-gray-400">
                <p>&copy; <?php echo e(date('Y')); ?> Vales Beach Resort. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/layouts/guest.blade.php ENDPATH**/ ?>