@extends('layouts.admin')

@section('content')
    <header class="bg-green-900 shadow">
        <div class="container mx-auto px-4 lg:px-16 py-6">
            <h2 class="text-2xl font-semibold text-white">
                Admin Dashboard
            </h2>
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
                    </div>
                </div>

                <!-- User Management (Admin & Manager Only) -->
                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            User Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Manage user accounts, permissions, and access controls.
                        </p>
                        <a href="{{ route('admin.users') }}" class="inline-block w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
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
@endsection
