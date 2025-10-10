@extends('layouts.guest')

@section('content')
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
                Welcome Back, {{ auth()->user()->name }}
            </h1>
            <p class="text-xl text-gray-200 max-w-3xl mx-auto">
                Your perfect beachside getaway awaits. Browse our exclusive rooms and manage your bookings all in one place.
            </p>
        </div>

        <!-- Quick Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <!-- Browse Rooms Card -->
            <a href="{{ route('guest.rooms') }}" class="group">
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
            <a href="{{ route('guest.bookings') }}" class="group">
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

            <!-- Special Offers Card -->
            <a href="#" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Special Offers</h3>
                    <p class="text-green-100">Exclusive deals just for you</p>
                </div>
            </a>

            <!-- Support Card -->
            <a href="#" class="group">
                <div class="bg-green-900 border-2 border-green-700 rounded-xl p-6 text-center transform transition duration-300 hover:scale-105 hover:bg-green-800 cursor-pointer shadow-lg">
                    <div class="bg-green-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition-colors duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Support</h3>
                    <p class="text-green-100">We're here to help 24/7</p>
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
@endsection
