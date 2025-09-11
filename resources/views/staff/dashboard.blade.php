@extends('layouts.staff')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Staff Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Rooms Management Card -->
        <a href="{{ route('staff.rooms.index') }}" class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 hover:bg-green-800/50 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Rooms & Facilities</h2>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-gray-300">View and manage room information</p>
        </a>

        <!-- Bookings Management Card -->
        <a href="{{ route('staff.bookings.index') }}" class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 hover:bg-green-800/50 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Bookings</h2>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-300">Manage reservations and check-ins</p>
        </a>

        <!-- Food Menu Management Card -->
        <a href="{{ route('staff.menu.index') }}" class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 hover:bg-green-800/50 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-white">Food Menu</h2>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-gray-300">Update food and beverage menu</p>
        </a>
    </div>
</div>
@endsection