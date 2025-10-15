@extends('layouts.guest')

@section('content')
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16 max-w-4xl">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Book Service
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Request booking for {{ $service->name }}
            </p>
            <div class="mt-6">
                <a href="{{ route('guest.services.show', $service) }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Service Details
                </a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="bg-green-600/20 border border-green-500/50 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-3"></i>
                <span class="text-green-100">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-600/20 border border-red-500/50 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                <span class="text-red-100">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Service Summary Card -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-6 mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-green-800/50 rounded-lg flex items-center justify-center">
                    @if($service->category === 'spa')
                    <i class="fas fa-spa text-2xl text-green-400"></i>
                    @elseif($service->category === 'dining')
                    <i class="fas fa-utensils text-2xl text-orange-400"></i>
                    @elseif($service->category === 'transportation')
                    <i class="fas fa-car text-2xl text-blue-400"></i>
                    @elseif($service->category === 'activities')
                    <i class="fas fa-swimmer text-2xl text-purple-400"></i>
                    @else
                    <i class="fas fa-concierge-bell text-2xl text-yellow-400"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-green-50">{{ $service->name }}</h3>
                    <p class="text-green-300">{{ ucfirst(str_replace('_', ' ', $service->category)) }}</p>
                    <p class="text-green-400 font-semibold">₱{{ number_format($service->price, 2) }}</p>
                </div>
                @if($service->duration)
                <div class="text-right">
                    <p class="text-green-200 text-sm">Duration</p>
                    <p class="text-green-50 font-semibold">
                        @if($service->duration >= 60)
                            {{ floor($service->duration / 60) }}h {{ $service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '' }}
                        @else
                            {{ $service->duration }}m
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Booking Form -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 p-8">
            <form action="{{ route('guest.services.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Hidden Fields for Controller -->
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                <input type="hidden" name="service_type" value="{{ $service->name }}">
                <input type="hidden" name="description" id="description" value="Guest booking for {{ $service->name }} - Service request">

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="requested_date" class="block text-green-200 text-sm font-medium mb-2">
                            Preferred Date <span class="text-red-400">*</span>
                        </label>
                        <input type="date" 
                               id="requested_date" 
                               name="requested_date" 
                               value="{{ old('requested_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               required
                               class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        @error('requested_date')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="requested_time" class="block text-green-200 text-sm font-medium mb-2">
                            Preferred Time <span class="text-red-400">*</span>
                        </label>
                        <select id="requested_time" 
                                name="requested_time" 
                                required
                                class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="">Select Time</option>
                            <option value="08:00" {{ old('requested_time') === '08:00' ? 'selected' : '' }}>8:00 AM</option>
                            <option value="09:00" {{ old('requested_time') === '09:00' ? 'selected' : '' }}>9:00 AM</option>
                            <option value="10:00" {{ old('requested_time') === '10:00' ? 'selected' : '' }}>10:00 AM</option>
                            <option value="11:00" {{ old('requested_time') === '11:00' ? 'selected' : '' }}>11:00 AM</option>
                            <option value="12:00" {{ old('requested_time') === '12:00' ? 'selected' : '' }}>12:00 PM</option>
                            <option value="13:00" {{ old('requested_time') === '13:00' ? 'selected' : '' }}>1:00 PM</option>
                            <option value="14:00" {{ old('requested_time') === '14:00' ? 'selected' : '' }}>2:00 PM</option>
                            <option value="15:00" {{ old('requested_time') === '15:00' ? 'selected' : '' }}>3:00 PM</option>
                            <option value="16:00" {{ old('requested_time') === '16:00' ? 'selected' : '' }}>4:00 PM</option>
                            <option value="17:00" {{ old('requested_time') === '17:00' ? 'selected' : '' }}>5:00 PM</option>
                            <option value="18:00" {{ old('requested_time') === '18:00' ? 'selected' : '' }}>6:00 PM</option>
                            <option value="19:00" {{ old('requested_time') === '19:00' ? 'selected' : '' }}>7:00 PM</option>
                            <option value="20:00" {{ old('requested_time') === '20:00' ? 'selected' : '' }}>8:00 PM</option>
                        </select>
                        @error('requested_time')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hidden scheduled_date field (combination of date and time) -->
                <input type="hidden" name="scheduled_date" id="scheduled_date">

                <!-- Number of Guests -->
                <div>
                    <label for="guests_count" class="block text-green-200 text-sm font-medium mb-2">
                        Number of Guests <span class="text-red-400">*</span>
                        @if($service->capacity)
                        <span class="text-green-400 text-sm">(Maximum: {{ $service->capacity }})</span>
                        @endif
                    </label>
                    <input type="number" 
                           id="guests_count" 
                           name="guests_count" 
                           value="{{ old('guests_count', 1) }}"
                           min="1"
                           @if($service->capacity) max="{{ $service->capacity }}" @endif
                           required
                           class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    @error('guests_count')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Special Requests -->
                <div>
                    <label for="special_requests" class="block text-green-200 text-sm font-medium mb-2">
                        Special Requests or Notes
                    </label>
                    <textarea id="special_requests" 
                              name="special_requests" 
                              rows="4"
                              placeholder="Any special requirements, allergies, or preferences..."
                              class="w-full px-4 py-3 bg-green-800/50 border border-green-600/50 rounded-lg text-green-100 placeholder-green-400 focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ old('special_requests') }}</textarea>
                    @error('special_requests')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Booking Information -->
                <div class="bg-green-800/20 border border-green-600/30 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-400 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-green-200 font-medium mb-2">Booking Information</h4>
                            <ul class="text-green-300 text-sm space-y-1">
                                <li>• This is a booking request. Confirmation will be sent via email/SMS</li>
                                <li>• Our staff will contact you within 24 hours to confirm availability</li>
                                <li>• Payment can be made upon arrival or as directed by our staff</li>
                                <li>• Cancellations must be made at least 24 hours in advance</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('guest.services.show', $service) }}" 
                       class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Booking Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
// Combine date and time into scheduled_date when form is submitted
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const dateInput = document.getElementById('requested_date');
    const timeInput = document.getElementById('requested_time');
    const scheduledDateInput = document.getElementById('scheduled_date');

    // Function to update scheduled_date
    function updateScheduledDate() {
        const date = dateInput.value;
        const time = timeInput.value;
        
        if (date && time) {
            const scheduledDateTime = `${date} ${time}:00`;
            scheduledDateInput.value = scheduledDateTime;
            console.log('Updated scheduled_date:', scheduledDateTime);
        }
    }

    // Update scheduled_date when date or time changes
    dateInput.addEventListener('change', updateScheduledDate);
    timeInput.addEventListener('change', updateScheduledDate);

    // Update scheduled_date before form submission
    form.addEventListener('submit', function(e) {
        updateScheduledDate();
        
        // Validate that scheduled_date is set
        if (!scheduledDateInput.value) {
            e.preventDefault();
            alert('Please select both date and time for your service.');
            return false;
        }

        // Validate that the scheduled date is in the future
        const scheduledDate = new Date(scheduledDateInput.value);
        const now = new Date();
        
        if (scheduledDate <= now) {
            e.preventDefault();
            alert('Please select a future date and time for your service.');
            return false;
        }

        console.log('Form submission with data:', {
            service_id: document.querySelector('[name="service_id"]').value,
            service_type: document.querySelector('[name="service_type"]').value,
            description: document.querySelector('[name="description"]').value,
            scheduled_date: scheduledDateInput.value,
            guests_count: document.querySelector('[name="guests_count"]').value,
            special_requests: document.querySelector('[name="special_requests"]').value
        });
    });

    // Initialize scheduled_date if both date and time are already selected
    updateScheduledDate();
});

// Auto-update description to include guest count when it changes
document.getElementById('guests_count').addEventListener('change', function() {
    const guestCount = this.value;
    const serviceName = document.querySelector('[name="service_type"]').value;
    const descriptionField = document.querySelector('[name="description"]');
    
    if (guestCount && serviceName) {
        descriptionField.value = `Guest booking for ${serviceName} - ${guestCount} guest${guestCount > 1 ? 's' : ''}`;
    }
});
</script>
@endsection