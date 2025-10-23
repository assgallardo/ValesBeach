@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold text-white">Cottage Booking Details</h1>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $booking->status_color }}">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            <p class="text-gray-300">Booking Reference: <span class="font-mono text-blue-400">{{ $booking->booking_reference }}</span></p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Cottage Details -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-white mb-4">Cottage Details</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Cottage Name:</span>
                            <span class="text-white font-semibold">{{ $booking->cottage->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Location:</span>
                            <span class="text-white">{{ $booking->cottage->location ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Capacity:</span>
                            <span class="text-white">{{ $booking->cottage->capacity }} persons</span>
                        </div>
                    </div>
                </div>

                <!-- Booking Information -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-white mb-4">Booking Information</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Booking Type:</span>
                            <span class="text-white font-semibold">{{ ucfirst(str_replace('_', ' ', $booking->booking_type)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Check-in:</span>
                            <span class="text-white">{{ $booking->check_in_date->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($booking->booking_type === 'overnight')
                        <div class="flex justify-between">
                            <span class="text-gray-400">Check-out:</span>
                            <span class="text-white">{{ $booking->check_out_date->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Duration:</span>
                            <span class="text-white">{{ $booking->check_in_date->diffInDays($booking->check_out_date) }} night(s)</span>
                        </div>
                        @endif
                        @if($booking->booking_type === 'hourly' && $booking->hours_booked)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Hours Booked:</span>
                            <span class="text-white">{{ $booking->hours_booked }} hours</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-400">Guests:</span>
                            <span class="text-white">{{ $booking->guests }} adults{{ $booking->children ? ', ' . $booking->children . ' children' : '' }}</span>
                        </div>
                        @if($booking->special_requests)
                        <div class="pt-3 border-t border-gray-700">
                            <p class="text-gray-400 mb-2">Special Requests:</p>
                            <p class="text-white">{{ $booking->special_requests }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-white mb-4">Payment Information</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Amount:</span>
                            <span class="text-white font-bold text-xl">{{ $booking->formatted_total_price }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Amount Paid:</span>
                            <span class="text-green-400 font-semibold">{{ $booking->formatted_amount_paid }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Remaining Balance:</span>
                            <span class="text-yellow-400 font-semibold">{{ $booking->formatted_remaining_balance }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-700">
                            <span class="text-gray-400">Payment Status:</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $booking->payment_status_color }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Panel -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg p-6 sticky top-4 space-y-4">
                    <h3 class="text-white font-semibold mb-4">Actions</h3>

                    @if($booking->payment_status === 'pending' || $booking->payment_status === 'partial')
                        <a href="{{ route('payments.create', ['type' => 'cottage_booking', 'id' => $booking->id]) }}" 
                           class="block w-full text-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 font-semibold">
                            Make Payment
                        </a>
                    @endif

                    @if($booking->canBeCancelled())
                        <form action="{{ route('guest.cottage-bookings.cancel', $booking) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                            @csrf
                            <button type="submit" 
                                    class="block w-full text-center bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors duration-200">
                                Cancel Booking
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('guest.cottage-bookings.index') }}" 
                       class="block w-full text-center bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        View All Bookings
                    </a>

                    <a href="{{ route('guest.dashboard') }}" 
                       class="block w-full text-center bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Back to Dashboard
                    </a>

                    @if($booking->payment_status === 'pending')
                        <div class="bg-yellow-500/20 border border-yellow-500 rounded-lg p-3 mt-4">
                            <p class="text-yellow-300 text-sm">
                                <strong>Payment Required:</strong> Please complete your payment to confirm this booking.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
