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
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="flex space-x-3">
                <!-- Quick Room Selection for Booking -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
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
                         class="absolute right-0 mt-2 w-64 bg-gray-800 rounded-md shadow-lg py-1 z-50 max-h-64 overflow-y-auto"
                         style="display: none;">
                        <div class="px-4 py-2 text-sm text-gray-300 border-b border-gray-700">Select Room to Book:</div>
                        @php
                            $availableRooms = \App\Models\Room::where('is_available', true)->get();
                        @endphp
                        @foreach($availableRooms as $room)
                            <a href="{{ route('admin.reservations.createFromRoom', $room) }}" 
                               class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <div class="font-medium">{{ $room->name }}</div>
                                <div class="text-xs text-gray-400">₱{{ number_format($room->price_per_night, 2) }}/night • {{ $room->capacity }} guests</div>
                            </a>
                        @endforeach
                        @if($availableRooms->isEmpty())
                            <div class="px-4 py-2 text-sm text-gray-400">No available rooms</div>
                        @endif
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

    <!-- Reservations Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
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
                    <tr class="hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">#{{ $booking->id }}</div>
                            <div class="text-xs text-gray-400">{{ $booking->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->room->name }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->guests }} guests</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_in->format('l \a\t g:i A') }}</div>
                            <div class="text-white mt-1">{{ $booking->check_out->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_out->format('l \a\t g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-bold text-lg">{{ $booking->formatted_total_price }}</div>
                            <div class="text-sm text-gray-400">
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
                                <a href="{{ route('admin.bookings.show', $booking) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(in_array(auth()->user()->role, ['admin', 'manager']))
                                <button type="button"
                                        onclick="updateStatus('{{ $booking->id }}')"
                                        class="text-green-400 hover:text-green-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No reservations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 bg-gray-900">
            {{ $bookings->appends(request()->query())->links() }}
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
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/admin/bookings/${bookingId}/status`;
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
