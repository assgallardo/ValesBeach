@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Cottage Header -->
    <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg mb-6">
        @if($cottage->primary_image_url)
            <img src="{{ $cottage->primary_image_url }}" alt="{{ $cottage->name }}" class="w-full h-64 md:h-96 object-cover">
        @else
            <div class="w-full h-64 md:h-96 bg-gradient-to-br from-green-800 to-green-600 flex items-center justify-center">
                <svg class="w-32 h-32 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        @endif
        
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold text-white">{{ $cottage->name }}</h1>
                @if($cottage->is_featured)
                    <span class="bg-yellow-500 text-yellow-900 px-3 py-1 rounded-full font-semibold">
                        ⭐ Featured
                    </span>
                @endif
            </div>
            <p class="text-gray-300 text-lg">{{ $cottage->description }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cottage Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Pricing Information -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-4">Pricing</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">Day Rate</p>
                        <p class="text-2xl font-bold text-green-400">{{ $cottage->formatted_price_per_day }}</p>
                        <p class="text-gray-500 text-xs">6:00 AM - 6:00 PM</p>
                    </div>
                    @if($cottage->price_per_hour)
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">Hourly Rate</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $cottage->formatted_price_per_hour }}</p>
                        <p class="text-gray-500 text-xs">Min {{ $cottage->min_hours }} hrs, Max {{ $cottage->max_hours }} hrs</p>
                    </div>
                    @endif
                    @if($cottage->weekend_rate && $cottage->weekend_rate > $cottage->price_per_day)
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">Weekend Rate</p>
                        <p class="text-2xl font-bold text-purple-400">₱{{ number_format($cottage->weekend_rate, 2) }}</p>
                        <p class="text-gray-500 text-xs">Friday - Sunday</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Capacity & Features -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-4">Details</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <svg class="w-8 h-8 mx-auto text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-white font-bold">{{ $cottage->capacity }}</p>
                            <p class="text-gray-400 text-sm">Guests</p>
                        </div>
                    </div>
                    @if($cottage->bedrooms)
                    <div class="text-center">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <svg class="w-8 h-8 mx-auto text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <p class="text-white font-bold">{{ $cottage->bedrooms }}</p>
                            <p class="text-gray-400 text-sm">Bedrooms</p>
                        </div>
                    </div>
                    @endif
                    @if($cottage->bathrooms)
                    <div class="text-center">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <svg class="w-8 h-8 mx-auto text-cyan-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-white font-bold">{{ $cottage->bathrooms }}</p>
                            <p class="text-gray-400 text-sm">Bathrooms</p>
                        </div>
                    </div>
                    @endif
                    @if($cottage->size_sqm)
                    <div class="text-center">
                        <div class="bg-gray-700 rounded-lg p-4">
                            <svg class="w-8 h-8 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <p class="text-white font-bold">{{ $cottage->size_sqm }}</p>
                            <p class="text-gray-400 text-sm">Sqm</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($cottage->amenities && count($cottage->amenities) > 0)
                <div class="mb-4">
                    <h3 class="text-white font-semibold mb-3">Amenities</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($cottage->amenities as $amenity)
                        <div class="flex items-center text-gray-300">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($cottage->features && count($cottage->features) > 0)
                <div>
                    <h3 class="text-white font-semibold mb-3">Features</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($cottage->features as $feature)
                        <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">
                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Location & Rules -->
            <div class="bg-gray-800 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-4">Additional Information</h2>
                @if($cottage->location)
                <div class="mb-4">
                    <h3 class="text-white font-semibold mb-2">Location</h3>
                    <p class="text-gray-300">{{ $cottage->location }}</p>
                </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Day Use</p>
                        <p class="text-white">{{ $cottage->allow_day_use ? '✓ Allowed' : '✗ Not Available' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Overnight Stay</p>
                        <p class="text-white">{{ $cottage->allow_overnight ? '✓ Allowed' : '✗ Not Available' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Pets</p>
                        <p class="text-white">{{ $cottage->allow_pets ? '✓ Allowed' : '✗ Not Allowed' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Events</p>
                        <p class="text-white">{{ $cottage->allow_events ? '✓ Allowed' : '✗ Not Allowed' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Card -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800 rounded-lg p-6 sticky top-4">
                <h2 class="text-2xl font-bold text-white mb-4">Book This Cottage</h2>
                
                <div class="bg-green-500/20 border border-green-500 rounded-lg p-4 mb-4">
                    <p class="text-green-300 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Available
                    </p>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Starting from</span>
                        <span class="text-2xl font-bold text-green-400">{{ $cottage->formatted_price_per_day }}</span>
                    </div>
                    @if($cottage->security_deposit)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Security Deposit</span>
                        <span class="text-gray-300">₱{{ number_format($cottage->security_deposit, 2) }}</span>
                    </div>
                    @endif
                </div>

                <a href="{{ route('guest.cottages.book', $cottage) }}" 
                   class="block w-full text-center bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200 font-semibold">
                    Book Now
                </a>

                <a href="{{ route('guest.cottages.index') }}" 
                   class="block w-full text-center bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors duration-200 mt-3">
                    ← Back to Cottages
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
