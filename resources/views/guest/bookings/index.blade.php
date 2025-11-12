@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header with View History Button -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">My Bookings</h1>
        <a href="{{ route('guest.bookings.history') }}" 
           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            <i class="fas fa-history mr-2"></i>
            View History
        </a>
    </div>

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
                                Check-in: {{ $booking->check_in->format('M d, Y') }} at {{ $booking->room->check_in_time ? \Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A') : '12:00 AM' }}
                            </p>
                            <p class="text-gray-300">
                                Check-out: {{ $booking->check_out->format('M d, Y') }} at {{ $booking->room->check_out_time ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A') : '12:00 AM' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-green-400">
                                @php
                                    // Compute a reliable fallback total for display
                                    // Same-day bookings count as 1 night/day
                                    $checkInVal = $booking->check_in ?? null;
                                    $checkOutVal = $booking->check_out ?? null;
                                    $checkIn = $checkInVal ? \Carbon\Carbon::parse($checkInVal)->startOfDay() : null;
                                    $checkOut = $checkOutVal ? \Carbon\Carbon::parse($checkOutVal)->startOfDay() : null;
                                    $nights = ($checkIn && $checkOut) ? $checkIn->diffInDays($checkOut) : 0;
                                    // diffInDays returns float, use == not ===
                                    if ($nights == 0) { $nights = 1; }
                                    $roomPrice = optional($booking->room)->price ?? 0;
                                    $fallbackTotal = $roomPrice * $nights;
                                    $rawTotal = (float)($booking->total_price ?? 0);
                                    $displayTotal = $rawTotal > 0 ? $rawTotal : $fallbackTotal;
                                @endphp
                                {{ '₱' . number_format($displayTotal, 2) }}
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
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <!-- View Details Button -->
                            <a href="{{ route('guest.bookings.show', $booking) }}" 
                               class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            
                            <!-- Payment Button (if payment is needed) -->
                            @if($booking->remaining_balance > 0)
                            <a href="{{ route('payments.create', $booking) }}" 
                               class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-credit-card mr-1"></i>Pay Now
                            </a>
                            @endif
                            
                            <!-- Invoice Button (if invoice exists) -->
                            @if($booking->invoice)
                            <a href="{{ route('invoices.show', $booking->invoice) }}" 
                               class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                <i class="fas fa-file-invoice mr-1"></i>Invoice
                            </a>
                            @endif
                            
                            <!-- Cancel Booking Button -->
                            <form action="{{ route('guest.bookings.cancel', $booking) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this booking?')"
                                  class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- View Details Button for other statuses -->
                        <div class="mt-4 flex flex-wrap justify-end gap-2">
                            <a href="{{ route('guest.bookings.show', $booking) }}" 
                               class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            
                            <!-- Invoice Button (if invoice exists) -->
                            @if($booking->invoice)
                            <a href="{{ route('invoices.show', $booking->invoice) }}" 
                               class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                <i class="fas fa-file-invoice mr-1"></i>Invoice
                            </a>
                            @endif
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
