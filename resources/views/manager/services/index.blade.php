@extends('layouts.admin')

@section('content')
<!-- Background decorative blur elements -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
</div>

<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Services Management
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                Manage all resort services and offerings
            </p>
            <div class="mt-6 space-x-4">
                <a href="{{ route('manager.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
                <a href="{{ route('manager.services.create') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Add New Service
                </a>
            </div>
        </div>

        <!-- Service Categories Filter - Same as Guest -->
        <div class="flex flex-wrap justify-center gap-4 mb-8">
            <a href="{{ route('manager.services.index') }}" 
               class="px-4 py-2 {{ !request('category') ? 'bg-green-600 text-white' : 'bg-green-900/50 text-green-200 hover:bg-green-600 hover:text-white' }} rounded-lg transition-colors duration-200">
                All Services
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'spa']) }}" 
               class="px-4 py-2 {{ request('category') === 'spa' ? 'bg-green-600 text-white' : 'bg-green-900/50 text-green-200 hover:bg-green-600 hover:text-white' }} rounded-lg transition-colors duration-200">
                Spa & Wellness
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'dining']) }}" 
               class="px-4 py-2 {{ request('category') === 'dining' ? 'bg-green-600 text-white' : 'bg-green-900/50 text-green-200 hover:bg-green-600 hover:text-white' }} rounded-lg transition-colors duration-200">
                Dining
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'transportation']) }}" 
               class="px-4 py-2 {{ request('category') === 'transportation' ? 'bg-green-600 text-white' : 'bg-green-900/50 text-green-200 hover:bg-green-600 hover:text-white' }} rounded-lg transition-colors duration-200">
                Transportation
            </a>
            <a href="{{ route('manager.services.index', ['category' => 'activities']) }}" 
               class="px-4 py-2 {{ request('category') === 'activities' ? 'bg-green-600 text-white' : 'bg-green-900/50 text-green-200 hover:bg-green-600 hover:text-white' }} rounded-lg transition-colors duration-200">
                Activities
            </a>
        </div>

        <!-- Services Grid - Same Layout as Guest -->
        @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($services as $service)
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg border border-green-700/30 overflow-hidden hover:border-green-500/50 transition-all duration-300">
                <!-- Service Image - Same as Guest -->
                <div class="h-48 bg-green-800/50 relative">
                    @if($service->image)
                    <img src="{{ asset('storage/' . $service->image) }}" 
                         alt="{{ $service->name }}" 
                         class="w-full h-full object-cover">
                    @else
                    <div class="flex items-center justify-center h-full">
                        @if($service->category === 'spa')
                        <i class="fas fa-spa text-4xl text-green-400"></i>
                        @elseif($service->category === 'dining')
                        <i class="fas fa-utensils text-4xl text-orange-400"></i>
                        @elseif($service->category === 'transportation')
                        <i class="fas fa-car text-4xl text-blue-400"></i>
                        @elseif($service->category === 'activities')
                        <i class="fas fa-swimmer text-4xl text-purple-400"></i>
                        @else
                        <i class="fas fa-concierge-bell text-4xl text-yellow-400"></i>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Category Badge - Same as Guest -->
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-1 bg-green-600/80 text-green-100 rounded-full text-xs font-medium">
                            {{ ucfirst(str_replace('_', ' ', $service->category)) }}
                        </span>
                    </div>

                    <!-- Price Badge - Same as Guest -->
                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-1 bg-black/60 text-white rounded-full text-sm font-bold">
                            ₱{{ number_format($service->price, 0) }}
                        </span>
                    </div>

                    <!-- Status Badge - Manager Addition -->
                    <div class="absolute bottom-3 left-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            @if($service->is_available) bg-green-500/20 text-green-400 border border-green-400/30
                            @else bg-red-500/20 text-red-400 border border-red-400/30
                            @endif">
                            {{ $service->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                </div>

                <!-- Service Details - Same as Guest -->
                <div class="p-6">
                    <h3 class="text-lg font-bold text-green-50 mb-2">{{ $service->name }}</h3>
                    
                    <p class="text-green-300 text-sm mb-4 line-clamp-3">{{ $service->description }}</p>
                    
                    <div class="space-y-2 mb-4">
                        @if($service->duration)
                        <div class="flex justify-between text-sm">
                            <span class="text-green-400">Duration:</span>
                            <span class="text-green-50">
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
                            <span class="text-green-400">Capacity:</span>
                            <span class="text-green-50">{{ $service->capacity }} {{ $service->capacity === 1 ? 'person' : 'people' }}</span>
                        </div>
                        @endif
                        <!-- Manager Additional Info -->
                        <div class="flex justify-between text-sm">
                            <span class="text-green-400">Created:</span>
                            <span class="text-green-50">{{ $service->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Manager Actions - Different from Guest -->
                    <div class="space-y-2">
                        <!-- Primary Actions Row -->
                        <div class="flex gap-2">
                            <a href="{{ route('manager.services.show', $service) }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded text-sm transition-colors">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('manager.services.edit', $service) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                        </div>
                        
                        <!-- Secondary Actions Row -->
                        <div class="flex gap-2">
                            <!-- Toggle Status -->
                            <form action="{{ route('manager.services.toggle-status', $service) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full py-2 px-3 rounded text-sm transition-colors
                                        @if($service->is_available) bg-yellow-600 hover:bg-yellow-700 text-white
                                        @else bg-green-600 hover:bg-green-700 text-white
                                        @endif">
                                    <i class="fas fa-toggle-{{ $service->is_available ? 'off' : 'on' }} mr-1"></i>
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
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded text-sm transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination - Same as Guest -->
        <div class="flex justify-center">
            {{ $services->links() }}
        </div>
        @else
        <!-- Empty State - Same as Guest but Manager-specific -->
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <h3 class="text-xl font-medium text-green-200 mb-2">No Services Found</h3>
            <p class="text-green-400 mb-6">
                @if(request()->hasAny(['category', 'search', 'status']))
                    Try adjusting your filters or search terms.
                @else
                    Start by creating your first service offering.
                @endif
            </p>
            <a href="{{ route('manager.services.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add New Service
            </a>
        </div>
        @endif
    </div>
</main>

<!-- Success/Error Messages - Floating notifications -->
@if(session('success'))
<div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center" id="success-message">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
</div>
<script>
    setTimeout(() => {
        const message = document.getElementById('success-message');
        if (message) {
            message.style.opacity = '0';
            message.style.transform = 'translateX(100%)';
            setTimeout(() => message.remove(), 300);
        }
    }, 5000);
</script>
@endif

@if(session('error'))
<div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center" id="error-message">
    <i class="fas fa-exclamation-circle mr-2"></i>
    {{ session('error') }}
</div>
<script>
    setTimeout(() => {
        const message = document.getElementById('error-message');
        if (message) {
            message.style.opacity = '0';
            message.style.transform = 'translateX(100%)';
            setTimeout(() => message.remove(), 300);
        }
    }, 5000);
</script>
@endif
@endsection