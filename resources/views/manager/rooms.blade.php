@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Rooms & Facilities
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Update room availability, rates, and facility details.
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200 mr-4">
                        Back to Dashboard
                    </a>
                    <a href="{{ route('manager.rooms.create') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Add New Room
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8">
                <form method="GET" action="{{ route('manager.rooms') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-green-200 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search rooms..."
                               class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-green-200 mb-2">Type</label>
                        <select name="type" class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">All Types</option>
                            <option value="standard" {{ request('type') === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="deluxe" {{ request('type') === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                            <option value="suite" {{ request('type') === 'suite' ? 'selected' : '' }}>Suite</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-green-200 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-green-800/50 border border-green-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="occupied" {{ request('status') === 'occupied' ? 'selected' : '' }}>Occupied</option>
                            <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    <div class="flex items-end space-x-2">
                        <button type="submit" 
                                class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('manager.rooms') }}" 
                           class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Rooms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                @forelse($rooms as $room)
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden hover:bg-green-900/70 transition-all duration-300">
                    <!-- Room Image -->
                    <div class="h-48 bg-gray-600 flex items-center justify-center relative">
                        @if($room->images && $room->images->count() > 0)
                        <img src="{{ asset('storage/' . $room->images->first()->image_path) }}" 
                             alt="{{ $room->name }}" 
                             class="w-full h-full object-cover">
                        @else
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                {{ $room->status === 'available' ? 'bg-green-600 text-green-100' : '' }}
                                {{ $room->status === 'occupied' ? 'bg-red-600 text-red-100' : '' }}
                                {{ $room->status === 'maintenance' ? 'bg-yellow-600 text-yellow-100' : '' }}
                                {{ $room->status === 'out_of_order' ? 'bg-red-800 text-red-100' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $room->status ?? 'available')) }}
                            </span>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-green-50">{{ $room->name }}</h3>
                            <div class="text-right">
                                <div class="text-green-400 font-bold">₱{{ number_format($room->price ?? 0, 2) }}</div>
                                <div class="text-xs text-green-300">per night</div>
                            </div>
                        </div>
                        
                        <div class="text-green-300 space-y-2 mb-4">
                            <p><strong>Type:</strong> {{ ucfirst($room->type ?? 'Standard') }}</p>
                            <p><strong>Capacity:</strong> {{ $room->capacity ?? 2 }} guests</p>
                            @if($room->amenities)
                                <p><strong>Amenities:</strong> {{ count(json_decode($room->amenities, true)) }} items</p>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('manager.rooms.show', $room) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                View Details
                            </a>
                            <a href="{{ route('manager.rooms.edit', $room) }}" 
                               class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                Edit
                            </a>
                            @if($room->status === 'available' && $room->is_available)
                            <a href="{{ route('manager.bookings.quick-book', $room) }}" 
                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded text-sm transition-colors">
                                Book Room
                            </a>
                            @endif
                        </div>

                        <!-- Quick Status Update -->
                        <div class="mt-4 pt-4 border-t border-green-700">
                            <form onsubmit="updateRoomStatus(event, {{ $room->id }})" class="flex space-x-2">
                                <select name="status" 
                                        class="flex-1 px-2 py-1 text-xs bg-green-800/50 border border-green-600 rounded text-white">
                                    <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                    <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="out_of_order" {{ $room->status === 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                                </select>
                                <button type="submit" 
                                        class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white text-xs rounded transition-colors">
                                    Update
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-16">
                    <div class="text-green-300">
                        <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                        <h3 class="text-xl font-medium text-green-200 mb-2">No rooms found</h3>
                        <p class="text-green-400 mb-6">
                            @if(request()->hasAny(['search', 'type', 'status']))
                                No rooms match your current filters.
                            @else
                                Start by adding your first room.
                            @endif
                        </p>
                        @if(!request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('manager.rooms.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add First Room
                        </a>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($rooms->hasPages())
            <div class="flex justify-center">
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-4">
                    {{ $rooms->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </main>
@endsection

@push('scripts')
<script>
function updateRoomStatus(event, roomId) {
    event.preventDefault();
    
    const form = event.target;
    const status = form.status.value;
    
    fetch(`{{ url('manager/rooms') }}/${roomId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update status: ' + error.message, 'error');
    });
}

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
</script>
@endpush