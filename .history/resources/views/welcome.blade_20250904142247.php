<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Vales Beach Resort</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <!-- Background decorative blur elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
        <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
        <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
    </div>

    <!-- Header -->
    <header class="relative z-10 bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex items-center justify-between h-32">
                <!-- Resort Name -->
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-green-50">
                    VALES BEACH RESORT
                </h1>
                
                <!-- Navigation -->
                <div class="flex items-center space-x-6 lg:space-x-8">
                    @auth
                        <!-- User Profile & Logout -->
                        <div class="flex items-center space-x-4">
                            <span class="text-green-50 text-sm">{{ auth()->user()->name }}</span>
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-600 text-white">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-50 text-sm hover:text-green-200 transition-colors duration-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Login/Signup Links for Guests -->
                        <a href="{{ route('login') }}" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                            Login
                        </a>
                        <a href="{{ route('signup') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            @guest
                <!-- Welcome Section for Non-logged in Users -->
                <div class="text-center mb-12 lg:mb-16">
                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-green-50 mb-6">
                        Welcome to Paradise
                    </h2>
                    <p class="text-green-50 opacity-80 text-lg lg:text-xl mb-8 max-w-2xl mx-auto">
                        Experience luxury and comfort at Vales Beach Resort. Your perfect getaway awaits with stunning ocean views, world-class amenities, and unforgettable memories.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('signup') }}" class="px-8 py-4 bg-green-600 text-white font-semibold text-lg rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition duration-200 ease-in-out transform hover:scale-105">
                            Book Your Stay
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-green-600 text-green-50 font-semibold text-lg rounded-lg hover:bg-green-600 hover:text-white transition duration-200 ease-in-out">
                            Member Login
                        </a>
                    </div>
                </div>

                <!-- Features Preview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-green-50 mb-2">Luxury Rooms</h3>
                        <p class="text-green-50 opacity-70">Comfortable accommodations with ocean views</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-green-50 mb-2">Fine Dining</h3>
                        <p class="text-green-50 opacity-70">Exquisite cuisine from around the world</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-green-50 mb-2">Premium Services</h3>
                        <p class="text-green-50 opacity-70">Spa, recreation, and concierge services</p>
                    </div>
                </div>
            @else
                @if(auth()->user()->role === 'guest')
                    <!-- Guest Dashboard -->
                    <div class="text-center mb-12 lg:mb-16">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                            Welcome back, {{ auth()->user()->name }}!
                        </h2>
                        <p class="text-green-50 opacity-80 text-lg">
                            What would you like to do today?
                        </p>
                    </div>

                    <!-- Guest Service Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
                        
                        <!-- Book a Room -->
                        <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <div class="text-center h-full flex flex-col">
                                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                                    Book a Room
                                </h3>
                                <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                                    Reserve your perfect accommodation with stunning ocean views and luxury amenities.
                                </p>
                                <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                                    View Available Rooms
                                </button>
                            </div>
                        </div>

                        <!-- Order Food -->
                        <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <div class="text-center h-full flex flex-col">
                                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                                    Order Food
                                </h3>
                                <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                                    Explore our gourmet menu and order delicious meals delivered to your room.
                                </p>
                                <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                                    View Menu
                                </button>
                            </div>
                        </div>

                        <!-- Other Services -->
                        <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <div class="text-center h-full flex flex-col">
                                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                                    Other Services
                                </h3>
                                <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                                    Access spa treatments, recreational activities, and concierge services.
                                </p>
                                <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                                    Browse Services
                                </button>
                            </div>
                        </div>

                    </div>
                @else
                    <!-- Staff/Manager/Admin Dashboard Link -->
                    <div class="text-center mb-12 lg:mb-16">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                            Welcome back, {{ auth()->user()->name }}!
                        </h2>
                        <p class="text-green-50 opacity-80 text-lg mb-8">
                            Access your {{ ucfirst(auth()->user()->role) }} dashboard to manage operations.
                        </p>
                        
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-green-600 text-white font-semibold text-lg rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition duration-200 ease-in-out transform hover:scale-105">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            {{ ucfirst(auth()->user()->role) }} Dashboard
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </main>
</body>
</html>
