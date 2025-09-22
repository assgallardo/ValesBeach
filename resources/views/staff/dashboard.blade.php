@extends('layouts.admin')

@section('content')
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
                <!-- Logout Button -->
                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
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

                <!-- Reservations Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Bookings & Reservations
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            View and manage all resort reservations and bookings.
                        </p>
                        <a href="{{ route('admin.reservations') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Reservations
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection