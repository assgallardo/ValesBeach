@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header with Back Button -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-white">Facility Details</h2>
            <a href="{{ route('admin.rooms.index') }}" 
               class="text-gray-300 hover:text-white">
                Back to Facilities
            </a>
        </div>

        <!-- Facility Details Card -->
        <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
            <!-- Basic Info Section -->
            <div class="p-6 border-b border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-2">{{ $room->name }}</h3>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            {{ $room->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $room->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-white">₱{{ number_format($room->price, 2) }}</p>
                        <p class="text-sm text-gray-400">per night</p>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Category</h4>
                        <p class="text-gray-300">{{ $room->category ?? 'Rooms' }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Type</h4>
                        <p class="text-gray-300">{{ $room->type }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Capacity</h4>
                        <p class="text-gray-300">{{ $room->capacity }} guests</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Beds</h4>
                        <p class="text-gray-300">{{ $room->beds }} {{ Str::plural('bed', $room->beds) }}</p>
                    </div>

                    @if(!empty($room->check_in_time) || !empty($room->check_out_time))
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Check-in / Check-out Time</h4>
                        <p class="text-gray-300">
                            @if(!empty($room->check_in_time))
                                Check-in: {{ \Carbon\Carbon::createFromFormat('H:i:s', $room->check_in_time)->format('g:i A') }}<br>
                            @endif
                            @if(!empty($room->check_out_time))
                                Check-out: {{ \Carbon\Carbon::createFromFormat('H:i:s', $room->check_out_time)->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-2">Amenities</h4>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach((is_array($room->amenities) ? $room->amenities : json_decode($room->amenities, true)) ?? [] as $amenity)
                                <span class="text-gray-300">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            <div class="p-6 border-t border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-2">Description</h4>
                <p class="text-gray-300">{{ $room->description }}</p>
            </div>

            <!-- Image Gallery Section -->
            <div class="p-6 border-t border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-2">Image Gallery</h4>
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($room->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="Room image" 
                                     class="w-full h-48 object-cover rounded-lg">
                                @if($image->is_featured)
                                    <span class="absolute top-2 left-2 px-2 py-1 bg-green-500 text-white text-xs rounded">
                                        Featured
                                    </span>
                                @endif
                                <button onclick="deleteImage({{ $room->id }}, {{ $image->id }})"
                                        class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-400 col-span-3">No images uploaded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 bg-gray-700 flex justify-end space-x-4">
                <a href="{{ route('admin.rooms.edit', $room) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Edit Facility
                </a>
                <form action="{{ route('admin.rooms.destroy', $room) }}" 
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this facility?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Delete Facility
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteImage(roomId, imageId) {
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`/admin/rooms/${roomId}/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection