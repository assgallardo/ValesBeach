@extends('layouts.admin')

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
                Service Details
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                View and manage service information
            </p>
            <div class="mt-6 space-x-4">
                <a href="{{ route('manager.services.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Services
                </a>
                <a href="{{ route('manager.services.edit', $service) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Service
                </a>
            </div>
        </div>

        <!-- Service Details Card -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden">
            <!-- Service Image -->
            <div class="h-64 bg-green-800/50 relative">
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
                
                <!-- Status Badge -->
                <div class="absolute top-4 left-4">
                    <span class="px-3 py-2 rounded-full text-sm font-medium
                        @if($service->is_available) bg-green-500/20 text-green-400 border border-green-400/30
                        @else bg-red-500/20 text-red-400 border border-red-400/30
                        @endif">
                        {{ $service->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                </div>

                <!-- Category Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-2 bg-green-600/80 text-green-100 rounded-full text-sm font-medium">
                        {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                    </span>
                </div>
            </div>

            <!-- Service Information -->
            <div class="p-8">
                <!-- Service Name and Price -->
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-3xl font-bold text-green-50">{{ $service->name }}</h1>
                    <span class="text-3xl font-bold text-green-400">₱{{ number_format($service->price, 2) }}</span>
                </div>

                <!-- Service Description -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-green-200 mb-3">Description</h3>
                    <p class="text-green-300 leading-relaxed">{{ $service->description }}</p>
                </div>

                <!-- Service Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @if($service->duration)
                    <div class="bg-green-800/30 rounded-lg p-4">
                        <h4 class="text-green-400 text-sm font-medium mb-1">Duration</h4>
                        <p class="text-green-50 text-lg font-semibold">
                            @if($service->duration >= 60)
                                {{ floor($service->duration / 60) }}h {{ $service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '' }}
                            @else
                                {{ $service->duration }}m
                            @endif
                        </p>
                    </div>
                    @endif

                    @if($service->capacity)
                    <div class="bg-green-800/30 rounded-lg p-4">
                        <h4 class="text-green-400 text-sm font-medium mb-1">Capacity</h4>
                        <p class="text-green-50 text-lg font-semibold">{{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</p>
                    </div>
                    @endif

                    <div class="bg-green-800/30 rounded-lg p-4">
                        <h4 class="text-green-400 text-sm font-medium mb-1">Created</h4>
                        <p class="text-green-50 text-lg font-semibold">{{ $service->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="bg-green-800/30 rounded-lg p-4">
                        <h4 class="text-green-400 text-sm font-medium mb-1">Last Updated</h4>
                        <p class="text-green-50 text-lg font-semibold">{{ $service->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('manager.services.edit', $service) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Service
                    </a>
                    
                    <form action="{{ route('manager.services.toggle-status', $service) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-6 py-3 rounded-lg transition-colors
                                @if($service->is_available) bg-yellow-600 hover:bg-yellow-700 text-white
                                @else bg-green-600 hover:bg-green-700 text-white
                                @endif">
                            <i class="fas fa-toggle-{{ $service->is_available ? 'off' : 'on' }} mr-2"></i>
                            {{ $service->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('manager.services.destroy', $service) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this service?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Service
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection