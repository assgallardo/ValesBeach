@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Service Details</h1>
            <p class="text-gray-400 mt-1">View complete service information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('manager.services.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Services
            </a>
            <a href="{{ route('manager.services.edit', $service) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Service
            </a>
        </div>
    </div>

    <!-- Service Card -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <!-- Service Image -->
        <div class="h-80 bg-gray-700 relative">
            @if($service->image)
            <img src="{{ asset('storage/' . $service->image) }}" 
                 alt="{{ $service->name }}" 
                 class="w-full h-full object-cover">
            @else
            <div class="flex items-center justify-center h-full">
                @if($service->category === 'spa')
                <svg class="w-32 h-32 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @elseif($service->category === 'dining')
                <svg class="w-32 h-32 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                @elseif($service->category === 'transportation')
                <svg class="w-32 h-32 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                </svg>
                @elseif($service->category === 'activities')
                <svg class="w-32 h-32 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @else
                <svg class="w-32 h-32 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                @endif
            </div>
            @endif
            
            <!-- Status Badge -->
            <div class="absolute top-4 left-4">
                <span class="px-4 py-2 rounded-lg text-sm font-semibold shadow-lg {{ $service->is_available ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                    {{ $service->is_available ? '✓ Available' : '✗ Unavailable' }}
                </span>
            </div>

            <!-- Category Badge -->
            <div class="absolute top-4 right-4">
                <span class="px-4 py-2 bg-blue-900 text-blue-200 rounded-lg text-sm font-semibold shadow-lg">
                    {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                </span>
            </div>

            <!-- Price Badge -->
            <div class="absolute bottom-4 right-4">
                <span class="px-6 py-3 bg-gray-900/90 text-white rounded-lg text-2xl font-bold shadow-lg">
                    ₱{{ number_format($service->price, 2) }}
                </span>
            </div>
        </div>

        <!-- Service Information -->
        <div class="p-8">
            <!-- Service Name -->
            <div class="mb-6">
                <h2 class="text-4xl font-bold text-white mb-2">{{ $service->name }}</h2>
                <div class="flex items-center space-x-4 text-sm text-gray-400">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Created {{ $service->created_at->format('M d, Y') }}
                    </span>
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Updated {{ $service->updated_at->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-gray-700 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-white mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Description
                </h3>
                <p class="text-gray-300 leading-relaxed">{{ $service->description }}</p>
            </div>

            <!-- Service Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-gray-700 rounded-lg p-5">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <h4 class="text-gray-400 text-sm font-medium">Category</h4>
                    </div>
                    <p class="text-white text-lg font-semibold">{{ ucfirst(str_replace('_', ' ', $service->category)) }}</p>
                </div>

                @if($service->duration)
                <div class="bg-gray-700 rounded-lg p-5">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-gray-400 text-sm font-medium">Duration</h4>
                    </div>
                    <p class="text-white text-lg font-semibold">
                        @if($service->duration >= 60)
                            {{ floor($service->duration / 60) }}h {{ $service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '' }}
                        @else
                            {{ $service->duration }} minutes
                        @endif
                    </p>
                </div>
                @endif

                @if($service->capacity)
                <div class="bg-gray-700 rounded-lg p-5">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h4 class="text-gray-400 text-sm font-medium">Capacity</h4>
                    </div>
                    <p class="text-white text-lg font-semibold">{{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</p>
                </div>
                @endif

                <div class="bg-gray-700 rounded-lg p-5">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="text-gray-400 text-sm font-medium">Price</h4>
                    </div>
                    <p class="text-white text-lg font-semibold">₱{{ number_format($service->price, 2) }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-700">
                <a href="{{ route('manager.services.edit', $service) }}" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Service
                </a>
                
                <form action="{{ route('manager.services.toggle-status', $service) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105 {{ $service->is_available ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        {{ $service->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                    </button>
                </form>
                
                <form action="{{ route('manager.services.destroy', $service) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Service
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
