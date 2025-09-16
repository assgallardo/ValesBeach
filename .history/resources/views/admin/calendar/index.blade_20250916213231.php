@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Booking Calendar</h1>
                <p class="text-gray-400 mt-2">View bookings, available and unavailable dates</p>
            </div>
            
            <!-- Month Navigation -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.calendar', ['year' => $startDate->copy()->subMonth()->year, 'month' => $startDate->copy()->subMonth()->month]) }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    ← Previous
                </a>
                <div class="text-white font-semibold text-lg">
                    {{ $startDate->format('F Y') }}
                </div>
                <a href="{{ route('admin.calendar', ['year' => $startDate->copy()->addMonth()->year, 'month' => $startDate->copy()->addMonth()->month]) }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Next →
                </a>
            </div>
        </div>
    </div>

    <!-- Room Legend -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">Rooms</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($rooms as $room)
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 rounded" style="background-color: {{ $room->color ?? sprintf('#%06X', mt_rand(0, 0xFFFFFF)) }};"></div>
                    <span class="text-white text-sm">{{ $room->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status Legend -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-white mb-4">Booking Status</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                <span class="text-white text-sm">Pending</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-white text-sm">Confirmed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-white text-sm">Checked In</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-purple-500 rounded"></div>
                <span class="text-white text-sm">Completed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-gray-500 rounded"></div>
                <span class="text-white text-sm">Checked Out</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-red-500 rounded"></div>
                <span class="text-white text-sm">Cancelled</span>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 bg-gray-900">
            @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                <div class="px-4 py-3 text-center text-sm font-medium text-gray-300 border-r border-gray-700 last:border-r-0">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Calendar Body -->
        <div class="grid grid-cols-7">
            @php
                $currentDate = $startDate->copy()->startOfWeek();
                $endOfCalendar = $endDate->copy()->endOfWeek();
            @endphp

            @while($currentDate <= $endOfCalendar)
                @php
                    $isCurrentMonth = $currentDate->month === $startDate->month;
                    $isToday = $currentDate->isToday();
                    $dayBookings = $bookings->filter(function($booking) use ($currentDate) {
                        return $currentDate->between($booking->check_in->toDateString(), $booking->check_out->copy()->subDay()->toDateString());
                    });
                @endphp

                <div class="min-h-[120px] border-r border-b border-gray-700 last:border-r-0 p-2 {{ $isCurrentMonth ? 'bg-gray-800' : 'bg-gray-900' }} {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                    <!-- Date Number -->
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium {{ $isCurrentMonth ? 'text-white' : 'text-gray-500' }} {{ $isToday ? 'bg-blue-500 px-2 py-1 rounded-full' : '' }}">
                            {{ $currentDate->day }}
                        </span>
                        @if($isCurrentMonth && $dayBookings->isEmpty())
                            <span class="text-xs text-green-400">Available</span>
                        @endif
                    </div>

                    <!-- Bookings for this day -->
                    <div class="space-y-1">
                        @foreach($dayBookings->take(3) as $booking)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500',
                                    'confirmed' => 'bg-green-500',
                                    'checked_in' => 'bg-blue-500',
                                    'checked_out' => 'bg-gray-500',
                                    'cancelled' => 'bg-red-500',
                                    'completed' => 'bg-purple-500',
                                ];
                                $statusColor = $statusColors[$booking->status] ?? 'bg-gray-500';
                            @endphp
                            <div class="text-xs {{ $statusColor }} text-white p-1 rounded cursor-pointer hover:opacity-80"
                                 onclick="showBookingDetails({{ $booking->id }})"
                                 title="{{ $booking->room->name }} - {{ $booking->user->name }}">
                                <div class="truncate">{{ $booking->room->name }}</div>
                                <div class="truncate">{{ $booking->user->name }}</div>
                            </div>
                        @endforeach

                        @if($dayBookings->count() > 3)
                            <div class="text-xs text-gray-400 p-1">
                                +{{ $dayBookings->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                </div>

                @php
                    $currentDate->addDay();
                @endphp
            @endwhile
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">This Month</p>
                    <p class="text-2xl font-bold">{{ $bookings->count() }}</p>
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
                    <p class="text-green-100 text-sm">Confirmed</p>
                    <p class="text-2xl font-bold">{{ $bookings->where('status', 'confirmed')->count() }}</p>
                </div>
                <div class="bg-green-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Pending</p>
                    <p class="text-2xl font-bold">{{ $bookings->where('status', 'pending')->count() }}</p>
                </div>
                <div class="bg-yellow-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
    </div>
</div>

<!-- Booking Details Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-white">Booking Details</h3>
                    <button onclick="closeBookingModal()" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="bookingContent" class="text-white">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const bookings = @json($bookings->values());

function showBookingDetails(bookingId) {
    const booking = bookings.find(b => b.id === bookingId);
    if (!booking) return;

    const statusColors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-green-100 text-green-800',
        'checked_in': 'bg-blue-100 text-blue-800',
        'checked_out': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800',
        'completed': 'bg-purple-100 text-purple-800',
    };

    const checkIn = new Date(booking.check_in);
    const checkOut = new Date(booking.check_out);
    
    const content = `
        <div class="space-y-4">
            <div>
                <label class="text-sm text-gray-400">Booking ID</label>
                <p class="text-white font-medium">#${booking.id}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Guest</label>
                <p class="text-white font-medium">${booking.user.name}</p>
                <p class="text-sm text-gray-400">${booking.user.email}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Room</label>
                <p class="text-white font-medium">${booking.room.name}</p>
                <p class="text-sm text-gray-400">${booking.guests} guests</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Check-in</label>
                <p class="text-white font-medium">${checkIn.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                <p class="text-sm text-gray-400">${checkIn.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Check-out</label>
                <p class="text-white font-medium">${checkOut.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                <p class="text-sm text-gray-400">${checkOut.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Total</label>
                <p class="text-green-400 font-bold text-lg">₱${parseFloat(booking.total_price).toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
            </div>
            <div>
                <label class="text-sm text-gray-400">Status</label>
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium ${statusColors[booking.status] || 'bg-gray-100 text-gray-800'}">
                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1).replace('_', ' ')}
                    </span>
                </div>
            </div>
            <div class="flex space-x-2 pt-4">
                <a href="/admin/reservations/${booking.id}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition-colors text-center">
                    View Details
                </a>
            </div>
        </div>
    `;
    
    document.getElementById('bookingContent').innerHTML = content;
    document.getElementById('bookingModal').classList.remove('hidden');
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBookingModal();
    }
});
</script>
@endpush
@endsection
