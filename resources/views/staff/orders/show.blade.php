@extends('layouts.staff')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-green-50 mb-2">Order Details</h1>
                <p class="text-gray-400">{{ $foodOrder->order_number }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('staff.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-600 border border-green-500 text-white px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Order Details & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Information Card -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            Order Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Order Number</p>
                                <p class="text-green-50 font-semibold text-lg">{{ $foodOrder->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Order Date</p>
                                <p class="text-green-50">{{ $foodOrder->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Customer Name</p>
                                <p class="text-green-50">{{ $foodOrder->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Customer Email</p>
                                <p class="text-green-50 break-all">{{ $foodOrder->customer_email }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Phone</p>
                                <p class="text-green-50">{{ $foodOrder->customer_phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 uppercase mb-1">Status</p>
                                @php
                                    $statusConfig = match($foodOrder->status) {
                                        'pending' => ['bg' => 'bg-yellow-600', 'text' => 'Pending'],
                                        'preparing' => ['bg' => 'bg-blue-600', 'text' => 'Preparing'],
                                        'ready' => ['bg' => 'bg-purple-600', 'text' => 'Ready for Pickup'],
                                        'completed' => ['bg' => 'bg-green-600', 'text' => 'Completed'],
                                        'cancelled' => ['bg' => 'bg-red-600', 'text' => 'Cancelled'],
                                        default => ['bg' => 'bg-gray-600', 'text' => ucfirst($foodOrder->status)]
                                    };
                                @endphp
                                <span class="inline-block px-3 py-1 {{ $statusConfig['bg'] }} text-white rounded-lg font-medium">
                                    {{ $statusConfig['text'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Delivery Information -->
                        <div class="mt-6 pt-6 border-t border-gray-700">
                            <h3 class="text-sm font-medium text-gray-300 uppercase mb-4 flex items-center">
                                <i class="fas fa-shipping-fast text-green-400 mr-2"></i>
                                Delivery Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Delivery Type</p>
                                    <p class="text-green-50 capitalize">{{ str_replace('_', ' ', $foodOrder->delivery_type ?? 'N/A') }}</p>
                                </div>
                                @if($foodOrder->delivery_location)
                                <div>
                                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Location</p>
                                    <p class="text-green-50">{{ $foodOrder->delivery_location }}</p>
                                </div>
                                @endif
                                @if($foodOrder->requested_delivery_time)
                                <div class="md:col-span-2">
                                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Requested Delivery Time</p>
                                    <p class="text-green-50">{{ \Carbon\Carbon::parse($foodOrder->requested_delivery_time)->format('M j, Y - g:i A') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($foodOrder->special_instructions)
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <p class="text-xs font-medium text-gray-400 uppercase mb-2">Special Instructions</p>
                                <div class="bg-yellow-900/30 border-l-4 border-yellow-500 p-4 rounded">
                                    <p class="text-yellow-100">{{ $foodOrder->special_instructions }}</p>
                                </div>
                            </div>
                        @endif

                        @if($foodOrder->staff_notes)
                            <div class="mt-6 pt-6 border-t border-gray-700">
                                <p class="text-xs font-medium text-gray-400 uppercase mb-2">Staff Notes</p>
                                <div class="bg-gray-700 border-l-4 border-gray-500 p-4 rounded">
                                    <p class="text-gray-200">{{ $foodOrder->staff_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-utensils mr-3"></i>
                            Order Items
                        </h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-750">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($foodOrder->orderItems as $item)
                                    <tr class="hover:bg-gray-750 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-green-50">{{ $item->menuItem->name ?? 'N/A' }}</div>
                                            @if($item->special_instructions)
                                                <div class="text-sm text-gray-400 mt-1">
                                                    <i class="fas fa-comment text-blue-400 mr-1"></i>
                                                    {{ $item->special_instructions }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block px-3 py-1 bg-gray-700 text-green-50 rounded-full font-medium">
                                                {{ $item->quantity }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-green-50">₱{{ number_format($item->price, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-semibold text-green-400">₱{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-750">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-lg font-bold text-green-50 uppercase">Total:</td>
                                    <td class="px-6 py-4 text-right text-xl font-bold text-green-400">₱{{ number_format($foodOrder->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Update Status & Timeline -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <!-- Update Status Card -->
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Update Status
                            </h2>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('staff.orders.update-status', $foodOrder) }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                                        New Status <span class="text-red-400">*</span>
                                    </label>
                                    <select id="status" name="status" required
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-green-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        <option value="pending" {{ $foodOrder->status === 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="preparing" {{ $foodOrder->status === 'preparing' ? 'selected' : '' }}>
                                            Preparing
                                        </option>
                                        <option value="ready" {{ $foodOrder->status === 'ready' ? 'selected' : '' }}>
                                            Ready for Pickup
                                        </option>
                                        <option value="completed" {{ $foodOrder->status === 'completed' ? 'selected' : '' }}>
                                            Completed
                                        </option>
                                        <option value="cancelled" {{ $foodOrder->status === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                                        Staff Notes <span class="text-gray-500">(Optional)</span>
                                    </label>
                                    <textarea id="notes" name="notes" rows="4"
                                              placeholder="Add notes about this order..."
                                              class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2.5 text-green-50 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('notes', $foodOrder->staff_notes) }}</textarea>
                                </div>

                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Timeline Card -->
                    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                            <h2 class="text-lg font-bold text-white flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                Order Timeline
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-check text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-50">Created</p>
                                    <p class="text-xs text-gray-400">{{ $foodOrder->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>

                            @if($foodOrder->confirmed_at)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-50">Confirmed</p>
                                        <p class="text-xs text-gray-400">{{ $foodOrder->confirmed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($foodOrder->prepared_at)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-fire text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-50">Started Preparing</p>
                                        <p class="text-xs text-gray-400">{{ $foodOrder->prepared_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($foodOrder->completed_at)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check-double text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-50">Completed</p>
                                        <p class="text-xs text-gray-400">{{ $foodOrder->completed_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($foodOrder->cancelled_at)
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-times text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-50">Cancelled</p>
                                        <p class="text-xs text-gray-400">{{ $foodOrder->cancelled_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
