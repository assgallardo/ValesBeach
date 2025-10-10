<x-admin-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Bookings Overview Card -->
        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Bookings</h3>
                <div class="space-y-4">
                    <!-- Add booking statistics here -->
                    <p class="text-gray-600">Total Active Bookings: {{ $activeBookingsCount ?? 0 }}</p>
                    <p class="text-gray-600">Pending Approvals: {{ $pendingBookingsCount ?? 0 }}</p>
                    <a href="{{ route('admin.bookings') }}" class="text-indigo-600 hover:text-indigo-900">View all bookings →</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics and Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 max-w-7xl mx-auto">
            <!-- Rooms & Facilities -->
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Rooms & Facilities
                    </h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow">
                        Update room availability, rates, and facility details.
                    </p>
                    <a href="{{ route('admin.rooms') }}" 
                       class="inline-block w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                        Manage Rooms
                    </a>
                </div>
            </div>
            @endif

            <!-- Bookings Management -->
            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        Bookings Management
                    </h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow">
                        Oversee guest reservations, check-ins, and check-outs.
                    </p>
                    <a href="{{ route('admin.bookings') }}" 
                       class="inline-block w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                        Manage Bookings
                    </a>
                </div>
            </div>
            @endif

            <!-- User Management -->
            @if(auth()->user()->hasRole('admin'))
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="text-center h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">
                        User Management
                    </h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow">
                        Manage user accounts, permissions, and access controls.
                    </p>
                    <a href="{{ route('admin.users') }}" 
                       class="inline-block w-full py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 text-center">
                        Manage Users
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
