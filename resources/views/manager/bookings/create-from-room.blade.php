@extends('layouts.manager')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8 flex items-center">
        <a href="{{ route('manager.bookings.index') }}" 
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
        
        <form action="{{ route('manager.bookings.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="room_id" value="{{ $room->id }}">
            
            <!-- Guest Selection or Creation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Guest Information *</label>
                    
                    <!-- Guest Type Selection -->
                    <div class="flex space-x-4 mb-4">
                        <label class="flex items-center">
                            <input type="radio" name="guest_type" value="existing" checked class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500" onchange="toggleGuestFields('existing')">
                            <span class="ml-2 text-gray-300">Select Existing Guest</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="guest_type" value="new" class="text-green-600 bg-gray-700 border-gray-600 focus:ring-green-500" onchange="toggleGuestFields('new')">
                            <span class="ml-2 text-gray-300">Create New Guest</span>
                        </label>
                    </div>

                    <!-- Existing Guest Selection -->
                    <div id="existing_guest_section" class="space-y-4">
                        <select name="user_id" id="user_id"
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
                    <div id="new_guest_section" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Guest Name *</label>
                            <input type="text" name="guest_name" id="guest_name"
                                   value="{{ old('guest_name') }}"
                                   placeholder="Enter guest full name"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Guest Email *</label>
                            <input type="email" name="guest_email" id="guest_email"
                                   value="{{ old('guest_email') }}"
                                   placeholder="Enter guest email address"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                           value="{{ old('check_out', date('Y-m-d')) }}"
                           min="{{ date('Y-m-d') }}"
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

            <!-- Early Check-in and Late Checkout Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-600 rounded-lg p-4 bg-gray-700 bg-opacity-50">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="early_checkin" value="1" {{ old('early_checkin') ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 bg-gray-600 border-gray-500 rounded focus:ring-green-500">
                        <span class="ml-3 text-white font-medium">Early Check-in</span>
                    </label>
                    <p class="text-gray-400 text-sm mt-2 ml-8">
                        Check-in before standard time (Fee: ₱500)
                    </p>
                    <div class="ml-8 mt-2">
                        <input type="time" name="early_checkin_time" value="{{ old('early_checkin_time') }}"
                               class="px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-sm">
                    </div>
                </div>

                <div class="border border-gray-600 rounded-lg p-4 bg-gray-700 bg-opacity-50">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="late_checkout" value="1" {{ old('late_checkout') ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 bg-gray-600 border-gray-500 rounded focus:ring-green-500">
                        <span class="ml-3 text-white font-medium">Late Check-out</span>
                    </label>
                    <p class="text-gray-400 text-sm mt-2 ml-8">
                        Check-out after standard time (Fee: ₱500)
                    </p>
                    <div class="ml-8 mt-2">
                        <input type="time" name="late_checkout_time" value="{{ old('late_checkout_time') }}"
                               class="px-3 py-2 bg-gray-600 border border-gray-500 rounded text-white text-sm">
                    </div>
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
                <a href="{{ route('manager.bookings.index') }}" 
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
// Toggle guest fields based on selection
function toggleGuestFields(type) {
    const existingSection = document.getElementById('existing_guest_section');
    const newSection = document.getElementById('new_guest_section');
    const userIdSelect = document.getElementById('user_id');
    const guestNameInput = document.getElementById('guest_name');
    const guestEmailInput = document.getElementById('guest_email');
    
    if (type === 'existing') {
        existingSection.style.display = 'block';
        newSection.style.display = 'none';
        // Clear new guest fields
        guestNameInput.value = '';
        guestEmailInput.value = '';
    } else {
        existingSection.style.display = 'none';
        newSection.style.display = 'grid';
        // Clear existing guest selection
        userIdSelect.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const totalPreview = document.getElementById('total_preview');
    const breakdownPreview = document.getElementById('breakdown_preview');
    const pricePerNight = {{ $room->price }};
    
    // Get the form
    const form = document.querySelector('form');

    function updateTotalPreview() {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (checkInInput.value && checkOutInput.value && checkOutDate >= checkInDate) {
            let nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            
            // Same-day booking counts as 1 night
            if (nights === 0) {
                nights = 1;
            }
            
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
        // Allow same-day booking - min checkout is same as check-in
        checkOutInput.min = this.value;
        
        // If checkout is before check-in, set it to check-in date (same-day booking)
        if (checkOutInput.value && new Date(checkOutInput.value) < checkInDate) {
            checkOutInput.value = this.value;
        }
        
        updateTotalPreview();
    });

    checkOutInput.addEventListener('change', updateTotalPreview);

    // Initial calculation
    updateTotalPreview();
});
</script>
@endsection
