@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Quick Book Room - {{ $room->name }}
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Create a new booking for this room
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.bookings.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                    </a>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <!-- Room Information Card -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 border border-green-700/30 mb-8">
                    <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                        <i class="fas fa-bed mr-3 text-purple-400"></i>Room Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-green-300 text-sm font-medium mb-2">Room Name</label>
                            <p class="text-green-50 font-medium">{{ $room->name }}</p>
                        </div>
                        <div>
                            <label class="block text-green-300 text-sm font-medium mb-2">Room Type</label>
                            <p class="text-green-50">{{ $room->type ?? 'Standard' }}</p>
                        </div>
                        <div>
                            <label class="block text-green-300 text-sm font-medium mb-2">Capacity</label>
                            <p class="text-green-50">{{ $room->capacity ?? 2 }} guests</p>
                        </div>
                        <div>
                            <label class="block text-green-300 text-sm font-medium mb-2">Price per Night</label>
                            <p class="text-green-50 font-medium">₱{{ number_format($room->price ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-green-300 text-sm font-medium mb-2">Status</label>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if(($room->status ?? 'available') === 'available') bg-green-500/20 text-green-400
                                @elseif(($room->status ?? 'available') === 'occupied') bg-red-500/20 text-red-400
                                @elseif(($room->status ?? 'available') === 'maintenance') bg-yellow-500/20 text-yellow-400
                                @endif">
                                {{ ucfirst($room->status ?? 'Available') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('manager.bookings.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <!-- Guest Selection -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 border border-green-700/30">
                        <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                            <i class="fas fa-user mr-3 text-blue-400"></i>Guest Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="user_id" class="block text-green-300 text-sm font-medium mb-2">Select Guest</label>
                                <select id="user_id" name="user_id" required
                                        class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400">
                                    <option value="">Choose a guest...</option>
                                    @foreach($guests as $guest)
                                    <option value="{{ $guest->id }}">{{ $guest->name }} ({{ $guest->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 border border-green-700/30">
                        <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                            <i class="fas fa-calendar-check mr-3 text-green-400"></i>Booking Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="check_in_date" class="block text-green-300 text-sm font-medium mb-2">Check-in Date</label>
                                <input type="datetime-local" 
                                       id="check_in_date" 
                                       name="check_in" 
                                       value="{{ old('check_in', date('Y-m-d\TH:i')) }}"
                                       min="{{ date('Y-m-d\TH:i') }}"
                                       required
                                       class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400 @error('check_in') border-red-500 @enderror">
                                @error('check_in')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="check_out_date" class="block text-green-300 text-sm font-medium mb-2">Check-out Date</label>
                                <input type="datetime-local" 
                                       id="check_out_date" 
                                       name="check_out" 
                                       value="{{ old('check_out', date('Y-m-d\TH:i', strtotime('+1 day'))) }}"
                                       min="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}"
                                       required
                                       class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400 @error('check_out') border-red-500 @enderror">
                                @error('check_out')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="guests" class="block text-green-300 text-sm font-medium mb-2">Number of Guests</label>
                                <input type="number" 
                                       id="guests" 
                                       name="guests" 
                                       value="{{ old('guests', 1) }}"
                                       min="1" 
                                       max="{{ $room->capacity ?? 10 }}"
                                       required
                                       class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400 @error('guests') border-red-500 @enderror">
                                @error('guests')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-green-400 text-sm mt-1">Maximum: {{ $room->capacity ?? 10 }} guests</p>
                            </div>
                            
                            <div>
                                <label for="status" class="block text-green-300 text-sm font-medium mb-2">Booking Status</label>
                                <select id="status" 
                                        name="status" 
                                        class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400">
                                    <option value="confirmed">Confirmed</option>
                                    <option value="pending">Pending</option>
                                    <option value="checked_in">Checked In</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="special_requests" class="block text-green-300 text-sm font-medium mb-2">Special Requests</label>
                            <textarea id="special_requests" 
                                      name="special_requests" 
                                      rows="4" 
                                      placeholder="Any special requests or notes..."
                                      class="w-full bg-green-800/50 border border-green-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:border-green-400">{{ old('special_requests') }}</textarea>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8 border border-green-700/30">
                        <h3 class="text-xl font-bold text-green-50 mb-6 flex items-center">
                            <i class="fas fa-calculator mr-3 text-yellow-400"></i>Price Summary
                        </h3>
                        <div id="price-breakdown">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-green-300">Room Rate per Night</span>
                                    <span class="text-green-50">₱{{ number_format($room->price ?? 0, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-300">Number of Nights</span>
                                    <span class="text-green-50" id="nights-count">1</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-300">Subtotal</span>
                                    <span class="text-green-50" id="subtotal">₱{{ number_format($room->price ?? 0, 2) }}</span>
                                </div>
                                <hr class="border-green-700">
                                <div class="flex justify-between font-bold text-lg">
                                    <span class="text-green-200">Total Amount</span>
                                    <span class="text-green-50" id="total-amount">₱{{ number_format($room->price ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="total_price" id="total_price_input" value="{{ $room->price ?? 0 }}">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-calendar-plus mr-2"></i>Create Booking
                        </button>
                        <a href="{{ route('manager.bookings.index') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-lg transition-colors duration-200 font-medium text-center">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    const roomPrice = {{ $room->price ?? 0 }};
    
    // Price calculation
    function calculateTotal() {
        const checkIn = document.getElementById('check_in_date').value;
        const checkOut = document.getElementById('check_out_date').value;
        
        if (checkIn && checkOut) {
            const startDate = new Date(checkIn);
            const endDate = new Date(checkOut);
            const timeDiff = endDate.getTime() - startDate.getTime();
            const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            if (nights > 0) {
                const subtotal = roomPrice * nights;
                const total = subtotal;
                
                document.getElementById('nights-count').textContent = nights;
                document.getElementById('subtotal').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
                document.getElementById('total-amount').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
                document.getElementById('total_price_input').value = total;
            }
        }
    }

    document.getElementById('check_in_date').addEventListener('change', calculateTotal);
    document.getElementById('check_out_date').addEventListener('change', calculateTotal);
    
    // Initial calculation
    calculateTotal();
</script>
@endpush
