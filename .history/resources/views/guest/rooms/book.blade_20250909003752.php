@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                Book {{ $room->name }}
            </h2>
            <p class="text-xl text-gray-200">
                Complete your booking details below
            </p>
        </div>

        @if($errors->any())
        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-2">Room Details</h3>
                        <p class="text-gray-300">{{ $room->description }}</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-300">
                            <span>Price per night:</span>
                            <span class="font-bold">{{ $room->formatted_price }}</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Max guests:</span>
                            <span>{{ $room->capacity }} persons</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('guest.rooms.book.store', $room) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-in Date -->
                        <div>
                            <label for="check_in" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date</label>
                            <input type="date" id="check_in" name="check_in" 
                                   value="{{ old('check_in') }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>

                        <!-- Check-out Date -->
                        <div>
                            <label for="check_out" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date</label>
                            <input type="date" id="check_out" name="check_out" 
                                   value="{{ old('check_out') }}"
                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-300 mb-2">Number of Guests</label>
                            <input type="number" id="guests" name="guests" 
                                   value="{{ old('guests', 1) }}"
                                   min="1" max="{{ $room->capacity }}"
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label for="special_requests" class="block text-sm font-medium text-gray-300 mb-2">Special Requests (Optional)</label>
                        <textarea id="special_requests" name="special_requests" rows="4"
                                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                                  placeholder="Any special requests or requirements?">{{ old('special_requests') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('guest.rooms') }}" 
                           class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
