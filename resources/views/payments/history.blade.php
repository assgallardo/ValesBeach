@extends('layouts.guest')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">💳 Payment History</h1>
                    <p class="text-gray-400">View all your payments organized by category</p>
                </div>
            <div class="flex gap-3">
                @if($bookings->isNotEmpty() || $servicePayments->isNotEmpty() || $foodOrderPayments->isNotEmpty())
                <!-- Payment Method Selector -->
                <div class="relative inline-block">
                    <button type="button"
                            id="paymentMethodBtn"
                            onclick="togglePaymentMethodMenu()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-credit-card mr-2"></i>
                        <span id="paymentMethodText">Select Payment Method</span>
                        <i class="fas fa-chevron-down ml-2 text-sm"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="paymentMethodMenu" 
                         class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-lg shadow-xl border border-gray-700 hidden z-50">
                        <div class="py-2">
                            <a href="#" onclick="selectPaymentMethod('cash', 'Cash', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 w-5"></i>
                                <span>Cash</span>
                            </a>
                            <a href="#" onclick="selectPaymentMethod('card', 'Card', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-credit-card mr-2 w-5"></i>
                                <span>Card</span>
                            </a>
                            <a href="#" onclick="selectPaymentMethod('gcash', 'GCash', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-mobile-alt mr-2 w-5"></i>
                                <span>GCash</span>
                            </a>
                            <a href="#" onclick="selectPaymentMethod('bank_transfer', 'Bank Transfer', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-university mr-2 w-5"></i>
                                <span>Bank Transfer</span>
                            </a>
                            <a href="#" onclick="selectPaymentMethod('paymaya', 'PayMaya', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-wallet mr-2 w-5"></i>
                                <span>PayMaya</span>
                            </a>
                            <a href="#" onclick="selectPaymentMethod('online', 'Online', event)" 
                               class="block px-4 py-2 text-white hover:bg-gray-700 transition-colors flex items-center">
                                <i class="fas fa-globe mr-2 w-5"></i>
                                <span>Online</span>
                            </a>
                        </div>
                    </div>
                </div>
                <form action="{{ route('invoices.generate-combined') }}" method="POST" class="inline">
                    @csrf
                    @foreach($bookings as $booking)
                        <input type="hidden" name="bookings[]" value="{{ $booking->id }}">
                    @endforeach
                    @foreach($servicePayments as $payment)
                        <input type="hidden" name="services[]" value="{{ $payment->id }}">
                    @endforeach
                    @foreach($foodOrderPayments as $payment)
                        <input type="hidden" name="food_orders[]" value="{{ $payment->id }}">
                    @endforeach
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <i class="fas fa-file-invoice mr-2"></i>Generate Invoice
                    </button>
                </form>
                @endif
                <a href="{{ route('guest.dashboard') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
            </div>
        </div>

        @if($bookings->isEmpty() && $servicePayments->isEmpty() && $foodOrderPayments->isEmpty())
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-xl p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-700 rounded-full mb-4">
                    <i class="fas fa-receipt text-4xl text-gray-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">No Payment History</h3>
                <p class="text-gray-400 mb-6">You haven't made any payments yet.</p>
                <a href="{{ route('guest.rooms.browse') }}"
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-bed mr-2"></i>Browse Rooms
                </a>
            </div>
        @else
            <div class="space-y-6">
                <!-- Payment Method Badge -->
                @if($generalPaymentMethod)
                <div class="flex items-center justify-between bg-gray-800 rounded-lg border border-gray-700 p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-credit-card text-indigo-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Payment Method</p>
                            <p class="text-xs text-gray-500">All payments processed via</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-semibold shadow-lg">
                            <i class="fas fa-check-circle mr-2 text-xs"></i>
                            {{ ucfirst(str_replace('_', ' ', $generalPaymentMethod)) }}
                        </span>
                    </div>
                </div>
                @endif

                <!-- Booking Payments Card -->
                @if($bookings->isNotEmpty())
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl overflow-hidden border border-gray-700 shadow-xl">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-bed text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Booking Payments</h2>
                                    <p class="text-blue-100 text-sm">{{ $bookings->count() }} booking(s)</p>
                                </div>
                            </div>
                            @php
                                $totalBookingPayments = $bookings->sum('amount_paid');
                            @endphp
                            <div class="text-right">
                                <p class="text-blue-100 text-sm">Total Paid</p>
                                <p class="text-3xl font-bold text-white">₱{{ number_format($totalBookingPayments, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment List -->
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($bookings as $booking)
                            <div class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700 transition-all duration-200 border border-gray-600/50">
                                <!-- Booking Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex-1">
                                        <div class="text-sm text-gray-300 mb-1">
                                            <span class="font-medium text-white">{{ $booking->room->name }}</span>
                                            <span class="text-gray-500 mx-1">•</span>
                                            <span class="font-mono text-xs text-blue-400">{{ $booking->booking_reference }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}
                                            <span class="mx-1">•</span>
                                            Total: <span class="text-blue-400 font-semibold">₱{{ number_format($booking->total_price, 2) }}</span>
                                            <span class="mx-1">•</span>
                                            Balance: <span class="{{ $booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400' }} font-semibold">₱{{ number_format($booking->remaining_balance, 2) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        @php
                                            $statusConfig = [
                                                'completed' => ['bg' => 'bg-purple-600', 'text' => 'Completed'],
                                                'confirmed' => ['bg' => 'bg-blue-600', 'text' => 'Confirmed'],
                                                'pending' => ['bg' => 'bg-gray-600', 'text' => 'Pending'],
                                                'cancelled' => ['bg' => 'bg-red-600', 'text' => 'Cancelled']
                                            ];
                                            $config = $statusConfig[$booking->status] ?? ['bg' => 'bg-gray-600', 'text' => ucfirst($booking->status)];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $config['bg'] }} text-white">
                                            {{ $config['text'] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Individual Payment Transactions -->
                                @if($booking->payments && $booking->payments->count() > 0)
                                <div class="space-y-2 pl-4 border-l-2 border-blue-600/30">
                                    @foreach($booking->payments as $payment)
                                    <div class="flex items-center justify-between bg-gray-800/50 rounded p-2">
                                        <div class="flex items-center gap-3 flex-1">
                                            <span class="text-lg font-bold text-blue-400">₱{{ number_format($payment->amount, 2) }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                                @if($payment->status === 'completed') bg-green-500 text-white
                                                @elseif($payment->status === 'confirmed') bg-blue-500 text-white
                                                @elseif($payment->status === 'pending') bg-yellow-500 text-gray-900
                                                @elseif($payment->status === 'overdue') bg-orange-500 text-white
                                                @elseif($payment->status === 'processing') bg-indigo-500 text-white
                                                @elseif($payment->status === 'failed') bg-red-600 text-white
                                                @elseif($payment->status === 'refunded') bg-red-500 text-white
                                                @elseif($payment->status === 'cancelled') bg-gray-600 text-white
                                                @else bg-gray-500 text-white
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $payment->payment_reference }}</span>
                                            <span class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <a href="{{ route('guest.bookings.show', $booking) }}" 
                                           class="inline-flex items-center justify-center px-2 py-1 bg-blue-600/20 text-blue-400 rounded hover:bg-blue-600/30 transition-colors text-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-2 text-gray-500 text-sm italic">
                                    No payments recorded yet
                                </div>
                                @endif

                                <!-- View Button -->
                                <div class="mt-3 flex justify-end">
                                    <a href="{{ route('guest.bookings.show', $booking) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
                                        <i class="fas fa-eye mr-1.5"></i>View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Service Payments Card -->
                @if($servicePayments->isNotEmpty())
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl overflow-hidden border border-gray-700 shadow-xl">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Service Payments</h2>
                                    <p class="text-green-100 text-sm">{{ $servicePayments->count() }} service request(s)</p>
                                </div>
                            </div>
                            @php
                                $totalServicePayments = $servicePayments->where('status', 'completed')->sum('amount');
                            @endphp
                            <div class="text-right">
                                <p class="text-green-100 text-sm">Total Paid</p>
                                <p class="text-3xl font-bold text-white">₱{{ number_format($totalServicePayments, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment List -->
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($servicePayments as $payment)
                            <div class="bg-gray-700/50 rounded-lg p-3 hover:bg-gray-700 transition-all duration-200 border border-gray-600/50">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div class="flex-1">
                                        <!-- Price and Status Badges -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xl font-bold text-green-400">₱{{ number_format($payment->amount, 2) }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                                @if($payment->status === 'completed') bg-green-500 text-white
                                                @elseif($payment->status === 'confirmed') bg-blue-500 text-white
                                                @elseif($payment->status === 'pending') bg-yellow-500 text-gray-900
                                                @elseif($payment->status === 'overdue') bg-orange-500 text-white
                                                @elseif($payment->status === 'processing') bg-indigo-500 text-white
                                                @elseif($payment->status === 'failed') bg-red-600 text-white
                                                @elseif($payment->status === 'refunded') bg-red-500 text-white
                                                @elseif($payment->status === 'cancelled') bg-gray-600 text-white
                                                @else bg-gray-500 text-white
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Compact Info -->
                                        <div class="text-sm text-gray-300 mb-2">
                                            <span class="font-medium text-white">{{ $payment->serviceRequest->service->name ?? 'Service Request' }}</span>
                                            <span class="text-gray-500 mx-1">•</span>
                                            <span class="font-mono text-xs text-green-400">{{ $payment->payment_reference }}</span>
                                        </div>
                                        
                                        <!-- Compact Payment Info -->
                                        <div class="flex items-center gap-4 text-xs">
                                            <div>
                                                <span class="text-gray-400">Date:</span>
                                                <span class="text-gray-200 ml-1">{{ $payment->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        @if($payment->serviceRequest)
                                        <a href="{{ route('guest.service-requests.show', $payment->serviceRequest->id) }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                            <i class="fas fa-eye mr-1.5"></i>View
                                        </a>
                                        @else
                                        <a href="{{ route('guest.services.history') }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm">
                                            <i class="fas fa-list mr-1.5"></i>History
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Food Order Payments Card -->
                @if($foodOrderPayments->isNotEmpty())
                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl overflow-hidden border border-gray-700 shadow-xl">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-utensils text-2xl text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Food Order Payments</h2>
                                    <p class="text-orange-100 text-sm">{{ $foodOrderPayments->count() }} order(s)</p>
                                </div>
                            </div>
                            @php
                                $totalFoodPayments = $foodOrderPayments->where('status', 'completed')->sum('amount');
                            @endphp
                            <div class="text-right">
                                <p class="text-orange-100 text-sm">Total Paid</p>
                                <p class="text-3xl font-bold text-white">₱{{ number_format($totalFoodPayments, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment List -->
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($foodOrderPayments as $payment)
                            <div class="bg-gray-700/50 rounded-lg p-3 hover:bg-gray-700 transition-all duration-200 border border-gray-600/50">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                                    <div class="flex-1">
                                        <!-- Price and Status Badges -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xl font-bold text-orange-400">₱{{ number_format($payment->amount, 2) }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                                @if($payment->status === 'completed') bg-green-500 text-white
                                                @elseif($payment->status === 'confirmed') bg-blue-500 text-white
                                                @elseif($payment->status === 'pending') bg-yellow-500 text-gray-900
                                                @elseif($payment->status === 'overdue') bg-orange-500 text-white
                                                @elseif($payment->status === 'processing') bg-indigo-500 text-white
                                                @elseif($payment->status === 'failed') bg-red-600 text-white
                                                @elseif($payment->status === 'refunded') bg-red-500 text-white
                                                @elseif($payment->status === 'cancelled') bg-gray-600 text-white
                                                @else bg-gray-500 text-white
                                                @endif">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                            @if($payment->foodOrder)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $payment->foodOrder->status === 'pending' ? 'bg-yellow-600 text-white' : 
                                                   ($payment->foodOrder->status === 'preparing' ? 'bg-purple-600 text-white' :
                                                    ($payment->foodOrder->status === 'ready' ? 'bg-green-600 text-white' : 'bg-blue-600 text-white')) }}">
                                                {{ ucfirst($payment->foodOrder->status) }}
                                            </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Compact Info -->
                                        @if($payment->foodOrder)
                                        <div class="text-sm text-gray-300 mb-2">
                                            <span class="font-mono text-xs text-orange-400">{{ $payment->foodOrder->order_number }}</span>
                                            <span class="text-gray-500 mx-1">•</span>
                                            <span class="text-xs">{{ $payment->foodOrder->orderItems->count() }} items</span>
                                            @if($payment->foodOrder->delivery_type)
                                            <span class="text-gray-500 mx-1">•</span>
                                            <span class="text-xs">{{ ucfirst(str_replace('_', ' ', $payment->foodOrder->delivery_type)) }}</span>
                                            @endif
                                        </div>
                                        
                                        <!-- Items Preview -->
                                        <div class="text-xs text-gray-400 mb-2">
                                            @foreach($payment->foodOrder->orderItems->take(2) as $item)
                                                <span class="text-orange-400 font-semibold">{{ $item->quantity }}x</span> {{ $item->menuItem->name ?? 'Item' }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                            @if($payment->foodOrder->orderItems->count() > 2)
                                                <span class="italic">+{{ $payment->foodOrder->orderItems->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                        @endif
                                        
                                        <!-- Compact Payment Info -->
                                        <div class="flex items-center gap-4 text-xs">
                                            <div>
                                                <span class="text-gray-400">Date:</span>
                                                <span class="text-gray-200 ml-1">{{ $payment->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        @if($payment->foodOrder)
                                        <a href="{{ route('guest.food-orders.show', $payment->foodOrder) }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium text-sm">
                                            <i class="fas fa-eye mr-1.5"></i>View
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Summary Stats -->
            @php
                $totalPaid = $bookings->sum('amount_paid') + 
                             $servicePayments->where('status', 'completed')->sum('amount') + 
                             $foodOrderPayments->where('status', 'completed')->sum('amount');
                $totalTransactions = $bookings->sum(function($b) { return $b->payments->count(); }) + 
                                   $servicePayments->count() + 
                                   $foodOrderPayments->count();
            @endphp
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Total Amount Paid</p>
                            <p class="text-2xl font-bold text-green-400">₱{{ number_format($totalPaid, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Total Transactions</p>
                            <p class="text-2xl font-bold text-blue-400">{{ $totalTransactions }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-400 mb-1">Categories</p>
                            <p class="text-2xl font-bold text-green-400">
                                {{ ($bookings->isNotEmpty() ? 1 : 0) + ($servicePayments->isNotEmpty() ? 1 : 0) + ($foodOrderPayments->isNotEmpty() ? 1 : 0) }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-layer-group text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

<script>
let paymentMethodMenuOpen = false;

function togglePaymentMethodMenu() {
    const menu = document.getElementById('paymentMethodMenu');
    paymentMethodMenuOpen = !paymentMethodMenuOpen;
    
    if (paymentMethodMenuOpen) {
        menu.classList.remove('hidden');
    } else {
        menu.classList.add('hidden');
    }
}

function selectPaymentMethod(method, methodName, event) {
    event.preventDefault();
    
    // Close the menu
    document.getElementById('paymentMethodMenu').classList.add('hidden');
    paymentMethodMenuOpen = false;
    
    // Update button text
    document.getElementById('paymentMethodText').textContent = methodName;
    
    // Show confirmation
    if (!confirm(`Update ALL your payments to "${methodName}"?`)) {
        // Reset button text if cancelled
        document.getElementById('paymentMethodText').textContent = 'Select Payment Method';
        return;
    }
    
    // Show loading state
    const button = document.getElementById('paymentMethodBtn');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';

    const url = '{{ route("payments.bulkUpdateMethod") }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    if (!csrfToken || !csrfToken.content) {
        alert('Error: CSRF token not found. Please refresh the page and try again.');
        button.disabled = false;
        button.innerHTML = originalText;
        document.getElementById('paymentMethodText').textContent = 'Select Payment Method';
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('payment_method', method);
    
    // Make the request
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        // First try to get response as text to see what we're dealing with
        return response.text().then(text => {
            console.log('Response status:', response.status);
            console.log('Response text:', text);
            
            try {
                const data = JSON.parse(text);
                
                if (!response.ok) {
                    const errorMsg = data.message || data.error || `Server error: ${response.status}`;
                    throw new Error(errorMsg);
                }
                
                return data;
            } catch (parseError) {
                if (!response.ok) {
                    throw new Error(`Server error (${response.status}): ${text.substring(0, 100)}`);
                }
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        console.log('Success response:', data);
        
        if (data.success) {
            alert(data.message || `Payment method updated successfully for ${data.updated_count || 0} payment(s).`);
            location.reload();
        } else {
            const errorMsg = data.message || 'Failed to update payment methods';
            throw new Error(errorMsg);
        }
    })
    .catch(error => {
        console.error('Full error:', error);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        
        const errorMsg = error.message || 'An unexpected error occurred. Please try again.';
        alert('Error: ' + errorMsg);
        button.disabled = false;
        button.innerHTML = originalText;
        document.getElementById('paymentMethodText').textContent = 'Select Payment Method';
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const button = document.getElementById('paymentMethodBtn');
    const menu = document.getElementById('paymentMethodMenu');
    
    if (!button.contains(event.target) && !menu.contains(event.target)) {
        menu.classList.add('hidden');
        paymentMethodMenuOpen = false;
    }
});
</script>

@endsection
