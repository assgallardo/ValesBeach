@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Edit Booking
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Reference: {{ $booking->booking_reference }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.bookings.show', $booking) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Booking
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <form action="{{ route('manager.bookings.update', $booking) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guest Selection -->
                            <div class="md:col-span-2">
                                <label for="user_id" class="block text-green-200 text-sm font-medium mb-2">Guest</label>
                                <select name="user_id" id="user_id" required 
                                        class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    @foreach($guests as $guest)
                                    <option value="{{ $guest->id }}" {{ $booking->user_id == $guest->id ? 'selected' : '' }}>
                                        {{ $guest->name }} ({{ $guest->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Room Selection -->
                            <div class="md:col-span-2">
                                <label for="room_id" class="block text-green-200 text-sm font-medium mb-2">Room</label>
                                <select name="room_id" id="room_id" required 
                                        class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} - {{ $room->type }} (₱{{ number_format($room->price, 2) }}/night)
                                    </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check-in Date -->
                            <div>
                                <label for="check_in_date" class="block text-green-200 text-sm font-medium mb-2">Check-in Date</label>
                                <input type="date" name="check_in_date" id="check_in_date" required
                                       value="{{ $booking->check_in_date }}"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('check_in_date')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Check-out Date -->
                            <div>
                                <label for="check_out_date" class="block text-green-200 text-sm font-medium mb-2">Check-out Date</label>
                                <input type="date" name="check_out_date" id="check_out_date" required
                                       value="{{ $booking->check_out_date }}"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('check_out_date')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Number of Guests -->
                            <div>
                                <label for="guests" class="block text-green-200 text-sm font-medium mb-2">Number of Guests</label>
                                <input type="number" name="guests" id="guests" required min="1" max="10"
                                       value="{{ $booking->guests }}"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('guests')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Price -->
                            <div>
                                <label for="total_price" class="block text-green-200 text-sm font-medium mb-2">Total Price (₱)</label>
                                <input type="number" name="total_price" id="total_price" required min="0" step="0.01"
                                       value="{{ $booking->total_price }}"
                                       class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                @error('total_price')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Special Requests -->
                            <div class="md:col-span-2">
                                <label for="special_requests" class="block text-green-200 text-sm font-medium mb-2">Special Requests</label>
                                <textarea name="special_requests" id="special_requests" rows="3"
                                          class="w-full px-4 py-3 bg-green-800/50 border border-green-700 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Any special requests or notes...">{{ $booking->special_requests }}</textarea>
                                @error('special_requests')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection