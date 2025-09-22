@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">My Bookings</h1>

    @if($bookings->isEmpty())
        <div class="bg-green-900/50 rounded-lg p-8 text-center">
            <p class="text-gray-300 text-lg">You haven't made any bookings yet.</p>
            <a href="{{ route('guest.rooms.browse') }}" 
               class="inline-block mt-4 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Browse Rooms
            </a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($bookings as $booking)
                <div class="bg-green-900/50 rounded-lg p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                {{ $booking->room->name }}
                            </h3>
                            <p class="text-gray-300">
                                Check-in: {{ $booking->check_in->format('M d, Y \a\t g:i A') }}
                            </p>
                            <p class="text-gray-300">
                                Check-out: {{ $booking->check_out->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                {{ $booking->formatted_total_price }}
                            </p>
                            <p class="text-sm text-gray-400 mt-2">
                                Status: <span class="capitalize px-2 py-1 rounded text-xs font-medium
                                    @if($booking->status === 'confirmed') bg-green-600 text-white
                                    @elseif($booking->status === 'pending') bg-yellow-600 text-white
                                    @elseif($booking->status === 'checked_in') bg-blue-600 text-white
                                    @elseif($booking->status === 'checked_out') bg-gray-600 text-white
                                    @elseif($booking->status === 'cancelled') bg-red-600 text-white
                                    @else bg-gray-600 text-white @endif">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($booking->status === 'pending' || $booking->status === 'confirmed')
                        <div class="mt-4 flex justify-end gap-3">
                            <!-- View Details Button -->
                            <a href="{{ route('guest.bookings.show', $booking) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                View Details
                            </a>
                            
                            <!-- Cancel Booking Button -->
                            <form action="{{ route('guest.bookings.cancel', $booking) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    Cancel Booking
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- View Details Button for other statuses -->
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('guest.bookings.show', $booking) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                View Details
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
