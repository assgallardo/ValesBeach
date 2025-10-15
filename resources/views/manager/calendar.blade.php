@extends('layouts.manager')

@section('title', 'Calendar Management')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Calendar Management</h1>
            <p class="text-gray-300">Manage bookings and room availability</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Rooms -->
            <div class="bg-green-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-green-100 text-sm">Total Rooms</p>
                        <p class="text-2xl font-bold text-white">{{ $totalRooms }}</p>
                    </div>
                </div>
            </div>

            <!-- Occupied Rooms -->
            <div class="bg-blue-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-blue-100 text-sm">Currently Occupied</p>
                        <p class="text-2xl font-bold text-white">{{ $occupiedRooms }}</p>
                    </div>
                </div>
            </div>

            <!-- Occupancy Rate -->
            <div class="bg-purple-800 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-purple-100 text-sm">Occupancy Rate</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($occupancyRate, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Actions -->
        <div class="flex flex-wrap gap-4 mb-8">
            <a href="{{ route('manager.bookings.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Booking
            </a>
            
            <a href="{{ route('manager.bookings.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                View All Bookings
            </a>

            <a href="{{ route('manager.rooms') }}" 
               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Manage Rooms
            </a>
        </div>

        <!-- Calendar Section -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">Booking Calendar</h2>
                <div class="flex items-center space-x-4">
                    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors" 
                            onclick="previousMonth()">
                        ← Previous
                    </button>
                    <span class="text-white font-medium" id="currentMonth">{{ now()->format('F Y') }}</span>
                    <button class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors" 
                            onclick="nextMonth()">
                        Next →
                    </button>
                </div>
            </div>

            <!-- Simple Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 mb-4">
                <!-- Calendar headers -->
                <div class="p-3 text-center text-gray-400 font-medium">Sun</div>
                <div class="p-3 text-center text-gray-400 font-medium">Mon</div>
                <div class="p-3 text-center text-gray-400 font-medium">Tue</div>
                <div class="p-3 text-center text-gray-400 font-medium">Wed</div>
                <div class="p-3 text-center text-gray-400 font-medium">Thu</div>
                <div class="p-3 text-center text-gray-400 font-medium">Fri</div>
                <div class="p-3 text-center text-gray-400 font-medium">Sat</div>
            </div>

            <!-- Calendar days will be populated by JavaScript -->
            <div id="calendarDays" class="grid grid-cols-7 gap-2">
                <!-- Days will be dynamically generated -->
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="mt-8 bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold text-white mb-6">Recent Bookings</h2>
            
            @if($bookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-700">
                                <th class="text-left py-3 px-4 text-gray-300">Guest</th>
                                <th class="text-left py-3 px-4 text-gray-300">Room</th>
                                <th class="text-left py-3 px-4 text-gray-300">Check-in</th>
                                <th class="text-left py-3 px-4 text-gray-300">Check-out</th>
                                <th class="text-left py-3 px-4 text-gray-300">Status</th>
                                <th class="text-left py-3 px-4 text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings->take(10) as $booking)
                            <tr class="border-b border-gray-700 hover:bg-gray-750">
                                <td class="py-3 px-4 text-white">
                                    {{ $booking->user->name ?? 'Guest' }}
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    {{ $booking->room->name ?? 'N/A' }}
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4 text-gray-300">
                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('manager.bookings.show', $booking->id) }}" 
                                       class="text-blue-400 hover:text-blue-300 mr-3">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400 mb-4">No bookings found</p>
                    <a href="{{ route('manager.bookings.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                        Create First Booking
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Simple calendar functionality
let currentDate = new Date();

function renderCalendar() {
    const month = currentDate.getMonth();
    const year = currentDate.getFullYear();
    
    // Update month display
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    document.getElementById('currentMonth').textContent = monthNames[month] + ' ' + year;
    
    // Calculate calendar grid
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'p-3 text-center';
        calendarDays.appendChild(emptyCell);
    }
    
    // Add days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'p-3 text-center text-white hover:bg-gray-700 rounded cursor-pointer';
        dayCell.textContent = day;
        
        // Highlight today
        const today = new Date();
        if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
            dayCell.classList.add('bg-green-600', 'text-white', 'font-bold');
        }
        
        calendarDays.appendChild(dayCell);
    }
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

// Initialize calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
});
</script>
@endsection