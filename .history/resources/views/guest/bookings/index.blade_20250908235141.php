@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            My Bookings
        </h2>
        <p class="text-xl text-gray-200">
            View and manage your bookings
        </p>
    </div>

    @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="px-6 py-4">Booking ID</th>
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Dates</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-700/50 transition-colors duration-200">
                        <td class="px-6 py-4 text-white">
                            #{{ $booking->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->room->name }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->guests }} guests</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_out->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-white">
                            {{ $booking->formatted_total_price }}
                        </td>
                        <td class="px-6 py-4">
                            {!! $booking->status_badge !!}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('guest.bookings.show', $booking) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                    View
                                </a>
                                @if(in_array($booking->status, ['pending', 'confirmed']))
                                <form action="{{ route('guest.bookings.cancel', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')"
                                            class="text-red-400 hover:text-red-300 transition-colors duration-200">
                                        Cancel
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            No bookings found. <a href="{{ route('guest.rooms') }}" class="text-blue-400 hover:underline">Book a room now</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-700 border-t border-gray-600">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
