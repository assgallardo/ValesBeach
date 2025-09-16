@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8 flex items-center">
        <a href="{{ route('admin.reservations') }}" 
           class="inline-flex items-center text-gray-400 hover:text-white mr-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reservations
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Reserve Room: {{ $room->name }}</h1>
            <p class="text-gray-400 mt-2">Create a reservation for this specific room</p>
        </div>
    </div>

    <!-- Room Information Card -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Room Details</h3>
                <p class="text-gray-300"><strong>Name:</strong> {{ $room->name }}</p>
                <p class="text-gray-300"><strong>Type:</strong> {{ $room->type ?? 'Standard' }}</p>
                <p class="text-gray-300"><strong>Capacity:</strong> {{ $room->capacity }} guests</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Pricing</h3>
                <p class="text-green-400 text-2xl font-bold">₱{{ number_format($room->price, 2) }}</p>
                <p class="text-gray-400">per night</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Amenities</h3>
                <p class="text-gray-300">{{ $room->description ?? 'Standard amenities included' }}</p>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-gray-800 rounded-lg p-8">
        <h2 class="text-xl font-semibold text-white mb-6">Guest & Booking Details</h2>
        
        <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            
            <!-- Guest Selection or Creation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <div x-data="{ guestType: 'existing' }">
                        <label class="block text-sm font-medium text-gray-300 mb-3">Guest Information *</label>
                        
                        <!-- Guest Type Selection -->
                        <div class="flex space-x-4 mb-4">
                            <label class="flex items-center">
                                <input type="radio" x-model="guestType" value="existing" class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-300">Select Existing Guest</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="guestType" value="new" class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-300">Create New Guest</span>
                            </label>
                        </div>

                        <!-- Existing Guest Selection -->
                        <div x-show="guestType === 'existing'" class="space-y-4">
                            <select name="user_id" :required="guestType === 'existing'"
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Choose a guest...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- New Guest Creation -->
                        <div x-show="guestType === 'new'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Guest Name *</label>
                                <input type="text" name="guest_name" :required="guestType === 'new'"
                                       value="{{ old('guest_name') }}"
                                       placeholder="Enter guest full name"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-400 mb-1">Guest Email *</label>
                                <input type="email" name="guest_email" :required="guestType === 'new'"
                                       value="{{ old('guest_email') }}"
                                       placeholder="Enter guest email address"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                    @error('user_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('guest_name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('guest_email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Booking Details -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Check-in Date *</label>
                    <input type="date" name="check_in" required id="check_in"
                           value="{{ old('check_in', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('check_in')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Check-out Date *</label>
                    <input type="date" name="check_out" required id="check_out"
                           value="{{ old('check_out', date('Y-m-d', strtotime('+1 day'))) }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('check_out')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Number of Guests *</label>
                    <input type="number" name="guests" required min="1" max="{{ $room->capacity }}"
                           value="{{ old('guests', 1) }}"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-sm text-gray-400 mt-1">Maximum: {{ $room->capacity }} guests</p>
                    @error('guests')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status and Price Preview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Booking Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="checked_in" {{ old('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="checked_out" {{ old('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Total Amount</label>
                    <div class="w-full px-4 py-3 bg-gray-600 border border-gray-600 rounded-lg">
                        <div class="text-green-400 text-xl font-bold" id="total_preview">
                            Select dates to calculate
                        </div>
                        <div class="text-gray-400 text-sm" id="breakdown_preview">
                            ₱{{ number_format($room->price, 2) }} per night
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6">
                <a href="{{ route('admin.bookings') }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                    Create Booking for {{ $room->name }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const totalPreview = document.getElementById('total_preview');
    const breakdownPreview = document.getElementById('breakdown_preview');
    const pricePerNight = {{ $room->price_per_night }};

    function updateTotalPreview() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
            const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            const total = pricePerNight * nights;
            
            totalPreview.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            breakdownPreview.textContent = `₱${pricePerNight.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} × ${nights} night${nights > 1 ? 's' : ''}`;
        } else {
            totalPreview.textContent = 'Select dates to calculate';
            breakdownPreview.textContent = `₱${pricePerNight.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} per night`;
        }
    }

    // Update check-out minimum date when check-in changes
    checkInInput.addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        const nextDay = new Date(checkInDate);
        nextDay.setDate(nextDay.getDate() + 1);
        checkOutInput.min = nextDay.toISOString().split('T')[0];
        
        if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
            checkOutInput.value = nextDay.toISOString().split('T')[0];
        }
        
        updateTotalPreview();
    });

    checkOutInput.addEventListener('change', updateTotalPreview);

    // Initial calculation
    updateTotalPreview();
});
</script>
@endsection
