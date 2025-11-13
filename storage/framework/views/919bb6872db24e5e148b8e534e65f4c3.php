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
    
                        <?php endif; ?>
                        
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
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-gray-200 hover:text-white transition-colors duration-200">Login</a>
                        <a href="<?php echo e(route('signup')); ?>" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">Sign Up</a>
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
<?php /**PATH C:\Users\sethy\ValesBeach\resources\views\layouts\guest.blade.php ENDPATH**/ ?>