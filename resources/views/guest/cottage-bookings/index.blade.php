@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">My Cottage Bookings</h1>
        <p class="text-gray-300">View and manage your Bahay Kubo reservations</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($bookings->isEmpty())
        <div class="bg-gray-800 rounded-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <h3 class="text-xl font-semibold text-white mb-2">No Cottage Bookings Yet</h3>
            <p class="text-gray-400 mb-6">You haven't booked any cottages yet. Start exploring our beautiful Bahay Kubo!</p>
            <a href="{{ route('guest.cottages.index') }}" class="inline-block bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                Browse Cottages
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <div class="bg-gray-800 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-1">{{ $booking->cottage->name }}</h3>
                            <p class="text-gray-400 text-sm font-mono">{{ $booking->booking_reference }}</p>
                        </div>
                        <div class="flex gap-2 mt-3 md:mt-0">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $booking->status_color }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $booking->payment_status_color }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div>
                            <p class="text-gray-400 text-sm">Booking Type</p>
                            <p class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Check-in</p>
                            <p class="text-white">{{ $booking->check_in_date->format('M d, Y') }}</p>
                        </div>
                        @if($booking->booking_type === 'overnight')
                        <div>
                            <p class="text-gray-400 text-sm">Check-out</p>
                            <p class="text-white">{{ $booking->check_out_date->format('M d, Y') }}</p>
                        </div>
                        @elseif($booking->booking_type === 'hourly')
                        <div>
                            <p class="text-gray-400 text-sm">Hours</p>
                            <p class="text-white">{{ $booking->hours_booked }} hrs</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-gray-400 text-sm">Total Amount</p>
                            <p class="text-green-400 font-bold">{{ $booking->formatted_total_price }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('guest.cottage-bookings.show', $booking) }}" 
                           class="flex-1 text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                            View Details
                        </a>
                        
                        @if($booking->payment_status === 'pending' || $booking->payment_status === 'partial')
                            <a href="{{ route('payments.create', ['type' => 'cottage_booking', 'id' => $booking->id]) }}" 
                               class="flex-1 text-center bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                Pay Now
                            </a>
                        @endif

                        @if($booking->canBeCancelled())
                            <form action="{{ route('guest.cottage-bookings.cancel', $booking) }}" method="POST" class="flex-1"
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="mt-8 text-center">
        <a href="{{ route('guest.dashboard') }}" class="text-blue-400 hover:text-blue-300 transition-colors">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
