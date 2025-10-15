@extends('layouts.guest')

@section('title', 'My Food Orders - ValesBeach Resort')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Food Orders</h1>
            <p class="text-gray-600 mt-2">Track your current and past food orders</p>
        </div>
        
        <a href="{{ route('guest.food-orders.menu') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Browse Menu</span>
        </a>
    </div>

    @if($orders->count() > 0)
    <!-- Orders List -->
    <div class="space-y-6">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Order Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-3">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Order #{{ $order->order_number }}
                            </h2>
                            
                            <!-- Status Badge -->
                            <span class="px-3 py-1 text-sm rounded-full font-medium
                                @switch($order->status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('confirmed') bg-blue-100 text-blue-800 @break
                                    @case('preparing') bg-orange-100 text-orange-800 @break
                                    @case('ready') bg-green-100 text-green-800 @break
                                    @case('delivered') bg-green-100 text-green-800 @break
                                    @case('cancelled') bg-red-100 text-red-800 @break
                                @endswitch">
                                {{ str_replace('_', ' ', ucfirst($order->status)) }}
                            </span>
                        </div>
                        
                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <p><strong>Placed:</strong> {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
                            <p><strong>Delivery:</strong> {{ str_replace('_', ' ', ucfirst($order->delivery_type)) }}
                                @if($order->delivery_location) - {{ $order->delivery_location }} @endif
                            </p>
                            @if($order->requested_delivery_time)
                            <p><strong>Requested Time:</strong> {{ $order->requested_delivery_time->format('M j \a\t g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">
                            ${{ $order->formatted_total_amount }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $order->orderItems->sum('quantity') }} item{{ $order->orderItems->sum('quantity') !== 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                    @foreach($order->orderItems->take(6) as $item)
                    <div class="flex items-center space-x-3">
                        @if($item->menuItem->image)
                        <img src="{{ asset('storage/' . $item->menuItem->image) }}" 
                             alt="{{ $item->menuItem->name }}" 
                             class="w-12 h-12 object-cover rounded">
                        @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                        
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $item->menuItem->name }}</p>
                            <p class="text-sm text-gray-600">
                                Qty: {{ $item->quantity }} × ${{ $item->formatted_unit_price }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($order->orderItems->count() > 6)
                <p class="text-sm text-gray-600 mb-4">
                    ... and {{ $order->orderItems->count() - 6 }} more item{{ $order->orderItems->count() - 6 !== 1 ? 's' : '' }}
                </p>
                @endif
                
                @if($order->special_instructions)
                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong>Special Instructions:</strong> {{ $order->special_instructions }}
                    </p>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <div class="flex space-x-3">
                        <a href="{{ route('guest.food-orders.show', $order) }}" 
                           class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                            View Details
                        </a>
                        
                        @if($order->status === 'delivered')
                        <button class="text-green-600 hover:text-green-800 font-semibold text-sm"
                                onclick="showReorderModal('{{ $order->id }}')">
                            Reorder
                        </button>
                        @endif
                    </div>
                    
                    @if($order->status === 'pending')
                    <form action="{{ route('guest.food-orders.cancel', $order) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to cancel this order?')" 
                          class="inline">
                        @csrf
                        <button type="submit" 
                                class="text-red-600 hover:text-red-800 font-semibold text-sm">
                            Cancel Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
    
    @else
    <!-- Empty State -->
    <div class="text-center py-12">
        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h2 class="text-2xl font-bold text-gray-500 mb-2">No orders yet</h2>
        <p class="text-gray-400 mb-6">Start by exploring our delicious menu</p>
        <a href="{{ route('guest.food-orders.menu') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Browse Menu</span>
        </a>
    </div>
    @endif
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Reorder Items</h3>
                <p class="text-gray-600 mb-6">This will add all items from this order to your current cart.</p>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeReorderModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    <button onclick="confirmReorder()" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentOrderId = null;

function showReorderModal(orderId) {
    currentOrderId = orderId;
    document.getElementById('reorderModal').classList.remove('hidden');
}

function closeReorderModal() {
    currentOrderId = null;
    document.getElementById('reorderModal').classList.add('hidden');
}

function confirmReorder() {
    if (currentOrderId) {
        // In a real implementation, you would make an AJAX call to reorder
        alert('Reorder functionality would be implemented here to add items to cart');
        closeReorderModal();
    }
}

// Close modal when clicking outside
document.getElementById('reorderModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReorderModal();
    }
});
</script>
@endsection
