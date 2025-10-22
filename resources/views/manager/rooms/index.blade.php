@extends('layouts.manager')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-white">Rooms & Facilities</h2>
        <a href="{{ route('manager.rooms.create') }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
            Add New Room
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <form action="{{ route('manager.rooms.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-gray-300 mb-2">Search</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search rooms..."
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2">
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-gray-300 mb-2">Room Type</label>
                <select name="type" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-gray-300 mb-2">Price Range</label>
                <div class="flex space-x-2">
                    <input type="number" 
                           name="min_price" 
                           value="{{ request('min_price') }}"
                           placeholder="Min"
                           class="w-1/2 bg-gray-700 text-white rounded-lg px-4 py-2">
                    <input type="number" 
                           name="max_price" 
                           value="{{ request('max_price') }}"
                           placeholder="Max"
                           class="w-1/2 bg-gray-700 text-white rounded-lg px-4 py-2">
                </div>
            </div>

            <!-- Availability Filter -->
            <div>
                <label class="block text-gray-300 mb-2">Status</label>
                <select name="is_available" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2">
                    <option value="">All Status</option>
                    <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                <a href="{{ route('manager.rooms.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div class="text-gray-300 mb-4">
        Found {{ $rooms->total() }} rooms
    </div>

    <!-- Rooms Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="px-6 py-4">Room Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Capacity</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    @forelse($rooms as $room)
                        <tr class="text-gray-300 hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a href="{{ route('manager.rooms.show', $room) }}" 
                                   class="text-blue-400 hover:text-blue-300 hover:underline">
                                    {{ $room->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $room->type }}</td>
                            <td class="px-6 py-4">₱{{ number_format($room->price, 2) }}</td>
                            <td class="px-6 py-4">{{ $room->capacity }} guests</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $room->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    @if($room->is_available)
                                    <a href="{{ route('manager.bookings.createFromRoom', $room) }}" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        Book Room
                                    </a>
                                    @endif
                                    <a href="{{ route('manager.rooms.edit', $room) }}" 
                                       class="text-blue-400 hover:text-blue-300">
                                        Edit
                                    </a>
                                    <button onclick="toggleAvailability({{ $room->id }}, {{ $room->is_available }})"
                                            class="text-yellow-400 hover:text-yellow-300">
                                        {{ $room->is_available ? 'Set Unavailable' : 'Set Available' }}
                                    </button>
                                    <form action="{{ route('manager.rooms.destroy', $room) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this room?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                No rooms found. Add your first room to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $rooms->links() }}
    </div>
</div>

@push('scripts')
<script>
function toggleAvailability(roomId, currentStatus) {
    if (confirm('Are you sure you want to change the room availability?')) {
        fetch(`/manager/rooms/${roomId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ is_available: !currentStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection
