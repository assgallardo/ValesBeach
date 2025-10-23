@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            Available Cottages (Bahay Kubo)
        </h2>
        <p class="text-xl text-gray-200">
            Book your perfect Bahay Kubo at Vales Beach Resort
        </p>
    </div>

    @if(isset($error))
        <div class="bg-red-500/20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
            {{ $error }}
        </div>
    @endif

    @if($cottages->isEmpty())
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <p class="text-gray-300 text-lg">No cottages available at the moment.</p>
            <a href="{{ route('guest.rooms.browse') }}" class="mt-4 inline-block bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700">
                Browse Rooms Instead
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cottages as $cottage)
            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-2xl transition-shadow duration-300">
                @if($cottage->primary_image_url)
                    <img src="{{ $cottage->primary_image_url }}" alt="{{ $cottage->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-green-800 to-green-600 flex items-center justify-center">
                        <svg class="w-20 h-20 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-xl font-bold text-white">{{ $cottage->name }}</h3>
                        @if($cottage->is_featured)
                            <span class="bg-yellow-500 text-yellow-900 text-xs px-2 py-1 rounded-full font-semibold">
                                Featured
                            </span>
                        @endif
                    </div>
                    
                    <p class="text-gray-300 mb-4 line-clamp-2">{{ $cottage->description }}</p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-300">
                            <span>Day Rate:</span>
                            <span class="font-bold text-green-400">{{ $cottage->formatted_price_per_day }}</span>
                        </div>
                        @if($cottage->price_per_hour)
                        <div class="flex justify-between text-gray-300">
                            <span>Hourly Rate:</span>
                            <span class="font-bold text-blue-400">{{ $cottage->formatted_price_per_hour }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-gray-300">
                            <span>Capacity:</span>
                            <span>{{ $cottage->capacity }} persons</span>
                        </div>
                        @if($cottage->bedrooms)
                        <div class="flex justify-between text-gray-300">
                            <span>Bedrooms:</span>
                            <span>{{ $cottage->bedrooms }}</span>
                        </div>
                        @endif
                    </div>

                    @if($cottage->features && count($cottage->features) > 0)
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 mb-2">Features:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($cottage->features, 0, 3) as $feature)
                            <span class="bg-gray-700 text-gray-300 text-xs px-2 py-1 rounded">
                                {{ ucfirst(str_replace('_', ' ', $feature)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('guest.cottages.show', $cottage) }}" 
                           class="flex-1 text-center bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            View Details
                        </a>
                        <a href="{{ route('guest.cottages.book', $cottage) }}" 
                           class="flex-1 text-center bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <div class="mt-8 text-center">
        <a href="{{ route('guest.dashboard') }}" class="text-blue-400 hover:text-blue-300 transition-colors">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
