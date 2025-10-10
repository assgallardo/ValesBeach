@extends('layouts.app')

@section('title', 'Food Menu - ValesBeach Resort')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Food Menu</h1>
            <p class="text-gray-600 mt-2">Delicious meals delivered to your room or pickup at our restaurant</p>
        </div>
        
        <!-- Cart Button -->
        <div class="relative">
            <a href="{{ route('guest.food-orders.cart') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5 6m0 0h9"/>
                </svg>
                <span>Cart</span>
                <span id="cart-count" class="bg-red-500 text-white rounded-full px-2 py-1 text-xs min-w-[20px] text-center">0</span>
            </a>
        </div>
    </div>

    <!-- Featured Items -->
    @if($featuredItems->count() > 0)
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Items</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredItems as $item)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                @endif
                
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-lg text-gray-900">{{ $item->name }}</h3>
                        <span class="text-lg font-bold text-blue-600">${{ $item->formatted_price }}</span>
                    </div>
                    
                    @if($item->description)
                    <p class="text-gray-600 text-sm mb-3">{{ $item->description }}</p>
                    @endif
                    
                    <!-- Dietary Badges -->
                    <div class="flex flex-wrap gap-1 mb-3">
                        @foreach($item->dietary_badges as $badge)
                        <span class="px-2 py-1 text-xs rounded-full {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                        @endforeach
                    </div>
                    
                    <!-- Add to Cart Form -->
                    <form class="add-to-cart-form" data-item-id="{{ $item->id }}">
                        @csrf
                        <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                        <div class="flex items-center space-x-2 mb-3">
                            <label class="text-sm font-medium text-gray-700">Quantity:</label>
                            <select name="quantity" class="border border-gray-300 rounded px-3 py-1">
                                @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <textarea name="special_instructions" placeholder="Special instructions (optional)" 
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3" rows="2"></textarea>
                        
                        <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-semibold transition duration-200">
                            Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Menu Categories -->
    @foreach($categories as $category)
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 mr-3 text-blue-600">
                {!! $category->icon !!}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h2>
                @if($category->description)
                <p class="text-gray-600">{{ $category->description }}</p>
                @endif
            </div>
        </div>

        @if($category->menuItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($category->menuItems as $item)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-200">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $item->name }}</h3>
                        @if($item->description)
                        <p class="text-gray-600 text-sm mb-2">{{ $item->description }}</p>
                        @endif
                        
                        <!-- Dietary and Info Badges -->
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($item->dietary_badges as $badge)
                            <span class="px-2 py-1 text-xs rounded-full {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @endforeach
                            
                            @if($item->calories)
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                {{ $item->calories }} cal
                            </span>
                            @endif
                            
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                                {{ $item->preparation_time }} min
                            </span>
                        </div>
                        
                        @if($item->ingredients && count($item->ingredients) > 0)
                        <p class="text-xs text-gray-500 mb-2">
                            <strong>Ingredients:</strong> {{ implode(', ', $item->ingredients) }}
                        </p>
                        @endif
                        
                        @if($item->allergens && count($item->allergens) > 0)
                        <p class="text-xs text-red-600 mb-2">
                            <strong>Allergens:</strong> {{ implode(', ', $item->allergens) }}
                        </p>
                        @endif
                    </div>
                    
                    <div class="ml-4 text-right">
                        <span class="text-xl font-bold text-blue-600">${{ $item->formatted_price }}</span>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form class="add-to-cart-form" data-item-id="{{ $item->id }}">
                    @csrf
                    <input type="hidden" name="menu_item_id" value="{{ $item->id }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Qty:</label>
                            <select name="quantity" class="border border-gray-300 rounded px-2 py-1">
                                @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded font-semibold transition duration-200">
                            Add to Cart
                        </button>
                    </div>
                    
                    <textarea name="special_instructions" placeholder="Special instructions (optional)" 
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm mt-2" rows="1"></textarea>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 italic">No items available in this category.</p>
        @endif
    </div>
    @endforeach
</div>

<!-- Add to Cart JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();
    
    // Handle add to cart forms
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            
            button.textContent = 'Adding...';
            button.disabled = true;
            
            fetch('{{ route("guest.food-orders.cart.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    // Show success message
                    button.textContent = 'Added!';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-green-800');
                    
                    // Update cart count
                    document.getElementById('cart-count').textContent = data.cart_count;
                    
                    // Reset form
                    this.reset();
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-800');
                        button.classList.add('bg-green-600', 'hover:bg-green-700');
                        button.disabled = false;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add item to cart. Please try again.');
                button.textContent = originalText;
                button.disabled = false;
            });
        });
    });
});

function updateCartCount() {
    fetch('{{ route("guest.food-orders.cart.count") }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        })
        .catch(error => console.error('Error updating cart count:', error));
}
</script>
@endsection
