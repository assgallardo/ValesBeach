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
            <h1 class="text-3xl font-bold text-white">Create Manual Reservation</h1>
            <p class="text-gray-400 mt-2">Add a new reservation for a guest</p>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-gray-800 rounded-lg p-8">
        <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Guest Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select Guest *</label>
                    <select name="user_id" required 
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Choose a guest...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Select Room *</label>
                    <select name="room_id" required id="room_select"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Choose a room...</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" 
                                    data-price="{{ $room->price }}"
                                    data-capacity="{{ $room->capacity }}"
                                    {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - ₱{{ number_format($room->price, 2) }}/night ({{ $room->capacity }} guests max)
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates and Guests -->
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
                    <input type="number" name="guests" required min="1" max="10"
                           value="{{ old('guests', 1) }}"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('guests')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Booking Status *</label>
                    <select name="status" required
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="pending" {{ old('status', 'confirmed') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', 'confirmed') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="checked_in" {{ old('status', 'confirmed') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
                        <option value="checked_out" {{ old('status', 'confirmed') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                        <option value="cancelled" {{ old('status', 'confirmed') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Estimated Total</label>
                    <div class="w-full px-4 py-3 bg-gray-600 border border-gray-600 rounded-lg text-white">
                        <span id="total_preview">Select room and dates</span>
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
                    Create Booking
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_select');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const guestsInput = document.querySelector('input[name="guests"]');
    const totalPreview = document.getElementById('total_preview');

    function updateTotalPreview() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (selectedRoom.value && checkInInput.value && checkOutInput.value && checkOutDate > checkInDate) {
            const pricePerNight = parseFloat(selectedRoom.dataset.price);
            const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            const total = pricePerNight * nights;
            
            totalPreview.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${nights} night${nights > 1 ? 's' : ''})`;
        } else {
            totalPreview.textContent = 'Select room and dates';
        }
    }

    function validateGuests() {
        const selectedRoom = roomSelect.options[roomSelect.selectedIndex];
        if (selectedRoom.value && guestsInput.value) {
            const capacity = parseInt(selectedRoom.dataset.capacity);
            const guests = parseInt(guestsInput.value);
            
            if (guests > capacity) {
                guestsInput.setCustomValidity(`This room can accommodate maximum ${capacity} guests`);
                guestsInput.style.borderColor = '#ef4444';
            } else {
                guestsInput.setCustomValidity('');
                guestsInput.style.borderColor = '#4b5563';
            }
        }
    }

    // Update room capacity limit when room changes
    roomSelect.addEventListener('change', function() {
        const selectedRoom = this.options[this.selectedIndex];
        if (selectedRoom.value) {
            const capacity = parseInt(selectedRoom.dataset.capacity);
            guestsInput.max = capacity;
            validateGuests();
        }
        updateTotalPreview();
    });

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
    guestsInput.addEventListener('input', validateGuests);

    // Initial calculation
    updateTotalPreview();
    validateGuests();
});

// Pre-select room if passed from quick book
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($selectedRoom))
        // Auto-select the room
        const roomSelect = document.getElementById('selected_room_id');
        if (roomSelect) {
            roomSelect.value = '{{ $selectedRoom->id }}';
        }
        
        // Auto-populate room selection
        selectedRoomData = {
            id: {{ $selectedRoom->id }},
            name: '{{ $selectedRoom->name }}',
            price: {{ $selectedRoom->price }},
            capacity: {{ $selectedRoom->capacity }}
        };
        
        // Show selected room info
        document.getElementById('selected_room_name').textContent = '{{ $selectedRoom->name }}';
        document.getElementById('selected_room_details').textContent = 'Capacity: {{ $selectedRoom->capacity }} guests • ₱{{ number_format($selectedRoom->price) }}/night';
        document.getElementById('selectedRoomSection').classList.remove('hidden');
        
        // Calculate initial total if dates are set
        const checkIn = document.getElementById('check_in').value;
        const checkOut = document.getElementById('check_out').value;
        if (checkIn && checkOut) {
            selectRoom({{ $selectedRoom->id }}, '{{ $selectedRoom->name }}', {{ $selectedRoom->price }}, {{ $selectedRoom->capacity }});
        }
        
        showNotification('Room {{ $selectedRoom->name }} pre-selected', 'success');
    @endif
});
</script>
@endsection
