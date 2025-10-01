@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Booking Details
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Booking Reference: {{ $booking->booking_reference }}
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.bookings.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Bookings
                    </a>
                    <a href="{{ route('manager.bookings.edit', $booking) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg ml-3 transition-colors duration-200">
                        Edit Booking
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Booking Details -->
                <div class="lg:col-span-2">
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 mb-8">
                        <h3 class="text-2xl font-bold text-green-50 mb-6">Booking Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Booking Reference</label>
                                <p class="text-green-50 text-lg font-medium">{{ $booking->booking_reference }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Status</label>
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                    @elseif($booking->status === 'completed') bg-purple-100 text-purple-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Check-in Date</label>
                                <p class="text-green-50">{{ Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Check-out Date</label>
                                <p class="text-green-50">{{ Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Number of Guests</label>
                                <p class="text-green-50">{{ $booking->guests }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Total Price</label>
                                <p class="text-green-50 text-lg font-bold">₱{{ number_format($booking->total_price, 2) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Created Date</label>
                                <p class="text-green-50">{{ $booking->created_at->format('F d, Y g:i A') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-green-200 text-sm font-medium mb-2">Nights</label>
                                <p class="text-green-50">{{ Carbon\Carbon::parse($booking->check_in_date)->diffInDays(Carbon\Carbon::parse($booking->check_out_date)) }}</p>
                            </div>
                        </div>
                        
                        @if($booking->special_requests)
                        <div class="mt-6">
                            <label class="block text-green-200 text-sm font-medium mb-2">Special Requests</label>
                            <p class="text-green-50 bg-green-800/30 p-4 rounded-lg">{{ $booking->special_requests }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Guest and Room Information -->
                <div class="lg:col-span-1">
                    <!-- Guest Information -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-green-50 mb-4">Guest Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Name</label>
                                <p class="text-green-50">{{ $booking->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Email</label>
                                <p class="text-green-50">{{ $booking->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Phone</label>
                                <p class="text-green-50">{{ $booking->user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Room Information -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-6">
                        <h3 class="text-xl font-bold text-green-50 mb-4">Room Information</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Room Name</label>
                                <p class="text-green-50">{{ $booking->room->name }}</p>
                            </div>
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Room Type</label>
                                <p class="text-green-50">{{ $booking->room->type }}</p>
                            </div>
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Capacity</label>
                                <p class="text-green-50">{{ $booking->room->capacity }} guests</p>
                            </div>
                            <div>
                                <label class="block text-green-200 text-sm font-medium">Price per Night</label>
                                <p class="text-green-50">₱{{ number_format($booking->room->price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6">
                        <h3 class="text-xl font-bold text-green-50 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            @if($booking->status === 'pending')
                            <form action="{{ route('manager.bookings.confirm', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                                    Confirm Booking
                                </button>
                            </form>
                            @endif
                            
                            @if($booking->status === 'confirmed')
                            <form action="{{ route('manager.bookings.checkin', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition-colors">
                                    Check In Guest
                                </button>
                            </form>
                            @endif
                            
                            @if($booking->status === 'checked_in')
                            <form action="{{ route('manager.bookings.checkout', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition-colors">
                                    Check Out Guest
                                </button>
                            </form>
                            @endif
                            
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                            <form action="{{ route('manager.bookings.cancel', $booking) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors"
                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                    Cancel Booking
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection