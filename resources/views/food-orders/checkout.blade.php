@extends('layouts.guest')

@section('title', 'Checkout - ValesBeach Resort')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-8">
        <a href="{{ route('guest.food-orders.cart') }}" 
           class="text-blue-600 hover:text-blue-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
    </div>

    <form action="{{ route('guest.food-orders.place-order') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Order Details Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Delivery Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery Information</h2>
                
                <!-- Delivery Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Type</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="delivery_type" value="room_service" 
                                   class="mr-2" {{ old('delivery_type', 'room_service') == 'room_service' ? 'checked' : '' }}
                                   onchange="toggleDeliveryLocation()">
                            <span class="font-medium">Room Service</span>
                            <span class="text-sm text-gray-600 ml-2">(+$5.00 delivery fee)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="delivery_type" value="pickup" 
                                   class="mr-2" {{ old('delivery_type') == 'pickup' ? 'checked' : '' }}
                                   onchange="toggleDeliveryLocation()">
                            <span class="font-medium">Pickup at Restaurant</span>
                            <span class="text-sm text-gray-600 ml-2">(No delivery fee)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="delivery_type" value="dining_room" 
                                   class="mr-2" {{ old('delivery_type') == 'dining_room' ? 'checked' : '' }}
                                   onchange="toggleDeliveryLocation()">
                            <span class="font-medium">Serve in Dining Room</span>
                            <span class="text-sm text-gray-600 ml-2">(No delivery fee)</span>
                        </label>
                    </div>
                    @error('delivery_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Delivery Location -->
                <div class="mb-4" id="delivery-location-section">
                    <label for="delivery_location" class="block text-sm font-medium text-gray-700 mb-2">
                        <span id="location-label">Room Number</span>
                    </label>
                    <input type="text" name="delivery_location" id="delivery_location" 
                           value="{{ old('delivery_location', $currentBooking ? $currentBooking->room->room_number ?? '' : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                           placeholder="Enter room number">
                    @error('delivery_location')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Requested Delivery Time -->
                <div class="mb-4">
                    <label for="requested_delivery_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Preferred Delivery Time (Optional)
                    </label>
                    <input type="datetime-local" name="requested_delivery_time" id="requested_delivery_time" 
                           value="{{ old('requested_delivery_time') }}"
                           min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-sm text-gray-600 mt-1">Leave blank for ASAP delivery (estimated 30-45 minutes)</p>
                    @error('requested_delivery_time')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Special Instructions -->
                <div class="mb-4">
                    <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                        Special Instructions (Optional)
                    </label>
                    <textarea name="special_instructions" id="special_instructions" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2"
                              placeholder="Any special requests or dietary considerations...">{{ old('special_instructions') }}</textarea>
                    @error('special_instructions')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            @if($currentBooking)
            <!-- Booking Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Current Booking</h3>
                <p class="text-blue-800 text-sm">
                    <strong>Booking:</strong> {{ $currentBooking->booking_number }}<br>
                    @if($currentBooking->room)
                    <strong>Room:</strong> {{ $currentBooking->room->room_number }} - {{ $currentBooking->room->room_type }}<br>
                    @endif
                    <strong>Dates:</strong> {{ $currentBooking->check_in_date->format('M j') }} - {{ $currentBooking->check_out_date->format('M j, Y') }}
                </p>
            </div>
            @endif
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <!-- Order Items -->
                <div class="space-y-3 mb-6">
                    @foreach($cartItems as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $item['menu_item']->name }}</h4>
                            <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }}</p>
                            @if($item['special_instructions'])
                            <p class="text-xs text-gray-500 italic">{{ $item['special_instructions'] }}</p>
                            @endif
                        </div>
                        <span class="font-semibold text-gray-900 ml-2">
                            ${{ number_format($item['total'], 2) }}
                        </span>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pricing Breakdown -->
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span class="font-semibold" id="delivery-fee">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (8%)</span>
                        <span class="font-semibold" id="tax-amount">$0.00</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span id="final-total">$0.00</span>
                        </div>
                    </div>
                </div>
                
                <!-- Place Order Button -->
                <button type="submit" 
                        class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-semibold transition duration-200">
                    Place Order
                </button>
                
                <p class="text-xs text-gray-500 mt-3 text-center">
                    By placing this order, you agree to our terms and conditions. 
                    Payment will be processed upon delivery.
                </p>
            </div>
        </div>
    </form>
</div>

<!-- Checkout JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    toggleDeliveryLocation();
    
    // Update totals when delivery type changes
    document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            calculateTotal();
            toggleDeliveryLocation();
        });
    });
});

function toggleDeliveryLocation() {
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
    const locationSection = document.getElementById('delivery-location-section');
    const locationLabel = document.getElementById('location-label');
    const locationInput = document.getElementById('delivery_location');
    
    if (deliveryType === 'room_service') {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Room Number';
        locationInput.placeholder = 'Enter room number';
        locationInput.required = true;
    } else if (deliveryType === 'pickup') {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Contact Number';
        locationInput.placeholder = 'Enter phone number for pickup notification';
        locationInput.required = true;
    } else {
        locationSection.style.display = 'block';
        locationLabel.textContent = 'Table Preference (Optional)';
        locationInput.placeholder = 'Preferred seating area or table number';
        locationInput.required = false;
    }
}

function calculateTotal() {
    const subtotal = {{ $subtotal }};
    const deliveryType = document.querySelector('input[name="delivery_type"]:checked').value;
    
    // Calculate delivery fee
    const deliveryFee = deliveryType === 'room_service' ? 5.00 : 0.00;
    
    // Calculate tax (8% on subtotal + delivery fee)
    const taxAmount = (subtotal + deliveryFee) * 0.08;
    
    // Calculate total
    const total = subtotal + deliveryFee + taxAmount;
    
    // Update display
    document.getElementById('delivery-fee').textContent = '$' + deliveryFee.toFixed(2);
    document.getElementById('tax-amount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('final-total').textContent = '$' + total.toFixed(2);
}
</script>
@endsection
