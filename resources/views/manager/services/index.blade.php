@extends('layouts.manager')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Services Management</h1>
            <p class="text-gray-400 mt-1">Manage all resort services and offerings</p>
        </div>
        <a href="{{ route('manager.services.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Service
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg mb-6 shadow-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500 text-white px-6 py-4 rounded-lg mb-6 shadow-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('manager.services.index') }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ !request('category') ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                All Services
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'spa']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('category') === 'spa' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                Spa & Wellness
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'dining']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('category') === 'dining' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                Dining
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'room_service']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('category') === 'room_service' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                Room Service
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'transportation']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('category') === 'transportation' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                Transportation
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'activities']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('category') === 'activities' ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white' }}">
                Activities
            </a>
        </div>
    </div>

    <!-- Services Grid -->
    @if($services->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($services as $service)
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
            <!-- Service Image -->
            <div class="h-48 bg-gray-700 relative">
                @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" 
                     alt="{{ $service->name }}" 
                     class="w-full h-full object-cover">
                @else
                <div class="flex items-center justify-center h-full">
                    @if($service->category === 'spa')
                    <i class="fas fa-spa text-purple-400 text-6xl"></i>
                    @elseif($service->category === 'dining')
                    <i class="fas fa-utensils text-green-400 text-6xl"></i>
                    @elseif($service->category === 'room_service')
                    <i class="fas fa-concierge-bell text-red-400 text-6xl"></i>
                    @elseif($service->category === 'transportation')
                    <i class="fas fa-car text-blue-400 text-6xl"></i>
                    @elseif($service->category === 'activities')
                    <i class="fas fa-volleyball-ball text-yellow-400 text-6xl"></i>
                    @else
                    <svg class="w-16 h-16 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    @endif
                </div>
                @endif
                
                <!-- Category Badge -->
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 bg-blue-900 text-blue-200 rounded-full text-xs font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                    </span>
                </div>

                <!-- Price Badge -->
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 bg-gray-900/80 text-white rounded-full text-sm font-bold">
                        ₱{{ number_format($service->price, 0) }}
                    </span>
                </div>

                <!-- Status Badge -->
                <div class="absolute bottom-3 left-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $service->is_available ? 'bg-green-900 text-green-200' : 'bg-red-900 text-red-200' }}">
                        {{ $service->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                </div>
            </div>

            <!-- Service Details -->
            <div class="p-6">
                <h3 class="text-lg font-bold text-white mb-2">{{ $service->name }}</h3>
                
                <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ $service->description }}</p>
                
                <div class="space-y-2 mb-4">
                    @if($service->duration)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Duration:</span>
                        <span class="text-white">
                            @if($service->duration >= 60)
                                {{ floor($service->duration / 60) }}h {{ $service->duration % 60 > 0 ? ($service->duration % 60) . 'm' : '' }}
                            @else
                                {{ $service->duration }}m
                            @endif
                        </span>
                    </div>
                    @endif
                    @if($service->capacity)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Capacity:</span>
                        <span class="text-white">{{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Created:</span>
                        <span class="text-white">{{ $service->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-2">
                    <!-- Primary Actions Row -->
                    <div class="flex gap-2">
                        <a href="{{ route('manager.services.show', $service) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        <a href="{{ route('manager.services.edit', $service) }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    </div>
                    
                    <!-- Secondary Actions Row -->
                    <div class="flex gap-2">
                        <!-- Toggle Status -->
                        <form action="{{ route('manager.services.toggle-status', $service) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200 {{ $service->is_available ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                {{ $service->is_available ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        
                        <!-- Delete -->
                        <form action="{{ route('manager.services.destroy', $service) }}" 
                              method="POST" 
                              class="flex-1"
                              onsubmit="return confirm('Are you sure you want to delete this service?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($services->hasPages())
        <div class="flex justify-center">
            {{ $services->links() }}
        </div>
    @endif
    @else
    <!-- Empty State -->
    <div class="bg-gray-800 rounded-lg shadow-xl p-12 text-center">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
        </svg>
        <h3 class="text-xl font-semibold text-gray-300 mb-2">No Services Found</h3>
        <p class="text-gray-400 mb-6">
            @if(request()->has('category'))
                No services found in this category. Try a different category or create a new service.
            @else
                Start by creating your first service offering.
            @endif
        </p>
        <a href="{{ route('manager.services.create') }}" 
           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Service
        </a>
    </div>
    @endif
</div>
@endsection
