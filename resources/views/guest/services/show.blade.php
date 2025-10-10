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
                {{ $service->name }}
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                {{ ucfirst(str_replace('_', ' ', $service->category)) }} Service
            </p>
            <div class="mt-6 space-x-4">
                <a href="{{ route('guest.services.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Services
                </a>
                @if($service->is_available)
                <a href="{{ route('guest.services.request', $service) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-calendar-plus mr-2"></i>Book This Service
                </a>
                @endif
            </div>
        </div>

        <!-- Service Details Card -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden mb-8">
            <!-- Service Image -->
            <div class="h-64 md:h-80 bg-green-800/50 relative">
                @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" 
                     alt="{{ $service->name }}" 
                     class="w-full h-full object-cover">
                @else
                <div class="flex items-center justify-center h-full">
                    @if($service->category === 'spa')
                    <i class="fas fa-spa text-8xl text-green-400"></i>
                    @elseif($service->category === 'dining')
                    <i class="fas fa-utensils text-8xl text-orange-400"></i>
                    @elseif($service->category === 'transportation')
                    <i class="fas fa-car text-8xl text-blue-400"></i>
                    @elseif($service->category === 'activities')
                    <i class="fas fa-swimmer text-8xl text-purple-400"></i>
                    @else
                    <i class="fas fa-concierge-bell text-8xl text-yellow-400"></i>
                    @endif
                </div>
                @endif
                
                <!-- Availability Badge -->
                <div class="absolute top-4 left-4">
                    @if($service->is_available)
                    <span class="px-3 py-2 bg-green-500/20 text-green-400 border border-green-400/30 rounded-full text-sm font-medium">
                        Available for Booking
                    </span>
                    @else
                    <span class="px-3 py-2 bg-red-500/20 text-red-400 border border-red-400/30 rounded-full text-sm font-medium">
                        Currently Unavailable
                    </span>
                    @endif
                </div>

                <!-- Category Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-2 bg-green-600/80 text-green-100 rounded-full text-sm font-medium">
                        {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                    </span>
                </div>

                <!-- Price Badge -->
                <div class="absolute bottom-4 right-4">
                    <span class="px-4 py-2 bg-black/70 text-white rounded-full text-2xl font-bold">
                        ₱{{ number_format($service->price, 0) }}
                    </span>
                </div>
            </div>

            <!-- Service Information -->
            <div class="p-8">
                <!-- Service Description -->
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-green-200 mb-4">About This Service</h3>
                    <p class="text-green-300 leading-relaxed text-lg">{{ $service->description }}</p>
                </div>

                <!-- Service Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    @if($service->duration)
                    <div class="bg-green-800/30 rounded-lg p-6 text-center">
                        <i class="fas fa-clock text-green-400 text-3xl mb-3"></i>
                        <h4 class="text-green-400 text-sm font-medium mb-2">Duration</h4>
                        <p class="text-green-50 text-xl font-semibold">
                            @if($service->duration >= 60)
                                {{ floor($service->duration / 60) }}h {{ $service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '' }}
                            @else
                                {{ $service->duration }} minutes
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($service->capacity)
                    <div class="bg-green-800/30 rounded-lg p-6 text-center">
                        <i class="fas fa-users text-green-400 text-3xl mb-3"></i>
                        <h4 class="text-green-400 text-sm font-medium mb-2">Maximum Capacity</h4>
                        <p class="text-green-50 text-xl font-semibold">{{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</p>
                    </div>
                    @endif

                    <div class="bg-green-800/30 rounded-lg p-6 text-center">
                        <i class="fas fa-peso-sign text-green-400 text-3xl mb-3"></i>
                        <h4 class="text-green-400 text-sm font-medium mb-2">Price</h4>
                        <p class="text-green-50 text-xl font-semibold">₱{{ number_format($service->price, 2) }}</p>
                    </div>
                </div>

                <!-- Booking Information -->
                @if($service->is_available)
                <div class="bg-green-800/20 border border-green-600/30 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-green-400 text-xl mt-1 mr-4"></i>
                        <div>
                            <h4 class="text-green-200 font-semibold mb-2">Booking Information</h4>
                            <ul class="text-green-300 space-y-1">
                                <li>• Book in advance to secure your preferred time slot</li>
                                <li>• Cancellations must be made at least 24 hours in advance</li>
                                @if($service->duration)
                                <li>• Service duration: {{ $service->duration >= 60 ? floor($service->duration / 60) . 'h ' . ($service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '') : $service->duration . ' minutes' }}</li>
                                @endif
                                @if($service->capacity)
                                <li>• Maximum capacity: {{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center">
                    @if($service->is_available)
                    <a href="{{ route('guest.services.request', $service) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-lg transition-colors text-lg font-semibold">
                        <i class="fas fa-calendar-plus mr-2"></i>Book This Service
                    </a>
                    @else
                    <div class="bg-gray-600 text-gray-300 px-8 py-4 rounded-lg text-lg font-semibold">
                        <i class="fas fa-times-circle mr-2"></i>Currently Unavailable
                    </div>
                    @endif
                    
                    <a href="{{ route('guest.services.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg transition-colors text-lg font-semibold">
                        <i class="fas fa-list mr-2"></i>Browse All Services
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        @if($relatedServices && $relatedServices->count() > 0)
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-green-50 mb-6 text-center">Other {{ ucfirst($service->category) }} Services</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedServices as $relatedService)
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden hover:border-green-500/50 transition-all duration-300">
                    <div class="h-32 bg-green-800/50 relative">
                        @if($relatedService->image)
                        <img src="{{ asset('storage/' . $relatedService->image) }}" 
                             alt="{{ $relatedService->name }}" 
                             class="w-full h-full object-cover">
                        @else
                        <div class="flex items-center justify-center h-full">
                            @if($relatedService->category === 'spa')
                            <i class="fas fa-spa text-2xl text-green-400"></i>
                            @elseif($relatedService->category === 'dining')
                            <i class="fas fa-utensils text-2xl text-orange-400"></i>
                            @elseif($relatedService->category === 'transportation')
                            <i class="fas fa-car text-2xl text-blue-400"></i>
                            @elseif($relatedService->category === 'activities')
                            <i class="fas fa-swimmer text-2xl text-purple-400"></i>
                            @else
                            <i class="fas fa-concierge-bell text-2xl text-yellow-400"></i>
                            @endif
                        </div>
                        @endif
                        
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 bg-black/60 text-white rounded-full text-sm font-bold">
                                ₱{{ number_format($relatedService->price, 0) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h4 class="text-green-50 font-semibold mb-2">{{ $relatedService->name }}</h4>
                        <p class="text-green-300 text-sm mb-3 line-clamp-2">{{ $relatedService->description }}</p>
                        <a href="{{ route('guest.services.show', $relatedService) }}" 
                           class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</main>
@endsection