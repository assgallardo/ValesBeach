<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vales Beach Resort</title>
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
                    <a href="/" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                        Home
                    </a>

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
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Dashboard Title -->
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50">
                    {{ ucfirst(auth()->user()->role) }} Dashboard
                </h2>
                <p class="text-green-50 opacity-80 text-lg mt-2">
                    Welcome back, {{ auth()->user()->name }}!
                </p>
            </div>

            <!-- Management Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 max-w-7xl mx-auto">
                
                <!-- Food Menu Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Food Menu Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Create, read, update, and delete food items available at the resort.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Menu
                        </button>
                    </div>
                </div>

                <!-- Rooms & Facilities -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Rooms & Facilities
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Update room availability, rates, and facility details.
                        </p>
                        <a href="{{ route('admin.rooms.index') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Rooms
                        </a>
                    </div>
                </div>

                <!-- Bookings Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Bookings Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            View and manage all resort bookings and reservations.
                        </p>
                        <a href="{{ route('admin.bookings') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Bookings
                        </a>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Bookings Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Oversee guest reservations, check-ins, and check-outs.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Bookings
                        </button>
                    </div>
                </div>

                <!-- User Management (Admin Only) -->
                @if(auth()->user()->role === 'admin')
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            User Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Manage user accounts, permissions, and access controls.
                        </p>
                        <a href="/admin/users" class="inline-block w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Users
                        </a>
                    </div>
                </div>
                @endif

                <!-- Reports & Analytics (Admin & Manager Only) -->
                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Reports & Analytics
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Access performance reports and guest insights.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            View Reports
                        </button>
                    </div>
                </div>

                <!-- Settings & Configuration (Admin & Manager Only) -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Settings & Configuration
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Configure system settings and resort preferences.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Settings
                        </button>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </main>
</body>
</html>
