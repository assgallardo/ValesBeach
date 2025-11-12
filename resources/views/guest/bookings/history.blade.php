@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <h1 class="text-3xl font-bold text-white mb-8">Booking History</h1>
    @if($bookings->isEmpty())
        <p class="text-gray-300">No booking history to display yet.</p>
    @else
        <div class="grid gap-6">
            @foreach($bookings as $booking)
                <div class="bg-green-900/50 rounded-lg p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-2">
                                {{ $booking->room->name ?? 'Room' }}
                            </h3>
                            <p class="text-gray-300">
                                Check-in: {{ \Carbon\Carbon::parse($booking->check_in ?? $booking->check_in_date)->format('M d, Y') }} at {{ $booking->room && $booking->room->check_in_time ? \Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A') : '12:00 AM' }}
                            </p>
                            <p class="text-gray-300">
                                Check-out: {{ \Carbon\Carbon::parse($booking->check_out ?? $booking->check_out_date)->format('M d, Y') }} at {{ $booking->room && $booking->room->check_out_time ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A') : '12:00 AM' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                ₱{{ number_format($booking->total_price, 2) }}
                            </p>
                            <p class="text-sm text-gray-400 mt-2">
                                Status: <span class="capitalize px-2 py-1 rounded text-xs font-medium
                                    @if($booking->status === 'checked_out') bg-gray-600 text-white
                                    @elseif($booking->status === 'cancelled') bg-red-600 text-white
                                    @else bg-gray-600 text-white @endif">
                                    {{ str_replace('_', ' ', $booking->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('guest.bookings.show', $booking) }}" 
                           class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </a>
                        @if(isset($booking->invoice))
                        <a href="{{ route('invoices.show', $booking->invoice) }}" 
                           class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                            <i class="fas fa-file-invoice mr-1"></i>Invoice
                        </a>
                        @endif
                        @if($booking->status === 'cancelled')
                        <form action="{{ route('guest.bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this cancelled booking?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-700 text-white text-sm rounded hover:bg-red-800" title="Delete booking">
                                &times;
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection