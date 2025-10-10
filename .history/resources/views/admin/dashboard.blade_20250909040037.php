<x-admin-layout>
    <x-slot name="header">
        Admin Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Statistics Overview Card -->
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="lg:col-span-3 bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <h4 class="text-lg font-medium text-green-100 mb-2">Active Bookings</h4>
                    <p class="text-3xl font-bold text-white">{{ $activeBookingsCount ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <h4 class="text-lg font-medium text-green-100 mb-2">Pending Approvals</h4>
                    <p class="text-3xl font-bold text-white">{{ $pendingBookingsCount ?? 0 }}</p>
                </div>
                <div class="flex items-center justify-center">
                    <a href="{{ route('admin.bookings') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-700 text-white font-medium rounded-lg hover:bg-green-600 transition-all duration-300">
                        View All Bookings
                        <svg class="w-5 h-5 ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Rooms & Facilities -->
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
            <div class="text-center h-full flex flex-col justify-between">
                <div>
                    <h3 class="text-2xl font-semibold text-white mb-4">
                        Rooms & Facilities
                    </h3>
                    <p class="text-green-100 text-lg">
                        Update room availability, rates, and facility details.
                    </p>
                </div>
                <div class="mt-8">
                    <a href="{{ route('admin.rooms') }}" 
                       class="inline-block w-full py-3 bg-green-700 text-white font-medium rounded-lg hover:bg-green-600 transition-all duration-300 text-center">
                        Manage Rooms
                    </a>
                </div>
            </div>
        </div>
        @endif

            <!-- Bookings Management -->
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Bookings Management
                    </h3>
                    <p class="text-green-100 mb-6 flex-grow">
                        Oversee guest reservations, check-ins, and check-outs.
                    </p>
                    <a href="{{ route('admin.bookings') }}" 
                       class="inline-block w-full py-3 bg-green-700 text-white font-medium rounded-lg hover:bg-green-600 transition-all duration-300 text-center">
                        Manage Bookings
                    </a>
                </div>
            </div>
            @endif

            <!-- User Management -->
            @if(auth()->user()->hasRole('admin'))
            <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-white mb-4">
                        User Management
                    </h3>
                    <p class="text-green-100 mb-6 flex-grow">
                        Manage user accounts, permissions, and access controls.
                    </p>
                    <a href="{{ route('admin.users') }}" 
                       class="inline-block w-full py-3 bg-green-700 text-white font-medium rounded-lg hover:bg-green-600 transition-all duration-300 text-center">
                        Manage Users
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
