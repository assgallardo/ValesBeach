@extends('layouts.admin')

@section('content')
    <header class="bg-green-900 shadow">
        <div class="container mx-auto px-4 lg:px-16 py-6">
            <h2 class="text-2xl font-semibold text-white">
                Booking Details
            </h2>
        </div>
    </header>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.bookings') }}" class="inline-flex items-center text-green-100 hover:text-green-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Bookings
        </a>
    </div>

    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-green-50">Booking Details</h1>
        <p class="text-green-100 mt-2">Booking #{{ $booking->id }}</p>
    </div>

    <!-- Booking Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Guest Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Guest Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Name</label>
                    <p class="text-green-50">{{ $booking->user->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Email</label>
                    <p class="text-green-50">{{ $booking->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Room Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Room Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Room Name</label>
                    <p class="text-green-50">{{ $booking->room->name }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Room Type</label>
                    <p class="text-green-50">{{ ucfirst($booking->room->type) }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Price per Night</label>
                    <p class="text-green-50">₱{{ number_format($booking->room->price_per_night, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Booking Details</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Check-in Date</label>
                    <p class="text-green-50">{{ $booking->check_in->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Check-out Date</label>
                    <p class="text-green-50">{{ $booking->check_out->format('F d, Y') }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Nights</label>
                    <p class="text-green-50">{{ $booking->check_in->diffInDays($booking->check_out) }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Number of Guests</label>
                    <p class="text-green-50">{{ $booking->guests }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-green-50 mb-4">Payment Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-green-200">Total Amount</label>
                    <p class="text-green-50">₱{{ number_format($booking->total_price, 2) }}</p>
                </div>
                <div>
                    <label class="text-sm text-green-200">Status</label>
                    <span class="inline-block px-3 py-1 rounded-full text-sm mt-1
                        @if($booking->status === 'confirmed') bg-green-500 text-white
                        @elseif($booking->status === 'pending') bg-yellow-500 text-black
                        @elseif($booking->status === 'cancelled') bg-red-500 text-white
                        @elseif($booking->status === 'checked_in') bg-blue-500 text-white
                        @else bg-gray-500 text-white
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Special Requests -->
    @if($booking->special_requests)
    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-green-50 mb-4">Special Requests</h2>
        <p class="text-green-100">{{ $booking->special_requests }}</p>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-green-50 mb-4">Update Status</h2>
        <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="flex items-center space-x-4">
            @csrf
            @method('PATCH')
            <select name="status" 
                    class="px-4 py-2 bg-gray-700 text-white rounded border border-gray-600 focus:border-green-500">
                @foreach(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $booking->status === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500 transition-colors">
                Update Status
            </button>
        </form>
    </div>
</div>
@endsection
