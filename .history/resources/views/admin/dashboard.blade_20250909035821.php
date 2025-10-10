<x-admin-layout>
    <x-slot name="header">
        Admin Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Bookings Overview Card -->
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm overflow-hidden shadow-xl rounded-lg border border-green-700">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Recent Bookings</h3>
                <div class="space-y-4">
                    <p class="text-green-100">Total Active Bookings: {{ $activeBookingsCount ?? 0 }}</p>
                    <p class="text-green-100">Pending Approvals: {{ $pendingBookingsCount ?? 0 }}</p>
                    <a href="{{ route('admin.bookings') }}" class="text-green-300 hover:text-green-200 inline-flex items-center">
                        View all bookings
                        <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Management Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Rooms & Facilities -->
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-white mb-4">
                        Rooms & Facilities
                    </h3>
                    <p class="text-green-100 mb-6 flex-grow">
                        Update room availability, rates, and facility details.
                    </p>
                    <a href="{{ route('admin.rooms') }}" 
                       class="inline-block w-full py-3 bg-green-700 text-white font-medium rounded-lg hover:bg-green-600 transition-all duration-300 text-center">
                        Manage Rooms
                    </a>
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
