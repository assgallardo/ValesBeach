@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            Available Rooms
        </h2>
        <p class="text-xl text-gray-200">
            Choose your perfect stay at Vales Beach Resort
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($rooms as $room)
        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
            @if($room->image_url)
                <img src="{{ $room->image_url }}" alt="{{ $room->name }}" class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                    <span class="text-gray-500">No image available</span>
                </div>
            @endif

            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-2">{{ $room->name }}</h3>
                <p class="text-gray-300 mb-4">{{ $room->description }}</p>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-gray-300">
                        <span>Price per night:</span>
                        <span class="font-bold">{{ $room->formatted_price }}</span>
                    </div>
                    <div class="flex justify-between text-gray-300">
                        <span>Max guests:</span>
                        <span>{{ $room->max_guests }} persons</span>
                    </div>
                </div>

                <a href="{{ route('guest.rooms.book', $room) }}" 
                   class="block w-full text-center bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Book Now
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
