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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Reservations Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Reservations Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            View, create, and manage all guest bookings and reservations.
                        </p>
                        <a href="{{ route('manager.bookings.index') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Bookings
                        </a>
                    </div>
                </div>

                <!-- Services Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Services Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Manage resort services, amenities, and guest requests.
                        </p>
                        <a href="{{ route('manager.services.index') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Services
                        </a>
                    </div>
                </div>

                <!-- Rooms Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Rooms & Facilities
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Update room availability, rates, and facility details.
                        </p>
                        <a href="{{ route('manager.rooms') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Rooms
                        </a>
                    </div>
                </div>

                <!-- Staff Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Staff Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Monitor staff performance and manage team schedules.
                        </p>
                        <a href="{{ route('manager.staff') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Staff
                        </a>
                    </div>
                </div>

                <!-- Guests Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Guests Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            View guest information and booking history.
                        </p>
                        <a href="{{ route('manager.guests') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Guests
                        </a>
                    </div>
                </div>

                <!-- Reports & Analytics -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Reports & Analytics
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            View detailed reports and performance analytics.
                        </p>
                        <a href="{{ route('manager.reports.index') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            View Reports
                        </a>
                    </div>
                </div>

                <!-- Maintenance Management -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Maintenance Management
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-8 flex-grow">
                            Track maintenance requests and facility status.
                        </p>
                        <a href="{{ route('manager.maintenance') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Maintenance
                        </a>
                    </div>
                </div>

                <!-- Task Assignment - REDESIGNED TO MATCH OTHER CARDS -->
                <div class="bg-green-800 rounded-lg p-6 lg:p-8 hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <div class="text-center h-full flex flex-col">
                        <div class="mb-4">
                            <svg class="w-12 h-12 mx-auto text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl lg:text-3xl font-bold text-white mb-6 leading-tight">
                            Task Assignment
                        </h3>
                        <p class="text-green-100 text-sm lg:text-base mb-4 flex-grow">
                            Assign service requests to staff members and monitor progress.
                        </p>
                        
                        @php
                            $pendingAssignments = \App\Models\ServiceRequest::whereIn('status', ['pending', 'confirmed'])->count();
                        @endphp
                        
                        <!-- Pending Assignments Status Marker -->
                        @if($pendingAssignments > 0)
                            <div class="mb-4 bg-yellow-500/20 border border-yellow-500/30 rounded-lg p-3">
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <span class="text-yellow-400 text-sm font-medium">
                                        {{ $pendingAssignments }} pending assignment{{ $pendingAssignments > 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="mb-4 bg-green-500/20 border border-green-500/30 rounded-lg p-3">
                                <div class="flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-green-400 text-sm font-medium">
                                        All tasks assigned
                                    </span>
                                </div>
                            </div>
                        @endif
                        
                        <a href="{{ route('manager.staff-assignment.index') }}" 
                           class="w-full py-3 bg-green-50 text-black font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200 text-center">
                            Manage Assignments
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </main>
@endsection