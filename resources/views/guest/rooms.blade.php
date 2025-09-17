@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-6">Our Rooms</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($rooms as $room)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($room->images->isNotEmpty())
                <img src="{{ asset('storage/' . $room->images->first()->image_path) }}" alt="{{ $room->name }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400">No image available</span>
                </div>
            @endif
            
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-2">{{ $room->name }}</h2>
                <p class="text-gray-600 mb-4">{{ $room->description }}</p>
                
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">
                        <i class="fas fa-user"></i> {{ $room->capacity }} guests
                    </span>
                    <span class="text-gray-600">
                        <i class="fas fa-star text-yellow-400"></i> {{ $room->rating }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold">₱{{ number_format((float)$room->price, 2) }}/night</span>
                    <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
