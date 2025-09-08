@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <!-- Booking Header -->
            <div class="bg-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-white">
                        Booking #{{ $booking->id }}
                    </h2>
                    {!! $booking->status_badge !!}
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-400">Room</p>
                        <p class="text-white text-lg">{{ $booking->room->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Price</p>
                        <p class="text-white text-lg">{{ $booking->formatted_total_price }}</p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="p-6 space-y-6">
                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Check-in</h3>
                        <p class="text-white">{{ $booking->check_in->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-400 mb-1">Check-out</h3>
                        <p class="text-white">{{ $booking->check_out->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Guests -->
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-1">Number of Guests</h3>
                    <p class="text-white">{{ $booking->guests }} persons</p>
                </div>

                <!-- Special Requests -->
                @if($booking->special_requests)
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-1">Special Requests</h3>
                    <p class="text-white">{{ $booking->special_requests }}</p>
                </div>
                @endif

                <!-- Room Details -->
                <div>
                    <h3 class="text-sm font-medium text-gray-400 mb-2">Room Information</h3>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <p class="text-white mb-2">{{ $booking->room->description }}</p>
                        <p class="text-gray-300">Maximum capacity: {{ $booking->room->max_guests }} guests</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-4 border-t border-gray-600">
                    <a href="{{ route('guest.bookings') }}" 
                       class="text-gray-400 hover:text-white transition-colors duration-200">
                        ← Back to My Bookings
                    </a>
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('guest.bookings.cancel', $booking) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            Cancel Booking
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
