@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Filter Section -->
    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8">
        <form action="{{ route('guest.rooms.browse') }}" method="GET" class="flex gap-4">
            <div class="w-full">
                <label class="block text-white mb-2">Room Type</label>
                <select name="type" 
                        class="w-full bg-green-800 text-white rounded-lg px-4 py-2"
                        onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rooms as $room)
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg overflow-hidden group hover:bg-green-800/50 transition-colors">
                <a href="{{ route('guest.rooms.show', $room) }}" class="block">
                    <!-- Room Image -->
                    @if($room->images->isNotEmpty())
                        <div class="relative h-64 overflow-hidden">
                            <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @endif

                    <!-- Room Details -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $room->name }}</h3>
                        <p class="text-gray-300 mb-4">{{ Str::limit($room->description, 100) }}</p>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-gray-300">
                                <span class="block text-sm">Type</span>
                                <span class="font-semibold text-white">{{ $room->type }}</span>
                            </div>
                            <div class="text-gray-300">
                                <span class="block text-sm">Capacity</span>
                                <span class="font-semibold text-white">{{ $room->capacity }} persons</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-gray-300 text-sm">Price per night</span>
                                <p class="text-2xl font-bold text-white">₱{{ number_format($room->price, 2) }}</p>
                            </div>
                            <span class="inline-flex items-center text-green-400 group-hover:translate-x-2 transition-transform">
                                View Details
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-400 py-8">
                No rooms available matching your criteria.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $rooms->links() }}
    </div>
</div>
@endsection