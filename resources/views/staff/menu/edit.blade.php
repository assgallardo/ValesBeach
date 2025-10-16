@extends('layouts.staff')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Edit Menu Item</h2>
                <a href="{{ route('staff.menu.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Menu
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('staff.menu.update', $menuItem) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $menuItem->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description', $menuItem->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="menu_category_id" class="form-label">Category *</label>
                                <select class="form-select @error('menu_category_id') is-invalid @enderror" 
                                        id="menu_category_id" name="menu_category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('menu_category_id', $menuItem->menu_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('menu_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (₱) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $menuItem->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ingredients" class="form-label">Ingredients (comma separated)</label>
                            <input type="text" class="form-control" id="ingredients" name="ingredients" 
                                   value="{{ old('ingredients', is_array($menuItem->ingredients) ? implode(', ', $menuItem->ingredients) : '') }}" 
                                   placeholder="e.g., Chicken, Rice, Vegetables">
                        </div>

                        <div class="mb-3">
                            <label for="allergens" class="form-label">Allergens (comma separated)</label>
                            <input type="text" class="form-control" id="allergens" name="allergens" 
                                   value="{{ old('allergens', is_array($menuItem->allergens) ? implode(', ', $menuItem->allergens) : '') }}" 
                                   placeholder="e.g., Peanuts, Dairy, Gluten">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="preparation_time" class="form-label">Preparation Time (minutes)</label>
                                <input type="number" class="form-control" id="preparation_time" name="preparation_time" 
                                       value="{{ old('preparation_time', $menuItem->preparation_time) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="calories" class="form-label">Calories</label>
                                <input type="number" class="form-control" id="calories" name="calories" 
                                       value="{{ old('calories', $menuItem->calories) }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="image" class="form-label">Item Image</label>
                            @if($menuItem->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($menuItem->image) }}" alt="{{ $menuItem->name }}" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dietary Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_vegetarian" 
                                       name="is_vegetarian" value="1" {{ old('is_vegetarian', $menuItem->is_vegetarian) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_vegetarian">Vegetarian</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_vegan" 
                                       name="is_vegan" value="1" {{ old('is_vegan', $menuItem->is_vegan) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_vegan">Vegan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_gluten_free" 
                                       name="is_gluten_free" value="1" {{ old('is_gluten_free', $menuItem->is_gluten_free) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_gluten_free">Gluten Free</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_dairy_free" 
                                       name="is_dairy_free" value="1" {{ old('is_dairy_free', $menuItem->is_dairy_free) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_dairy_free">Dairy Free</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_spicy" 
                                       name="is_spicy" value="1" {{ old('is_spicy', $menuItem->is_spicy) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_spicy">Spicy</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_available" 
                                       name="is_available" value="1" {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">Available for Order</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" 
                                       name="is_featured" value="1" {{ old('is_featured', $menuItem->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Featured Item</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('staff.menu.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Menu Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
