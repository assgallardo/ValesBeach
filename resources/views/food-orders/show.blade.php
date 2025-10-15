@extends('layouts.guest')

@section('title', 'Order #' . $foodOrder->order_number . ' - ValesBeach Resort')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Order #{{ $foodOrder->order_number }}</h1>
            <p class="text-gray-600 mt-1">Placed on {{ $foodOrder->created_at->format('M j, Y \a\t g:i A') }}</p>
        </div>
        
        <div class="flex space-x-3">
            <a href="{{ route('guest.food-orders.orders') }}" 
               class="text-blue-600 hover:text-blue-800 font-semibold flex items-center space-x-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>All Orders</span>
            </a>
            
            @if($foodOrder->status === 'pending')
            <form action="{{ route('guest.food-orders.cancel', $foodOrder) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                    Cancel Order
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Status</h2>
                
                <div class="flex items-center space-x-4 mb-4">
                    <div class="flex-shrink-0">
                        @php
                        $statusConfig = [
                            'pending' => ['color' => 'yellow', 'icon' => 'clock'],
                            'confirmed' => ['color' => 'blue', 'icon' => 'check-circle'],
                            'preparing' => ['color' => 'orange', 'icon' => 'fire'],
                            'ready' => ['color' => 'green', 'icon' => 'bell'],
                            'delivered' => ['color' => 'green', 'icon' => 'check-circle'],
                            'cancelled' => ['color' => 'red', 'icon' => 'x-circle']
                        ];
                        $config = $statusConfig[$foodOrder->status] ?? $statusConfig['pending'];
                        @endphp
                        
                        <div class="w-12 h-12 bg-{{ $config['color'] }}-100 rounded-full flex items-center justify-center">
                            @if($config['icon'] === 'clock')
                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @elseif($config['icon'] === 'check-circle')
                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @elseif($config['icon'] === 'fire')
                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                            </svg>
                            @elseif($config['icon'] === 'bell')
                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-{{ $config['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $foodOrder->status) }}</h3>
                        <p class="text-gray-600 text-sm">
                            @switch($foodOrder->status)
                                @case('pending')
                                    We've received your order and it's being processed
                                    @break
                                @case('confirmed')
                                    Your order has been confirmed and will start preparation soon
                                    @break
                                @case('preparing')
                                    Our chefs are preparing your delicious meal
                                    @break
                                @case('ready')
                                    Your order is ready for {{ $foodOrder->delivery_type === 'room_service' ? 'delivery' : 'pickup' }}
                                    @break
                                @case('delivered')
                                    Your order has been delivered. Enjoy your meal!
                                    @break
                                @case('cancelled')
                                    This order has been cancelled
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>
                
                <!-- Timeline -->
                <div class="mt-6">
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-900 font-medium">Order Placed</span>
                            <span class="text-gray-500 ml-auto">{{ $foodOrder->created_at->format('g:i A') }}</span>
                        </div>
                        
                        @if($foodOrder->confirmed_at)
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-900 font-medium">Order Confirmed</span>
                            <span class="text-gray-500 ml-auto">{{ $foodOrder->confirmed_at->format('g:i A') }}</span>
                        </div>
                        @endif
                        
                        @if($foodOrder->prepared_at)
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-900 font-medium">Preparation Started</span>
                            <span class="text-gray-500 ml-auto">{{ $foodOrder->prepared_at->format('g:i A') }}</span>
                        </div>
                        @endif
                        
                        @if($foodOrder->delivered_at)
                        <div class="flex items-center text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-gray-900 font-medium">
                                {{ $foodOrder->delivery_type === 'room_service' ? 'Delivered' : 'Ready for Pickup' }}
                            </span>
                            <span class="text-gray-500 ml-auto">{{ $foodOrder->delivered_at->format('g:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>
                
                <div class="space-y-4">
                    @foreach($foodOrder->orderItems as $item)
                    <div class="flex items-start space-x-4 py-4 border-b border-gray-200 last:border-b-0">
                        <!-- Item Image -->
                        <div class="flex-shrink-0">
                            @if($item->menuItem->image)
                            <img src="{{ asset('storage/' . $item->menuItem->image) }}" 
                                 alt="{{ $item->menuItem->name }}" 
                                 class="w-16 h-16 object-cover rounded">
                            @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Item Details -->
                        <div class="flex-1">
                            <h3 class="font-semibold text-lg text-gray-900">{{ $item->menuItem->name }}</h3>
                            @if($item->menuItem->description)
                            <p class="text-gray-600 text-sm mt-1">{{ $item->menuItem->description }}</p>
                            @endif
                            
                            <!-- Dietary Badges -->
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($item->menuItem->dietary_badges as $badge)
                                <span class="px-2 py-1 text-xs rounded-full {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                                @endforeach
                            </div>
                            
                            @if($item->special_instructions)
                            <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-sm text-yellow-800">
                                    <strong>Special Instructions:</strong> {{ $item->special_instructions }}
                                </p>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Quantity and Price -->
                        <div class="text-right">
                            <div class="text-sm text-gray-600 mb-1">
                                Qty: {{ $item->quantity }}
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                ${{ $item->formatted_unit_price }} each
                            </div>
                            <div class="text-lg font-bold text-gray-900">
                                ${{ $item->formatted_total_price }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <!-- Delivery Information -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Delivery Details</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>Type:</strong> {{ str_replace('_', ' ', ucfirst($foodOrder->delivery_type)) }}</p>
                        @if($foodOrder->delivery_location)
                        <p><strong>Location:</strong> {{ $foodOrder->delivery_location }}</p>
                        @endif
                        @if($foodOrder->requested_delivery_time)
                        <p><strong>Requested Time:</strong> {{ $foodOrder->requested_delivery_time->format('M j, g:i A') }}</p>
                        @endif
                    </div>
                </div>
                
                @if($foodOrder->special_instructions)
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Special Instructions</h3>
                    <p class="text-sm text-gray-600">{{ $foodOrder->special_instructions }}</p>
                </div>
                @endif
                
                <!-- Pricing -->
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">${{ $foodOrder->formatted_subtotal }}</span>
                    </div>
                    @if($foodOrder->delivery_fee > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span class="font-semibold">${{ $foodOrder->formatted_delivery_fee }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-semibold">${{ $foodOrder->formatted_tax_amount }}</span>
                    </div>
                    <div class="border-t border-gray-200 pt-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span>${{ $foodOrder->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Status -->
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Payment Status</span>
                        <span class="px-3 py-1 text-sm rounded-full
                            @switch($foodOrder->payment_status)
                                @case('pending') bg-yellow-100 text-yellow-800 @break
                                @case('paid') bg-green-100 text-green-800 @break
                                @case('failed') bg-red-100 text-red-800 @break
                                @case('refunded') bg-gray-100 text-gray-800 @break
                            @endswitch">
                            {{ ucfirst($foodOrder->payment_status) }}
                        </span>
                    </div>
                </div>
                
                @if($foodOrder->booking)
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-2">Associated Booking</h3>
                    <p class="text-blue-800 text-sm">
                        <strong>Booking:</strong> {{ $foodOrder->booking->booking_number }}<br>
                        @if($foodOrder->booking->room)
                        <strong>Room:</strong> {{ $foodOrder->booking->room->room_number }}
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
