@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Manage Reservations</h1>
                <p class="text-gray-400 mt-2">View and manage all resort reservations</p>
            </div>
            @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
            <div class="flex space-x-3">
                <!-- Quick Room Selection for Booking with Search -->
                <div x-data="{ 
                    open: false, 
                    searchQuery: '',
                    clearSearch() {
                        this.searchQuery = '';
                        this.$refs.searchInput.focus();
                    }
                }" class="relative">
                    <button @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()); }" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Quick Book Room
                        <svg class="w-4 h-4 ml-2 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-gray-800 rounded-lg shadow-xl z-50"
                         style="display: none;">
                        
                        <!-- Search Input -->
                        <div class="p-3 border-b border-gray-700">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input x-ref="searchInput"
                                       x-model="searchQuery"
                                       type="text"
                                       placeholder="Search facilities..."
                                       class="w-full pl-10 pr-10 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button x-show="searchQuery.length > 0"
                                        @click="clearSearch()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Room List -->
                        <div class="max-h-64 overflow-y-auto">
                            @php
                                $availableRooms = \App\Models\Room::where('is_available', true)->orderBy('category')->orderBy('name')->get();
                            @endphp
                            @foreach($availableRooms as $room)
                                <a href="{{ route('admin.reservations.createFromRoom', $room) }}" 
                                   x-show="searchQuery === '' || '{{ strtolower($room->name) }} {{ strtolower($room->category ?? '') }} {{ strtolower($room->type ?? '') }}'.includes(searchQuery.toLowerCase())"
                                   class="block px-4 py-3 text-sm text-gray-300 hover:bg-gray-700 hover:text-white border-b border-gray-700 last:border-b-0 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-white">{{ $room->name }}</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    ₱{{ number_format((float)$room->price, 2) }}/night
                                                </span>
                                                <span class="mx-2">•</span>
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    {{ $room->capacity }} guests
                                                </span>
                                            </div>
                                            @if($room->category)
                                            <div class="mt-1">
                                                <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                                    {{ $room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : '' }}
                                                    {{ $room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : '' }}
                                                    {{ $room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : '' }}">
                                                    {{ $room->category }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                            @endforeach
                            @if($availableRooms->isEmpty())
                                <div class="px-4 py-8 text-sm text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    No available facilities
                                </div>
                            @endif
                            <div x-show="searchQuery !== '' && !{{ $availableRooms->count() > 0 ? 'true' : 'false' }}" 
                                 class="px-4 py-8 text-sm text-center text-gray-400"
                                 style="display: none;">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                No facilities match your search
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Original Manual Booking -->
                <a href="{{ route('admin.reservations.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Manual Reservation
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-800 border border-green-600 text-green-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="bg-red-800 border border-red-600 text-red-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
    <div class="bg-red-800 border border-red-600 text-red-100 px-6 py-4 rounded-lg mb-8">
        <div class="font-bold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form action="{{ route('admin.reservations') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       value="{{ request('search') }}"
                       placeholder="Guest name, email, or room..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select name="status" id="status" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Room Filter -->
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-300 mb-2">Room</label>
                <select name="room_id" id="room_id" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Rooms</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-300 mb-2">Check-in From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-300 mb-2">Check-out To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.reservations') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Reservations</p>
                    <p class="text-2xl font-bold">{{ $bookings->total() }}</p>
                </div>
                <div class="bg-blue-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Active Bookings</p>
                    <p class="text-2xl font-bold">{{ $bookings->where('status', 'confirmed')->count() + $bookings->where('status', 'checked_in')->count() }}</p>
                </div>
                <div class="bg-green-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Completed</p>
                    <p class="text-2xl font-bold">{{ $bookings->where('status', 'completed')->count() }}</p>
                </div>
                <div class="bg-purple-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Cancelled</p>
                    <p class="text-2xl font-bold">{{ $bookings->where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="bg-red-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Room/Cottage Bookings -->
    <div x-data="{ activeTab: '{{ request('type', 'all') }}' }" class="mb-8">
        <!-- Tab Navigation -->
        <div class="bg-gray-800 rounded-t-lg">
            <div class="flex border-b border-gray-700 overflow-x-auto">
                <button @click="activeTab = 'all'; window.location.href = '{{ route('admin.reservations', ['type' => 'all'] + request()->except('type')) }}'" 
                        :class="activeTab === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 rounded-tl-lg flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>All Bookings ({{ $allBookings->total() }})</span>
                </button>
                <button @click="activeTab = 'room'; window.location.href = '{{ route('admin.reservations', ['type' => 'room'] + request()->except('type')) }}'" 
                        :class="activeTab === 'room' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Room Bookings ({{ $bookings->total() }})</span>
                </button>
                <button @click="activeTab = 'cottage'; window.location.href = '{{ route('admin.reservations', ['type' => 'cottage'] + request()->except('type')) }}'" 
                        :class="activeTab === 'cottage' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Cottage Bookings ({{ $cottageBookings->total() }})</span>
                </button>
                <button @click="activeTab = 'event'; window.location.href = '{{ route('admin.reservations', ['type' => 'event'] + request()->except('type')) }}'" 
                        :class="activeTab === 'event' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Events & Dining ({{ $eventDiningBookings->total() }})</span>
                </button>
            </div>
        </div>

        <!-- All Bookings Tab Content -->
        <div x-show="activeTab === 'all'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($allBookings as $booking)
                        <tr class="hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#{{ $booking->id }}</div>
                                <div class="text-xs text-gray-400">{{ $booking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->user->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $booking->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->room->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $booking->guests }} guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $booking->room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : '' }}
                                    {{ $booking->room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : '' }}
                                    {{ $booking->room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : '' }}">
                                    {{ $booking->room->category ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-400">{{ $booking->check_in->format('l \a\t g:i A') }}</div>
                                <div class="text-white mt-1">{{ $booking->check_out->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-400">{{ $booking->check_out->format('l \a\t g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-semibold">₱{{ number_format((float)$booking->total_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $booking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : '' }}
                                    {{ $booking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : '' }}
                                    {{ $booking->status === 'checked_in' ? 'bg-green-900 text-green-200' : '' }}
                                    {{ $booking->status === 'completed' ? 'bg-purple-900 text-purple-200' : '' }}
                                    {{ $booking->status === 'cancelled' ? 'bg-red-900 text-red-200' : '' }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for All Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                {{ $allBookings->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Room Bookings Tab Content -->
        <div x-show="activeTab === 'room'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Room</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-700 transition-colors" id="booking-row-{{ $booking->id }}">
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">#{{ $booking->id }}</div>
                            <div class="text-xs text-gray-400">{{ $booking->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white booking-room-name">{{ $booking->room->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-400 booking-guests">{{ $booking->guests }} guests</div>
                        </td>
                        <td class="px-6 py-4 booking-dates">
                            <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_in->format('l \a\t g:i A') }}</div>
                            <div class="text-white mt-1">{{ $booking->check_out->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_out->format('l \a\t g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-bold text-lg booking-total">{{ $booking->formatted_total_price }}</div>
                            <div class="text-sm text-gray-400 booking-nights">
                                {{ $booking->check_in->diffInDays($booking->check_out) }} night(s)
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span @class([
                                'px-3 py-1 rounded-full text-xs font-medium',
                                'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                'bg-blue-100 text-blue-800' => $booking->status === 'checked_in',
                                'bg-gray-100 text-gray-800' => $booking->status === 'checked_out',
                                'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                                'bg-purple-100 text-purple-800' => $booking->status === 'completed',
                            ])>
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.reservations.show', $booking) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors"
                                   title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
                                <!-- Edit Booking Details Button -->
                                <button type="button"
                                        onclick='editBookingDetails(@json($booking->load(["user", "room"])))'
                                        class="text-green-400 hover:text-green-300 transition-colors"
                                        title="Edit Booking Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <!-- Status Update Button -->
                                <button type="button"
                                        onclick="updateStatus('{{ $booking->id }}')"
                                        class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                        title="Update Status">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                No room bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Room Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Cottage Bookings Tab Content -->
        <div x-show="activeTab === 'cottage'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Cottage</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($cottageBookings as $cottageBooking)
                        <tr class="hover:bg-gray-700 transition-colors" id="cottage-booking-row-{{ $cottageBooking->id }}">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#C{{ $cottageBooking->id }}</div>
                                <div class="text-xs text-gray-400">{{ $cottageBooking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $cottageBooking->user->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $cottageBooking->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $cottageBooking->room->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $cottageBooking->guests }} guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-900 text-purple-200">
                                    {{ $cottageBooking->room->type ?? 'Cottage Booking' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $cottageBooking->check_in_date->format('M d, Y') }}</div>
                                @if($cottageBooking->check_in_time)
                                    <div class="text-sm text-gray-400">{{ date('g:i A', strtotime($cottageBooking->check_in_time)) }}</div>
                                @endif
                                <div class="text-white mt-1">{{ $cottageBooking->check_out_date->format('M d, Y') }}</div>
                                @if($cottageBooking->check_out_time)
                                    <div class="text-sm text-gray-400">{{ date('g:i A', strtotime($cottageBooking->check_out_time)) }}</div>
                                @endif
                                @if($cottageBooking->hours)
                                    <div class="text-xs text-blue-400 mt-1">{{ $cottageBooking->hours }} hours</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-bold text-lg">₱{{ number_format($cottageBooking->total_price, 2) }}</div>
                                @if($cottageBooking->nights > 0)
                                    <div class="text-sm text-gray-400">{{ $cottageBooking->nights }} night(s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span @class([
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    'bg-yellow-100 text-yellow-800' => $cottageBooking->status === 'pending',
                                    'bg-green-100 text-green-800' => $cottageBooking->status === 'confirmed',
                                    'bg-blue-100 text-blue-800' => $cottageBooking->status === 'checked_in',
                                    'bg-gray-100 text-gray-800' => $cottageBooking->status === 'checked_out',
                                    'bg-red-100 text-red-800' => $cottageBooking->status === 'cancelled',
                                    'bg-purple-100 text-purple-800' => $cottageBooking->status === 'completed',
                                    'bg-orange-100 text-orange-800' => $cottageBooking->status === 'no_show',
                                ])>
                                    {{ ucfirst(str_replace('_', ' ', $cottageBooking->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <button type="button"
                                            onclick="viewCottageBooking({{ $cottageBooking->id }})"
                                            class="text-blue-400 hover:text-blue-300 transition-colors"
                                            title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    @if(in_array(auth()->user()->role, ['admin', 'manager', 'staff']))
                                    <button type="button"
                                            onclick="updateCottageStatus('{{ $cottageBooking->id }}')"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                            title="Update Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                No cottage bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Cottage Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                {{ $cottageBookings->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Events & Dining Bookings Tab Content -->
        <div x-show="activeTab === 'event'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($eventDiningBookings as $eventBooking)
                        <tr class="hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#E{{ $eventBooking->id }}</div>
                                <div class="text-xs text-gray-400">{{ $eventBooking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $eventBooking->user->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $eventBooking->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $eventBooking->room->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-400">{{ $eventBooking->guests }} guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-pink-900 text-pink-200">
                                    {{ $eventBooking->room->type ?? 'Event & Dining' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $eventBooking->check_in->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-400">{{ $eventBooking->check_in->format('l \a\t g:i A') }}</div>
                                <div class="text-white mt-1">{{ $eventBooking->check_out->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-400">{{ $eventBooking->check_out->format('l \a\t g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-semibold">₱{{ number_format((float)$eventBooking->total_price, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $eventBooking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : '' }}
                                    {{ $eventBooking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : '' }}
                                    {{ $eventBooking->status === 'checked_in' ? 'bg-green-900 text-green-200' : '' }}
                                    {{ $eventBooking->status === 'completed' ? 'bg-purple-900 text-purple-200' : '' }}
                                    {{ $eventBooking->status === 'cancelled' ? 'bg-red-900 text-red-200' : '' }}">
                                    {{ ucfirst($eventBooking->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No event & dining bookings found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Events & Dining Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                {{ $eventDiningBookings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Booking Details Modal -->
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-white">Edit Booking Details</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="editBookingForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Guest Information (Read-only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Name</label>
                                <input type="text" id="edit_guest_name" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Email</label>
                                <input type="email" id="edit_guest_email" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <div>
                            <label for="edit_room_id" class="block text-sm font-medium text-gray-300 mb-2">Room</label>
                            <select name="room_id" id="edit_room_id" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Room</option>
                                @php
                                    $allRooms = \App\Models\Room::all();
                                @endphp
                                @foreach($allRooms as $room)
                                    <option value="{{ $room->id }}" data-price="{{ $room->price }}" data-capacity="{{ $room->capacity }}">
                                        {{ $room->name }} - ₱{{ number_format($room->price, 2) }}/night (Max: {{ $room->capacity }} guests)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Booking Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_check_in" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date & Time</label>
                                <input type="datetime-local" name="check_in" id="edit_check_in" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="edit_check_out" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date & Time</label>
                                <input type="datetime-local" name="check_out" id="edit_check_out" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="edit_guests" class="block text-sm font-medium text-gray-300 mb-2">
                                Number of Guests
                                <span id="edit_capacity_info" class="text-blue-400 text-xs font-normal ml-2"></span>
                            </label>
                            <input type="number" name="guests" id="edit_guests" min="1" max="20" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p id="edit_capacity_warning" class="text-yellow-400 text-sm mt-2 hidden">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Maximum capacity exceeded!
                            </p>
                        </div>

                        <!-- Special Requests -->
                        <div>
                            <label for="edit_special_requests" class="block text-sm font-medium text-gray-300 mb-2">Special Requests</label>
                            <textarea name="special_requests" id="edit_special_requests" rows="3"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requests or notes..."></textarea>
                        </div>

                        <!-- Total Price Display -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300">Total Amount:</span>
                                <span id="edit_total_display" class="text-green-400 font-bold text-xl">₱0.00</span>
                            </div>
                            <div class="text-sm text-gray-400 mt-2">
                                <span id="edit_nights_display">0 nights</span> × <span id="edit_rate_display">₱0.00/night</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition-colors">
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal (reuse from bookings index) -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Update Booking Status</h3>
                    <form id="statusForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition-colors">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
let currentBookingId = null;

// Edit Booking Details Function
function editBookingDetails(booking) {
    console.log('Editing booking:', booking);
    
    const modal = document.getElementById('editBookingModal');
    const form = document.getElementById('editBookingForm');
    
    currentBookingId = booking.id;
    
    // Set form action - use the update route with the booking ID
    form.action = `{{ url('admin/bookings') }}/${booking.id}`;
    
    // Add method spoofing for PUT request
    let methodField = form.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        form.appendChild(methodField);
    }
    methodField.value = 'PUT';
    
    // Populate guest information (read-only)
    document.getElementById('edit_guest_name').value = booking.user?.name || 'N/A';
    document.getElementById('edit_guest_email').value = booking.user?.email || 'N/A';
    
    // Populate editable fields
    const roomSelect = document.getElementById('edit_room_id');
    if (roomSelect && booking.room_id) {
        roomSelect.value = booking.room_id;
        console.log('Set room to:', booking.room_id, 'Current room:', booking.room?.name);
    }
    
    document.getElementById('edit_guests').value = booking.guests || 1;
    document.getElementById('edit_special_requests').value = booking.special_requests || '';
    
    // Handle dates - convert to local timezone
    if (booking.check_in) {
        try {
            const checkIn = typeof booking.check_in === 'string' 
                ? new Date(booking.check_in) 
                : booking.check_in;
            
            const year = checkIn.getFullYear();
            const month = String(checkIn.getMonth() + 1).padStart(2, '0');
            const day = String(checkIn.getDate()).padStart(2, '0');
            const hours = String(checkIn.getHours()).padStart(2, '0');
            const minutes = String(checkIn.getMinutes()).padStart(2, '0');
            
            document.getElementById('edit_check_in').value = `${year}-${month}-${day}T${hours}:${minutes}`;
        } catch (e) {
            console.error('Error parsing check-in date:', e);
        }
    }
    
    if (booking.check_out) {
        try {
            const checkOut = typeof booking.check_out === 'string' 
                ? new Date(booking.check_out) 
                : booking.check_out;
            
            const year = checkOut.getFullYear();
            const month = String(checkOut.getMonth() + 1).padStart(2, '0');
            const day = String(checkOut.getDate()).padStart(2, '0');
            const hours = String(checkOut.getHours()).padStart(2, '0');
            const minutes = String(checkOut.getMinutes()).padStart(2, '0');
            
            document.getElementById('edit_check_out').value = `${year}-${month}-${day}T${hours}:${minutes}`;
        } catch (e) {
            console.error('Error parsing check-out date:', e);
        }
    }
    
    // Calculate and display total (after setting all values)
    setTimeout(() => {
        calculateEditTotal();
        updateEditRoomCapacity();
    }, 100);
    
    // Show modal
    modal.classList.remove('hidden');
}

// Handle form submission with enhanced error handling
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    // Validate dates
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    
    if (checkInInput.value && checkOutInput.value) {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (checkOut <= checkIn) {
            showNotification('Check-out date must be after check-in date!', 'error');
            return;
        }
        
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        if (nights < 1) {
            showNotification('Booking must be for at least 1 night!', 'error');
            return;
        }
    }
    
    // Validate room capacity
    const guestsInput = document.getElementById('edit_guests');
    const maxCapacity = parseInt(guestsInput.max) || 20;
    const currentGuests = parseInt(guestsInput.value) || 0;
    
    if (currentGuests > maxCapacity) {
        showNotification(`Number of guests (${currentGuests}) exceeds room capacity (${maxCapacity})!`, 'error');
        return;
    }
    
    if (currentGuests < 1) {
        showNotification('Number of guests must be at least 1!', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('CSRF token missing. Please refresh the page.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
    }
    
    // Calculate total price and add to form data
    const roomSelect = document.getElementById('edit_room_id');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            formData.append('total_price', total);
            console.log(`Calculated: ${nights} nights × ₱${roomPrice} = ₱${total}`);
        }
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(data => {
        if (data.success) {
            showNotification('Booking updated successfully!', 'success');
            closeEditModal();
            updateBookingRow(currentBookingId, data.booking);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Complete error details:', error);
        showNotification('Failed to update booking: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Show notification function
function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                }
            </svg>
            ${message}
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Calculate total for edit form
function calculateEditTotal() {
    const roomSelect = document.getElementById('edit_room_id');
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    const totalDisplay = document.getElementById('edit_total_display');
    const nightsDisplay = document.getElementById('edit_nights_display');
    const rateDisplay = document.getElementById('edit_rate_display');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            totalDisplay.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            nightsDisplay.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
            rateDisplay.textContent = `₱${roomPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}/night`;
        }
    }
}

// Update room capacity constraints
function updateEditRoomCapacity() {
    const roomSelect = document.getElementById('edit_room_id');
    const guestsInput = document.getElementById('edit_guests');
    const capacityInfo = document.getElementById('edit_capacity_info');
    const capacityWarning = document.getElementById('edit_capacity_warning');
    
    if (roomSelect.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const capacity = parseInt(selectedOption.dataset.capacity) || 20;
        
        guestsInput.max = capacity;
        capacityInfo.textContent = `(Maximum: ${capacity} guests)`;
        
        const currentGuests = parseInt(guestsInput.value) || 0;
        if (currentGuests > capacity) {
            capacityWarning.classList.remove('hidden');
            guestsInput.classList.add('border-yellow-500');
        } else {
            capacityWarning.classList.add('hidden');
            guestsInput.classList.remove('border-yellow-500');
        }
    } else {
        guestsInput.max = 20;
        capacityInfo.textContent = '';
        capacityWarning.classList.add('hidden');
        guestsInput.classList.remove('border-yellow-500');
    }
}

// Validate guests on input
function validateEditGuests() {
    const guestsInput = document.getElementById('edit_guests');
    const capacityWarning = document.getElementById('edit_capacity_warning');
    const maxCapacity = parseInt(guestsInput.max) || 20;
    const currentGuests = parseInt(guestsInput.value) || 0;
    
    if (currentGuests > maxCapacity) {
        capacityWarning.classList.remove('hidden');
        guestsInput.classList.add('border-yellow-500');
    } else {
        capacityWarning.classList.add('hidden');
        guestsInput.classList.remove('border-yellow-500');
    }
}

// Update booking row in the table
function updateBookingRow(bookingId, bookingData) {
    const row = document.getElementById(`booking-row-${bookingId}`);
    if (!row) return;
    
    const roomNameEl = row.querySelector('.booking-room-name');
    if (roomNameEl && bookingData.room) {
        roomNameEl.textContent = bookingData.room.name;
    }
    
    const guestsEl = row.querySelector('.booking-guests');
    if (guestsEl) {
        guestsEl.textContent = `${bookingData.guests} guests`;
    }
    
    const datesEl = row.querySelector('.booking-dates');
    if (datesEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        const checkOut = new Date(bookingData.check_out);
        
        datesEl.innerHTML = `
            <div class="text-white">${checkIn.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkIn.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkIn.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
            <div class="text-white mt-1">${checkOut.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkOut.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkOut.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
        `;
    }
    
    const totalEl = row.querySelector('.booking-total');
    if (totalEl && bookingData.total_price) {
        totalEl.textContent = `₱${parseFloat(bookingData.total_price).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    }
    
    const nightsEl = row.querySelector('.booking-nights');
    if (nightsEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        const checkOut = new Date(bookingData.check_out);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        nightsEl.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
    }
    
    row.classList.add('bg-green-900', 'bg-opacity-30');
    setTimeout(() => {
        row.classList.remove('bg-green-900', 'bg-opacity-30');
    }, 2000);
}

// Add event listeners for real-time calculation and validation
document.getElementById('edit_room_id').addEventListener('change', function() {
    updateEditRoomCapacity();
    calculateEditTotal();
});
document.getElementById('edit_check_in').addEventListener('change', calculateEditTotal);
document.getElementById('edit_check_out').addEventListener('change', calculateEditTotal);
document.getElementById('edit_guests').addEventListener('input', validateEditGuests);

function closeEditModal() {
    document.getElementById('editBookingModal').classList.add('hidden');
}

// Status update functions
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/admin/reservations/${bookingId}/status`;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush
@endsection
