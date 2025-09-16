@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50">Room Management</h1>
        <p class="text-green-100 mt-2">Manage room availability, rates, and details</p>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-white">Room List</h2>
            <a href="{{ route('admin.rooms.create') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Add New Room
            </a>
        </div>

        <form action="{{ route('admin.rooms') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <input type="text" 
                    name="search" 
                    value="{{ $currentSearch }}" 
                    placeholder="Search rooms..."
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>

            <!-- Type Filter -->
            <div>
                <select name="type" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="">All Status</option>
                    <option value="available" {{ $currentStatus === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ $currentStatus === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>

            <!-- Filter Button -->
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Rooms Table -->
    <div class="bg-gray-800 rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Room</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Capacity</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-100">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse($rooms as $room)
                    <tr class="hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <div class="text-green-50">Room {{ $room->number }}</div>
                            <div class="text-sm text-green-200">{{ $room->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-green-50">
                            {{ ucfirst($room->type) }}
                        </td>
                        <td class="px-6 py-4 text-green-50">
                            {{ $room->capacity }} guests
                        </td>
                        <td class="px-6 py-4 text-green-50">
                            ₱{{ number_format($room->price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm
                                @if($room->is_available) bg-green-500 text-white
                                @else bg-red-500 text-white
                                @endif">
                                {{ $room->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <!-- Edit -->
                            <a href="{{ route('admin.rooms.edit', $room) }}" 
                               class="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                Edit
                            </a>
                            
                            <!-- Toggle Availability -->
                            <form action="{{ route('admin.rooms.toggle-availability', $room) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors">
                                    Toggle Status
                                </button>
                            </form>

                            <!-- Delete -->
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this room?')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-green-200">
                            No rooms found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
