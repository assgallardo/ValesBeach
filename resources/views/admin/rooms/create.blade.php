@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-white">Add New Facility</h2>
            <a href="{{ route('admin.rooms.index') }}" 
               class="text-gray-300 hover:text-white">
                Back to Facilities
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data" class="bg-gray-800 rounded-lg shadow-xl p-6">
            @csrf
            
            <!-- Display any validation errors -->
            @if ($errors->any())
                <div class="bg-red-500/10 text-red-400 p-4 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-300 mb-2">Facility Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}"
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2"
                       required>
            </div>

            <div x-data="{ selectedCategory: '{{ old('category', 'Rooms') }}' }">
                <!-- Category (Main Type) -->
                <div class="mb-6">
                    <label for="category" class="block text-gray-300 mb-2">Type</label>
                    <select name="category" 
                            id="category" 
                            x-model="selectedCategory"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="Rooms">Rooms</option>
                        <option value="Cottages">Cottages</option>
                        <option value="Event and Dining">Event and Dining</option>
                    </select>
                </div>

                <!-- Specific Type -->
                <div class="mb-6">
                    <label for="type" class="block text-gray-300 mb-2">Specific Type/Name</label>
                    <input type="text" 
                           name="type" 
                           id="type" 
                           value="{{ old('type') }}"
                           placeholder="e.g., Umbrella Cottage, Executive Room, Function Hall"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           required>
                    <p class="text-gray-400 text-sm mt-1">Examples: Umbrella Cottage, Bahay Kubo, Standard Room, Beer Garden, Dining Hall</p>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-gray-300 mb-2">Description</label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                              required>{{ old('description') }}</textarea>
                </div>

                <!-- Price -->
                <div class="mb-6">
                    <label for="price" class="block text-gray-300 mb-2">Price per Night (₱)</label>
                    <input type="number" 
                           name="price" 
                           id="price" 
                           value="{{ old('price') }}"
                           min="0"
                           step="0.01"
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                           required>
                </div>

                <!-- Capacity & Beds -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="capacity" class="block text-gray-300 mb-2">Capacity (Guests)</label>
                        <input type="number" 
                               name="capacity" 
                               id="capacity" 
                               value="{{ old('capacity', 2) }}"
                               min="1"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>
                    <div>
                        <label for="beds" class="block text-gray-300 mb-2">Number of Beds</label>
                        <input type="number" 
                               name="beds" 
                               id="beds" 
                               value="{{ old('beds', 0) }}"
                               min="0"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>
                </div>

                <!-- Optional Check-in/Check-out Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="check_in_time" class="block text-gray-300 mb-2">Check-in Time (optional)</label>
                        <input type="time"
                               name="check_in_time"
                               id="check_in_time"
                               value="{{ old('check_in_time') }}"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="check_out_time" class="block text-gray-300 mb-2">Check-out Time (optional)</label>
                        <input type="time"
                               name="check_out_time"
                               id="check_out_time"
                               value="{{ old('check_out_time') }}"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <!-- Amenities (Dynamic based on Category) -->
                <div class="mb-6">
                <label class="block text-gray-300 mb-2">Amenities</label>
                
                <!-- Rooms Amenities -->
                <div x-show="selectedCategory === 'Rooms'" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach(['WiFi', 'TV', 'Air Conditioning', 'Mini Bar', 'Refrigerator', 'Safe', 'Balcony', 'Ocean View', 'Room Service'] as $amenity)
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity }}"
                                       class="form-checkbox text-green-500 rounded"
                                       {{ in_array($amenity, old('amenities', [])) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $amenity }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Cottages Amenities -->
                <div x-show="selectedCategory === 'Cottages'" class="grid grid-cols-2 md:grid-cols-3 gap-4" style="display: none;">
                    @foreach(['Traditional Filipino cottage', '20 pax capacity', 'Bamboo construction', 'Thatched roof', 'Natural ventilation', 'Tables and benches', 'Near beach access', 'Shaded area', 'Power outlet'] as $amenity)
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity }}"
                                       class="form-checkbox text-green-500 rounded"
                                       {{ in_array($amenity, old('amenities', [])) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $amenity }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Event and Dining Amenities -->
                <div x-show="selectedCategory === 'Event and Dining'" class="grid grid-cols-2 md:grid-cols-3 gap-4" style="display: none;">
                    @foreach(['Air Conditioning', 'Sound System', 'Stage/Platform', 'Dining Tables', 'Chairs', 'Kitchen Access', 'Parking Space', 'Restrooms', 'WiFi', 'LED Lighting', 'Audio Visual Equipment', 'Catering Service'] as $amenity)
                        <div>
                            <label class="inline-flex items-center text-gray-300">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity }}"
                                       class="form-checkbox text-green-500 rounded"
                                       {{ in_array($amenity, old('amenities', [])) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $amenity }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
                </div>

                <!-- Availability -->
                <div class="mb-6">
                    <label class="inline-flex items-center text-gray-300">
                        <input type="checkbox" 
                               name="is_available" 
                               value="1"
                               {{ old('is_available', true) ? 'checked' : '' }}
                               class="form-checkbox text-green-500 rounded">
                        <span class="ml-2">Facility is Available</span>
                    </label>
                </div>
            </div>

            <!-- Facility Images -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2">Facility Images (Max 10)</label>
                <div class="mt-2" id="imagePreview" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                <input type="file" 
                       name="images[]"
                       id="roomImages"
                       accept="image/*" 
                       multiple
                       class="mt-1 block w-full text-gray-300
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-green-600 file:text-white
                              hover:file:bg-green-700"
                       onchange="previewImages(this)">
                <p class="mt-1 text-sm text-gray-400">
                    You can select up to 10 images. First image will be set as featured.
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Add Facility
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files.length > 10) {
        alert('You can only upload up to 10 images');
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg">
                ${index === 0 ? '<span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded text-sm">Featured</span>' : ''}
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}
</script>
@endpush
@endsection