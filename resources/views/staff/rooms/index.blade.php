@extends('layouts.staff')

@section('content')
<div class="bg-green-900/50 backdrop-blur-sm rounded-lg">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Rooms & Facilities</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rooms as $room)
                <div class="bg-green-800/50 rounded-lg overflow-hidden">
                    @if($room->images->isNotEmpty())
                        <div class="relative h-48">
                            <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                 alt="{{ $room->name }}"
                                 class="w-full h-full object-cover">
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-sm rounded-full {{ $room->is_available ? 'bg-green-600' : 'bg-red-600' }} text-white">
                                    {{ $room->is_available ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-white mb-2">{{ $room->name }}</h3>
                        <p class="text-gray-300 text-sm mb-4">{{ Str::limit($room->description, 100) }}</p>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-400">Price per night</span>
                                <p class="text-lg font-bold text-white">₱{{ number_format($room->price, 2) }}</p>
                            </div>
                            <a href="{{ route('staff.rooms.show', $room) }}" 
                               class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection