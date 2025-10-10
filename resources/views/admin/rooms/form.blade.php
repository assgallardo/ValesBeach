<x-admin-layout>
    <x-slot name="header">
        {{ isset($room) ? 'Edit Room' : 'Add New Room' }}
    </x-slot>

    <div class="container mx-auto px-4 lg:px-16 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-8">
                <form action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-6">
                    @csrf
                    @if(isset($room))
                        @method('PUT')
                    @endif

                    <!-- Room Number -->
                    <div>
                        <label for="number" class="block text-sm font-medium text-white mb-2">Room Number</label>
                        <input type="text" 
                               id="number" 
                               name="number" 
                               value="{{ old('number', $room->number ?? '') }}"
                               class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               required>
                        @error('number')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-white mb-2">Room Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $room->name ?? '') }}"
                               class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-white mb-2">Room Type</label>
                        <select id="type" 
                                name="type" 
                                class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                required>
                            <option value="">Select Type</option>
                            <option value="standard" {{ old('type', $room->type ?? '') === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="deluxe" {{ old('type', $room->type ?? '') === 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                            <option value="suite" {{ old('type', $room->type ?? '') === 'suite' ? 'selected' : '' }}>Suite</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-white mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                  required>{{ old('description', $room->description ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price and Capacity -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-white mb-2">Price per Night (₱)</label>
                            <input type="number" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $room->price ?? '') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   required>
                            @error('price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="capacity" class="block text-sm font-medium text-white mb-2">Capacity (Persons)</label>
                            <input type="number" 
                                   id="capacity" 
                                   name="capacity" 
                                   value="{{ old('capacity', $room->capacity ?? '') }}"
                                   min="1"
                                   class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                   required>
                            @error('capacity')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Amenities</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @php
                                $amenities = [
                                    'wifi' => 'Wi-Fi',
                                    'tv' => 'TV',
                                    'air_conditioning' => 'Air Conditioning',
                                    'refrigerator' => 'Refrigerator',
                                    'minibar' => 'Minibar',
                                    'safe' => 'Safe',
                                    'balcony' => 'Balcony',
                                    'ocean_view' => 'Ocean View',
                                    'bathtub' => 'Bathtub'
                                ];
                                $currentAmenities = old('amenities', $room->amenities ?? []);
                            @endphp

                            @foreach($amenities as $value => $label)
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $value }}"
                                       {{ in_array($value, $currentAmenities) ? 'checked' : '' }}
                                       class="rounded border-green-700 text-green-600 focus:ring-green-500 bg-gray-900">
                                <span class="ml-2 text-white">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('amenities')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Room Images -->
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Room Images</label>
                        <input type="file" 
                               name="images[]" 
                               accept="image/*" 
                               multiple
                               class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        <p class="mt-1 text-sm text-green-300">You can select multiple images. Supported formats: JPEG, PNG, GIF</p>
                        @error('images')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_available" 
                               name="is_available" 
                               value="1"
                               {{ old('is_available', $room->is_available ?? true) ? 'checked' : '' }}
                               class="rounded border-green-700 text-green-600 focus:ring-green-500 bg-gray-900">
                        <label for="is_available" class="ml-2 text-white">Make this room available for booking</label>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.rooms') }}" 
                           class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-all duration-300">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-600 transition-all duration-300">
                            {{ isset($room) ? 'Update Room' : 'Create Room' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
