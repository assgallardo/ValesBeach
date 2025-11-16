@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Add New Menu Item</h1>
            <p class="text-gray-400 mt-1">Create a new food or beverage item for the menu</p>
        </div>
        <a href="{{ route('staff.menu.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Menu
        </a>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold">Please correct the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-7 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <form action="{{ route('staff.menu.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Information (Left Column - 2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Item Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                            Item Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Enter menu item name"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                  placeholder="Describe the menu item..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category and Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="menu_category_id" class="block text-sm font-medium text-gray-300 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="menu_category_id" 
                                    name="menu_category_id"
                                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('menu_category_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('menu_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('menu_category_id')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-300 mb-2">
                                Price (₱) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}"
                                   class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('price') border-red-500 @enderror"
                                   placeholder="0.00"
                                   required>
                            @error('price')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Ingredients -->
                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-300 mb-2">
                            Ingredients
                        </label>
                        <input type="text" 
                               id="ingredients" 
                               name="ingredients" 
                               value="{{ old('ingredients') }}"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="e.g., Chicken, Rice, Vegetables (comma separated)">
                        <p class="mt-1 text-xs text-gray-400">Separate multiple ingredients with commas</p>
                    </div>

                    <!-- Allergens -->
                    <div>
                        <label for="allergens" class="block text-sm font-medium text-gray-300 mb-2">
                            Allergens
                        </label>
                        <input type="text" 
                               id="allergens" 
                               name="allergens" 
                               value="{{ old('allergens') }}"
                               class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="e.g., Peanuts, Dairy, Gluten (comma separated)">
                        <p class="mt-1 text-xs text-gray-400">List any allergens present in this item</p>
                    </div>

                    <!-- Preparation Time and Calories -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="preparation_time" class="block text-sm font-medium text-gray-300 mb-2">
                                Preparation Time (minutes)
                            </label>
                            <input type="number" 
                                   id="preparation_time" 
                                   name="preparation_time" 
                                   value="{{ old('preparation_time', 15) }}"
                                   min="0"
                                   class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="15"
                                   oninput="validatePreparationTime(this)">
                            <p id="preparation_time_error" class="mt-1 text-sm text-yellow-400 hidden">Must be at least 0</p>
                        </div>

                        <div>
                            <label for="calories" class="block text-sm font-medium text-gray-300 mb-2">
                                Calories
                            </label>
                            <input type="number" 
                                   id="calories" 
                                   name="calories" 
                                   value="{{ old('calories') }}"
                                   class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="e.g., 450">
                        </div>
                    </div>
                </div>

                <!-- Sidebar (Right Column - 1/3 width) -->
                <div class="space-y-6">
                    <!-- Item Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Item Image
                        </label>
                        <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center hover:border-green-500 transition-colors duration-200">
                            <div id="imagePreview" class="hidden mb-4">
                                <img id="previewImg" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                            </div>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewImage(this)">
                            <label for="image" class="cursor-pointer">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-400">Click to upload image</p>
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </label>
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dietary Options -->
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-white mb-3">Dietary Options</h3>
                        <div class="space-y-2">
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_vegetarian" 
                                       name="is_vegetarian" 
                                       value="1"
                                       {{ old('is_vegetarian') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">🌱 Vegetarian</span>
                            </label>
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_vegan" 
                                       name="is_vegan" 
                                       value="1"
                                       {{ old('is_vegan') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">🥗 Vegan</span>
                            </label>
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_gluten_free" 
                                       name="is_gluten_free" 
                                       value="1"
                                       {{ old('is_gluten_free') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">🌾 Gluten Free</span>
                            </label>
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_dairy_free" 
                                       name="is_dairy_free" 
                                       value="1"
                                       {{ old('is_dairy_free') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">🥛 Dairy Free</span>
                            </label>
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_spicy" 
                                       name="is_spicy" 
                                       value="1"
                                       {{ old('is_spicy') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">🌶️ Spicy</span>
                            </label>
                        </div>
                    </div>

                    <!-- Status Options -->
                    <div class="bg-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-white mb-3">Status</h3>
                        <div class="space-y-2">
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_available" 
                                       name="is_available" 
                                       value="1"
                                       {{ old('is_available', true) ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">✅ Available for Order</span>
                            </label>
                            <label class="flex items-center text-gray-300 cursor-pointer hover:text-white transition-colors">
                                <input type="checkbox" 
                                       id="is_featured" 
                                       name="is_featured" 
                                       value="1"
                                       {{ old('is_featured') ? 'checked' : '' }}
                                       class="form-checkbox h-4 w-4 text-green-500 rounded focus:ring-2 focus:ring-green-500">
                                <span class="ml-2 text-sm">⭐ Featured Item</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-700">
                <a href="{{ route('staff.menu.index') }}" 
                   class="px-6 py-2.5 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Menu Item
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function validatePreparationTime(input) {
    const errorMsg = document.getElementById('preparation_time_error');
    if (input.value < 0) {
        input.value = 0;
        errorMsg.classList.remove('hidden');
        setTimeout(() => {
            errorMsg.classList.add('hidden');
        }, 3000);
    } else {
        errorMsg.classList.add('hidden');
    }
}
</script>
@endsection
